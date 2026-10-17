// Live Chat Widget for Nova Debt Counselling - AJAX Polling Version
class LiveChat {
    constructor() {
        this.isOpen = false;
        this.isConnected = false;
        this.pollingInterval = null;
        this.statusInterval = null;
        this.messages = [];
        this.lastMessageTime = '';
        this.seenMessages = new Set();
        this.pollingDelay = 2000; // Poll every 2 seconds
        this.statusDelay = 5000; // Check status every 5 seconds
        
        // Load or generate user identity
        this.loadUserIdentity();
        
        this.isOnline = false;
        this.init();
    }

    generateUserId() {
        return 'user_' + Math.random().toString(36).substr(2, 9);
    }

    loadUserIdentity() {
        // Try to load existing identity from localStorage
        const storedUserId = localStorage.getItem('nova_chat_user_id');
        const storedUserName = localStorage.getItem('nova_chat_user_name');
        
        if (storedUserId) {
            this.userId = storedUserId;
        } else {
            this.userId = this.generateUserId();
            localStorage.setItem('nova_chat_user_id', this.userId);
        }
        
        if (storedUserName) {
            this.userName = storedUserName;
            this.hasEnteredName = true;
        } else {
            this.userName = null;
            this.hasEnteredName = false;
        }
    }

    saveUserIdentity() {
        localStorage.setItem('nova_chat_user_id', this.userId);
        if (this.userName) {
            localStorage.setItem('nova_chat_user_name', this.userName);
        }
    }

    init() {
        this.createChatWidget();
        this.setupInitialState();
        this.startPolling();
        this.bindEvents();
    }

    setupInitialState() {
        if (this.hasEnteredName) {
            // User already has a name, skip name input
            document.getElementById('name-input-container').style.display = 'none';
            document.getElementById('chat-messages').style.display = 'flex';
            document.getElementById('chat-input-container').style.display = 'flex';
            
            // Update greeting with stored name
            const greetingName = document.getElementById('user-greeting-name');
            if (greetingName) {
                greetingName.textContent = this.userName;
            }
        }
    }

    createChatWidget() {
        // Create chat widget HTML
        const chatHTML = `
            <div id="live-chat-widget" class="chat-widget">
                <!-- Chat Button -->
                <div id="chat-toggle" class="chat-toggle" data-testid="chat-toggle">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="chat-notification" id="chat-notification">1</div>
                </div>

                <!-- Chat Window -->
                <div id="chat-window" class="chat-window" data-testid="chat-window">
                    <div class="chat-header">
                        <div class="chat-header-info">
                            <div class="chat-avatar">
                                <img src="/images/Nova_logo_1757778806804.png" alt="Nova Support" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            </div>
                            <div class="chat-info">
                                <h4 style="color: white;">Nova Support</h4>
                                <p id="chat-status" aria-live="polite">Checking team availability...</p>
                            </div>
                        </div>
                        <button id="chat-close" class="chat-close" data-testid="chat-close">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Name Input Form -->
                    <div class="name-input-container" id="name-input-container" data-testid="name-input-container">
                        <div class="name-input-form">
                            <div class="name-input-header">
                                <h4>Chat directly with Nova</h4>
                                <p>Enter your name to start a direct conversation with our team.</p>
                            </div>
                            <div class="name-input-wrapper">
                                <input type="text" id="user-name-input" class="user-name-input" placeholder="Your name..." data-testid="user-name-input" maxlength="50">
                                <button id="start-chat-btn" class="start-chat-btn" data-testid="start-chat-btn">Start Chat</button>
                            </div>
                        </div>
                    </div>

                    <div class="chat-messages" id="chat-messages" data-testid="chat-messages" style="display: none;">
                        <div class="message agent-message">
                            <div class="message-content">
                                <p>Hi <span id="user-greeting-name">there</span>. This is a direct chat with Nova Support. Send a message below and a team member will reply here.</p>
                            </div>
                            <div class="message-time">${this.getCurrentTime()}</div>
                        </div>
                    </div>

                    <div class="chat-input-container" id="chat-input-container" style="display: none;">
                        <div class="chat-input-wrapper">
                            <input type="text" id="chat-input" class="chat-input" placeholder="Write your message..." data-testid="chat-input" maxlength="1000" autocomplete="off">
                            <button id="chat-send" class="chat-send-btn" data-testid="chat-send">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <line x1="22" y1="2" x2="11" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <polygon points="22,2 15,22 11,13 2,9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chat-whatsapp-fallback">Prefer WhatsApp? <a href="https://wa.me/27718022762" target="_blank" rel="noopener noreferrer">Message Nova directly</a></div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', chatHTML);
    }

    startPolling() {
        // Start polling for messages
        this.pollingInterval = setInterval(() => {
            this.pollForMessages();
        }, this.pollingDelay);

        // Start status checking
        this.statusInterval = setInterval(() => {
            this.checkStatus();
        }, this.statusDelay);

        // Initial status check
        this.checkStatus();
    }

    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        if (this.statusInterval) {
            clearInterval(this.statusInterval);
            this.statusInterval = null;
        }
    }

    async pollForMessages() {
        try {
            const response = await fetch('/api/get-messages.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    client_id: this.userId,
                    last_message_time: this.lastMessageTime,
                    is_admin: false
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data.messages && data.data.messages.length > 0) {
                    this.handleNewMessages(data.data.messages);
                    
                    // Update online status
                    if (data.data.onlineStatus) {
                        this.updateOnlineStatus(data.data.onlineStatus);
                    }
                }
                
                if (!this.isConnected) {
                    this.isConnected = true;
                    this.updateStatus('Online - Live Chat');
                }
            } else {
                if (this.isConnected) {
                    this.isConnected = false;
                    this.updateStatus('Connection issue');
                }
            }
        } catch (error) {
            if (this.isConnected) {
                this.isConnected = false;
                this.updateStatus('Reconnecting...');
            }
        }
    }

    async checkStatus() {
        try {
            const response = await fetch('/api/chat-status.php', {
                method: 'GET'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data.onlineStatus) {
                    this.updateOnlineStatus(data.data.onlineStatus);
                }
                
                if (!this.isConnected) {
                    this.isConnected = true;
                }
            }
        } catch (error) {
            // Status check failed, continue polling messages
        }
    }

    handleNewMessages(messages) {
        messages.forEach(message => {
            const isAdminReply = Boolean(message.isFromAdmin) && message.targetClientId === this.userId;
            const isOwnMessage = !message.isFromAdmin && message.clientId === this.userId;
            const messageKey = message.id || `${message.timestamp || ''}|${message.clientId || ''}|${message.isFromAdmin ? 'admin' : 'visitor'}|${message.message || ''}`;

            if ((isAdminReply || isOwnMessage) && !this.seenMessages.has(messageKey)) {
                this.seenMessages.add(messageKey);
                this.addMessage(message.message, isAdminReply ? 'agent' : 'user', message.timestamp);
                if (isAdminReply) this.showNotificationIfClosed();
            }

            if (message.timestamp && (!this.lastMessageTime || Date.parse(message.timestamp) > Date.parse(this.lastMessageTime))) {
                this.lastMessageTime = message.timestamp;
            }
        });
    }

    updateOnlineStatus(status) {
        const supportAvailable = typeof status.supportAvailable === 'boolean'
            ? status.supportAvailable
            : (Number(status.adminCount || 0) > 0 || status.isOnline === true);
        const statusText = supportAvailable
            ? 'Team online — send a message'
            : 'Team offline — leave a message';
        this.updateStatus(statusText, supportAvailable);
        this.isOnline = status.isOnline;
    }

    bindEvents() {
        const chatToggle = document.getElementById('chat-toggle');
        const chatClose = document.getElementById('chat-close');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        const startChatBtn = document.getElementById('start-chat-btn');
        const userNameInput = document.getElementById('user-name-input');

        chatToggle.addEventListener('click', () => this.toggleChat());
        chatClose.addEventListener('click', () => this.closeChat());
        chatSend.addEventListener('click', () => this.sendMessage());
        startChatBtn.addEventListener('click', () => this.startChatWithName());
        
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });

        userNameInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.startChatWithName();
            }
        });

        // Hide notification when chat is opened
        const notification = document.getElementById('chat-notification');
        if (notification) {
            notification.style.display = 'none';
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            this.stopPolling();
        });
    }

    toggleChat() {
        const chatWindow = document.getElementById('chat-window');
        const notification = document.getElementById('chat-notification');
        
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
            if (notification) {
                notification.style.display = 'none';
            }
        }
    }

    openChat() {
        const chatWindow = document.getElementById('chat-window');
        chatWindow.classList.add('open');
        this.isOpen = true;
        
        // Focus on appropriate input based on state
        setTimeout(() => {
            if (!this.hasEnteredName) {
                document.getElementById('user-name-input').focus();
            } else {
                document.getElementById('chat-input').focus();
            }
        }, 300);
    }

    closeChat() {
        const chatWindow = document.getElementById('chat-window');
        chatWindow.classList.remove('open');
        this.isOpen = false;
    }

    startChatWithName() {
        const nameInput = document.getElementById('user-name-input');
        const name = nameInput.value.trim();
        
        if (!name) {
            nameInput.focus();
            return;
        }

        if (name.length < 2) {
            alert('Please enter at least 2 characters for your name.');
            nameInput.focus();
            return;
        }

        // Set user name and mark as entered
        this.userName = name;
        this.hasEnteredName = true;
        
        // Save to localStorage
        this.saveUserIdentity();

        // Update greeting with user's name
        const greetingName = document.getElementById('user-greeting-name');
        if (greetingName) {
            greetingName.textContent = name;
        }

        // Hide name input form and show chat interface
        document.getElementById('name-input-container').style.display = 'none';
        document.getElementById('chat-messages').style.display = 'flex';
        document.getElementById('chat-input-container').style.display = 'flex';

        // Focus on chat input
        setTimeout(() => {
            document.getElementById('chat-input').focus();
        }, 100);
    }

    async sendMessage() {
        const input = document.getElementById('chat-input');
        const sendButton = document.getElementById('chat-send');
        const message = input.value.trim();
        
        if (!message || !this.hasEnteredName) return;

        // Keep one request in flight so the visitor cannot accidentally duplicate a message.
        input.disabled = true;
        sendButton.disabled = true;
        
        try {
            const response = await fetch('/api/send-message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    clientId: this.userId,
                    message: message,
                    userName: this.userName || 'Anonymous',
                    isFromAdmin: false
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Message could not be sent. Please try again.');
            input.value = '';
            const savedMessage = data.data || {};
            const messageKey = savedMessage.messageId || `${savedMessage.timestamp || ''}|${this.userId}|visitor|${message}`;
            this.addMessage(message, 'user', savedMessage.timestamp, messageKey);
        } catch (error) {
            this.addMessage(`Message not sent: ${error.message || 'Connection error. Please try again.'}`, 'system');
        } finally {
            input.disabled = false;
            sendButton.disabled = false;
            input.focus();
        }
    }

    addMessage(content, type = 'user', timestamp = null, messageId = null) {
        const messagesContainer = document.getElementById('chat-messages');
        if (messageId && this.seenMessages.has(messageId)) return;
        if (messageId) this.seenMessages.add(messageId);
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}-message`;
        
        // Create message content securely using DOM methods (prevents XSS)
        const messageContentDiv = document.createElement('div');
        messageContentDiv.className = 'message-content';
        
        const messageParagraph = document.createElement('p');
        messageParagraph.textContent = content; // Use textContent to prevent XSS
        messageContentDiv.appendChild(messageParagraph);
        
        const messageTimeDiv = document.createElement('div');
        messageTimeDiv.className = 'message-time';
        messageTimeDiv.textContent = timestamp ? new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : this.getCurrentTime();
        
        messageDiv.appendChild(messageContentDiv);
        messageDiv.appendChild(messageTimeDiv);

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    showNotificationIfClosed() {
        // Show notification if chat is closed
        if (!this.isOpen) {
            const notification = document.getElementById('chat-notification');
            if (notification) {
                notification.style.display = 'block';
                notification.textContent = '1';
                
                // Add pulse animation
                notification.style.animation = 'pulse 1.5s infinite';
            }
        }
    }

    updateStatus(status, isAvailable = null) {
        const statusElement = document.getElementById('chat-status');
        if (statusElement) {
            statusElement.textContent = status;
            
            // Apply color styling based on availability
            if (isAvailable !== null) {
                if (isAvailable) {
                    statusElement.style.color = '#22c55e'; // Green for available
                } else {
                    statusElement.style.color = '#f97316'; // Orange for not available
                }
            }
        }
    }

    getCurrentTime() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
}

// Initialize chat when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.liveChat = new LiveChat();
});
