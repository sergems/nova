<?php
// Nova Chat Admin Dashboard - AJAX Polling Version
$pageTitle = 'Chat Dashboard - Nova Debt Counselling';
$pageDescription = 'Admin dashboard for managing live chat conversations';

function renderSEO($page) {
    global $pageTitle, $pageDescription;
    echo "<title>$pageTitle</title>";
    echo "<meta name=\"description\" content=\"$pageDescription\">";
    echo '<meta property="og:title" content="' . $pageTitle . '">';
    echo '<meta property="og:description" content="' . $pageDescription . '">';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php renderSEO('chat-dashboard'); ?>
    <link rel="icon" type="image/png" href="/images/Nova_favi_1757765316803.png">
    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border);
        }
        
        .connection-status {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-online {
            background: rgba(200, 147, 61, 0.15);
            color: var(--nova-accent);
        }
        
        .status-offline {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .chat-dashboard {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            height: 600px;
        }
        
        .active-chats {
            background: var(--gray-50);
            border-radius: 8px;
            padding: 1rem;
            overflow-y: auto;
        }
        
        .chat-item {
            padding: 1rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }
        
        .chat-item:hover,
        .chat-item.active {
            border-color: var(--nova-primary);
            background: var(--nova-light-purple);
        }

        .chat-actions {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.25rem;
        }

        .chat-action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .delete-btn {
            background: #fee2e2;
            color: #991b1b;
        }

        .archive-btn {
            background: #f3f4f6;
            color: #4b5563;
        }

        .chat-action-btn:hover {
            transform: translateY(-1px);
        }
        
        .chat-conversation {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
        }
        
        .conversation-header {
            padding: 1rem;
            background: var(--gray-50);
            border-bottom: 1px solid var(--border);
            border-radius: 8px 8px 0 0;
        }
        
        .conversation-messages {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            padding: 0.75rem;
            border-radius: 8px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .user-message {
            background: var(--gray-100);
            margin-left: auto;
            text-align: right;
        }

        .admin-message {
            background: var(--nova-light-purple);
            margin-right: auto;
        }

        .message-content {
            margin-bottom: 0.25rem;
        }

        .message-time {
            font-size: 0.7rem;
            color: var(--gray-500);
        }
        
        .admin-response {
            display: flex;
            gap: 0.5rem;
            padding: 1rem;
            border-top: 1px solid var(--border);
        }
        
        .admin-input {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            outline: none;
        }
        
        .admin-input:focus {
            border-color: var(--nova-primary);
        }
        
        .send-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--nova-primary), var(--nova-accent));
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease;
        }
        
        .send-btn:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .admin-login {
            max-width: 400px;
            margin: 4rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .login-input {
            width: 100%;
            padding: 1rem;
            margin: 1rem 0;
            border: 2px solid var(--border);
            border-radius: 8px;
            outline: none;
        }
        
        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--nova-primary), var(--nova-accent));
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            text-align: center;
        }

        .view-toggle {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .view-btn {
            padding: 0.5rem 1rem;
            border: 2px solid var(--nova-primary);
            background: white;
            color: var(--nova-primary);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .view-btn.active {
            background: var(--nova-primary);
            color: white;
        }

        @media (max-width: 768px) {
            .chat-dashboard {
                grid-template-columns: 1fr;
                gap: 1rem;
                height: auto;
            }
            
            .active-chats {
                height: 200px;
            }
            
            .chat-conversation {
                height: 400px;
            }
        }
    </style>
</head>
<body style="background: var(--gray-100); min-height: 100vh;">
    <!-- Admin Login Modal -->
    <div id="admin-login" class="admin-login">
        <div style="margin-bottom: 2rem;">
            <img src="/images/Nova_logo_1757778806804.png" alt="Nova Logo" style="width: 60px; height: 60px;">
            <h2 style="margin: 1rem 0; color: var(--nova-primary);">Chat Dashboard</h2>
            <p style="color: var(--gray-600);">Enter admin credentials to access live chat management</p>
        </div>
        
        <form id="login-form">
            <input 
                type="text" 
                id="admin-username" 
                class="login-input" 
                placeholder="Username" 
                data-testid="admin-username-input"
                required
                autocomplete="username"
            >
            <input 
                type="password" 
                id="admin-password" 
                class="login-input" 
                placeholder="Password" 
                data-testid="admin-password-input"
                required
                autocomplete="current-password"
            >
            <button type="submit" class="login-btn" data-testid="admin-login-button">
                Access Dashboard
            </button>
            <div id="login-error" class="error-message" style="display: none;"></div>
        </form>
    </div>

    <!-- Dashboard (hidden initially) -->
    <div id="dashboard" class="dashboard-container" style="display: none;">
        <div class="dashboard-header">
            <div>
                <h1 style="margin: 0; color: var(--nova-primary);">Nova Chat Dashboard</h1>
                <p style="margin: 0; color: var(--gray-600);">Manage live chat conversations</p>
            </div>
            <div>
                <span id="connection-status" class="connection-status status-offline">
                    <span>🔴</span> Connecting...
                </span>
            </div>
        </div>

        <div class="chat-dashboard">
            <div class="active-chats">
                <div class="view-toggle">
                    <button id="active-chats-btn" class="view-btn active">Active</button>
                    <button id="archived-chats-btn" class="view-btn">Archived</button>
                </div>
                <h3 id="chat-section-title" style="margin-bottom: 1rem; color: var(--nova-primary);">Active Chats</h3>
                <div id="chat-list">
                    <div style="text-align: center; color: var(--gray-500); margin: 2rem 0;">
                        <p>Loading chats...</p>
                    </div>
                </div>
            </div>

            <div class="chat-conversation">
                <div class="conversation-header">
                    <h3 id="conversation-title" style="margin: 0; color: var(--nova-primary);">Select a chat to begin</h3>
                    <p id="conversation-status" style="margin: 0; color: var(--gray-600);">Choose an active chat from the sidebar to respond to customer inquiries</p>
                </div>

                <div id="conversation-messages" class="conversation-messages">
                    <div style="text-align: center; color: var(--gray-500); margin: 2rem;">
                        <p>💬 Welcome to Nova Chat Dashboard</p>
                        <p>Real-time chat management for Nova Debt Counselling</p>
                    </div>
                </div>

                <div id="chat-input-area" class="admin-response">
                    <input 
                        type="text" 
                        id="response-input" 
                        class="admin-input" 
                        placeholder="Type your response to the customer..." 
                        data-testid="response-input"
                        disabled
                    >
                    <button 
                        id="send-response" 
                        class="send-btn" 
                        data-testid="send-response"
                        disabled
                    >
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        class ChatDashboard {
            constructor() {
                this.isAuthenticated = false;
                this.adminClientId = null;
                this.currentChatId = null;
                this.activeChats = new Map();
                this.currentView = 'active';
                this.archivedChats = [];
                this.pollingInterval = null;
                this.lastMessageTime = '';
                this.pollingDelay = 2000; // Poll every 2 seconds
                this.init();
            }

            init() {
                this.bindLoginEvents();
            }

            bindLoginEvents() {
                const loginForm = document.getElementById('login-form');
                loginForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.authenticateAdmin();
                });
            }

            async authenticateAdmin() {
                const username = document.getElementById('admin-username').value.trim();
                const password = document.getElementById('admin-password').value.trim();
                
                if (!username || !password) {
                    this.showError('Please enter both username and password');
                    return;
                }

                // Show loading
                const loginBtn = document.querySelector('.login-btn');
                const originalText = loginBtn.textContent;
                loginBtn.textContent = 'Authenticating...';
                loginBtn.disabled = true;

                try {
                    const response = await fetch('/api/admin-auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            username: username,
                            password: password
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.isAuthenticated = true;
                        this.adminClientId = data.data.adminClientId;
                        this.showDashboard();
                        this.startPolling();
                    } else {
                        this.showError(data.message || 'Authentication failed');
                        loginBtn.textContent = originalText;
                        loginBtn.disabled = false;
                    }
                } catch (error) {
                    this.showError('Connection error. Please try again.');
                    loginBtn.textContent = originalText;
                    loginBtn.disabled = false;
                }
            }

            showDashboard() {
                document.getElementById('admin-login').style.display = 'none';
                document.getElementById('dashboard').style.display = 'block';
                this.bindDashboardEvents();
                this.bindViewToggleEvents();
                this.updateConnectionStatus('online');
            }

            bindViewToggleEvents() {
                const activeBtn = document.getElementById('active-chats-btn');
                const archivedBtn = document.getElementById('archived-chats-btn');

                activeBtn.addEventListener('click', () => {
                    this.switchToActiveView();
                });

                archivedBtn.addEventListener('click', () => {
                    this.switchToArchivedView();
                });
            }

            switchToActiveView() {
                this.currentView = 'active';
                document.getElementById('active-chats-btn').classList.add('active');
                document.getElementById('archived-chats-btn').classList.remove('active');
                document.getElementById('chat-section-title').textContent = 'Active Chats';
                document.getElementById('chat-input-area').style.display = 'flex';
                this.refreshChatList();
            }

            switchToArchivedView() {
                this.currentView = 'archived';
                document.getElementById('active-chats-btn').classList.remove('active');
                document.getElementById('archived-chats-btn').classList.add('active');
                document.getElementById('chat-section-title').textContent = 'Archived Chats';
                document.getElementById('chat-input-area').style.display = 'none';
                this.loadArchivedChats();
            }

            async loadArchivedChats() {
                // For now, show placeholder as archived chats aren't implemented in the PHP backend yet
                const chatList = document.getElementById('chat-list');
                chatList.innerHTML = `
                    <div style="text-align: center; color: var(--gray-500); margin: 2rem 0;">
                        <p>Archived chats</p>
                        <p style="font-size: 0.8rem;">Feature coming soon</p>
                    </div>
                `;
            }

            bindDashboardEvents() {
                const sendBtn = document.getElementById('send-response');
                const responseInput = document.getElementById('response-input');

                sendBtn.addEventListener('click', () => this.sendResponse());
                responseInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.sendResponse();
                    }
                });

                // Enable input and button
                responseInput.disabled = false;
                sendBtn.disabled = false;

                // Clean up on page unload
                window.addEventListener('beforeunload', () => {
                    this.stopPolling();
                });
            }

            startPolling() {
                // Start polling for messages
                this.pollingInterval = setInterval(() => {
                    this.pollForMessages();
                }, this.pollingDelay);

                // Initial poll
                this.pollForMessages();
            }

            stopPolling() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                    this.pollingInterval = null;
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
                            client_id: this.adminClientId,
                            last_message_time: this.lastMessageTime,
                            is_admin: true
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.handleNewMessages(data.data.messages || []);
                            this.updateOnlineStatus(data.data.onlineStatus);
                            this.updateConnectionStatus('online');
                        }
                    } else {
                        this.updateConnectionStatus('offline');
                    }
                } catch (error) {
                    this.updateConnectionStatus('offline');
                }
            }

            handleNewMessages(messages) {
                messages.forEach(message => {
                    if (!message.isFromAdmin) {
                        // User message - add to active chats
                        this.addOrUpdateChat(message);
                        
                        // Display in current conversation if selected
                        if (this.currentChatId === message.clientId) {
                            this.displayMessage(message);
                        }
                    }
                    
                    // Update last message time
                    if (message.timestamp) {
                        this.lastMessageTime = message.timestamp;
                    }
                });

                this.refreshChatList();
            }

            addOrUpdateChat(message) {
                const chatId = message.clientId;
                let chat = this.activeChats.get(chatId);
                
                if (!chat) {
                    chat = {
                        id: chatId,
                        userId: message.userId || chatId,
                        userName: message.userName || 'Anonymous',
                        messages: [],
                        lastActivity: new Date(),
                        lastMessage: ''
                    };
                    this.activeChats.set(chatId, chat);
                }
                
                // Add message to chat history
                chat.messages.push({
                    message: message.message,
                    timestamp: message.timestamp,
                    isFromAdmin: message.isFromAdmin || false
                });
                
                // Update chat info
                chat.lastMessage = message.message;
                chat.lastActivity = new Date(message.timestamp);
                if (message.userName) {
                    chat.userName = message.userName;
                }
            }

            refreshChatList() {
                if (this.currentView !== 'active') return;
                
                const chatList = document.getElementById('chat-list');
                
                if (this.activeChats.size === 0) {
                    chatList.innerHTML = `
                        <div style="text-align: center; color: var(--gray-500); margin: 2rem 0;">
                            <p>No active chats</p>
                            <p style="font-size: 0.8rem;">Visitors will appear here when they start chatting</p>
                        </div>
                    `;
                    return;
                }

                chatList.innerHTML = '';
                
                // Sort chats by last activity
                const sortedChats = Array.from(this.activeChats.entries())
                    .sort(([,a], [,b]) => b.lastActivity - a.lastActivity);
                
                sortedChats.forEach(([chatId, chat]) => {
                    const chatElement = document.createElement('div');
                    chatElement.className = 'chat-item';
                    if (this.currentChatId === chatId) {
                        chatElement.classList.add('active');
                    }
                    
                    const nameDiv = document.createElement('div');
                    nameDiv.style.fontWeight = '600';
                    nameDiv.style.marginBottom = '0.5rem';
                    nameDiv.textContent = `👤 ${chat.userName || 'Anonymous'}`;
                    
                    const messageDiv = document.createElement('div');
                    messageDiv.style.fontSize = '0.8rem';
                    messageDiv.style.color = 'var(--gray-600)';
                    const truncatedMessage = chat.lastMessage || 'Started conversation';
                    messageDiv.textContent = truncatedMessage.length > 30 ? 
                        truncatedMessage.substring(0, 30) + '...' : truncatedMessage;
                    
                    const timeDiv = document.createElement('div');
                    timeDiv.style.fontSize = '0.7rem';
                    timeDiv.style.color = 'var(--gray-500)';
                    timeDiv.style.marginTop = '0.25rem';
                    timeDiv.textContent = this.formatTime(chat.lastActivity);
                    
                    const contentWrapper = document.createElement('div');
                    contentWrapper.className = 'chat-content';
                    contentWrapper.appendChild(nameDiv);
                    contentWrapper.appendChild(messageDiv);
                    contentWrapper.appendChild(timeDiv);
                    
                    contentWrapper.addEventListener('click', () => {
                        this.selectChat(chatId);
                    });
                    
                    chatElement.appendChild(contentWrapper);
                    chatList.appendChild(chatElement);
                });
            }

            selectChat(chatId) {
                this.currentChatId = chatId;
                const chat = this.activeChats.get(chatId);
                
                document.getElementById('conversation-title').textContent = `Chat with ${chat.userName || 'Anonymous'}`;
                document.getElementById('conversation-status').textContent = `Active conversation • Last activity: ${this.formatTime(chat.lastActivity)}`;
                
                this.refreshChatList();
                this.loadChatMessages(chatId);
            }

            loadChatMessages(chatId) {
                const chat = this.activeChats.get(chatId);
                const messagesContainer = document.getElementById('conversation-messages');
                
                if (!chat || !chat.messages || chat.messages.length === 0) {
                    messagesContainer.innerHTML = `
                        <div style="text-align: center; color: var(--gray-500); margin: 2rem;">
                            <p>💬 Conversation with visitor</p>
                            <p style="font-size: 0.9rem;">No previous messages</p>
                        </div>
                    `;
                    return;
                }
                
                messagesContainer.innerHTML = '';
                
                // Display conversation history
                chat.messages.forEach(msg => {
                    this.displayMessage(msg);
                });
            }

            displayMessage(data) {
                const messagesContainer = document.getElementById('conversation-messages');
                
                // Remove welcome message if present
                if (messagesContainer.querySelector('div[style*="text-align: center"]')) {
                    messagesContainer.innerHTML = '';
                }
                
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${data.isFromAdmin ? 'admin-message' : 'user-message'}`;
                
                const messageContentDiv = document.createElement('div');
                messageContentDiv.className = 'message-content';
                
                const messageParagraph = document.createElement('p');
                messageParagraph.textContent = data.message;
                messageParagraph.style.margin = '0';
                messageContentDiv.appendChild(messageParagraph);
                
                const messageTimeDiv = document.createElement('div');
                messageTimeDiv.className = 'message-time';
                messageTimeDiv.textContent = new Date(data.timestamp).toLocaleTimeString();
                
                messageDiv.appendChild(messageContentDiv);
                messageDiv.appendChild(messageTimeDiv);
                
                messagesContainer.appendChild(messageDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            async sendResponse() {
                const input = document.getElementById('response-input');
                const message = input.value.trim();
                
                if (!message || !this.currentChatId) return;
                
                // Disable input temporarily
                input.disabled = true;
                document.getElementById('send-response').disabled = true;
                
                try {
                    const response = await fetch('/api/admin-response.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            targetClientId: this.currentChatId,
                            message: message
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            // Display message locally
                            this.displayMessage({
                                message: message,
                                timestamp: new Date().toISOString(),
                                isFromAdmin: true
                            });
                            
                            // Add to chat history
                            const chat = this.activeChats.get(this.currentChatId);
                            if (chat) {
                                chat.messages.push({
                                    message: message,
                                    timestamp: new Date().toISOString(),
                                    isFromAdmin: true
                                });
                            }
                            
                            input.value = '';
                        }
                    }
                } catch (error) {
                    console.error('Failed to send response:', error);
                }
                
                // Re-enable input
                input.disabled = false;
                document.getElementById('send-response').disabled = false;
                input.focus();
            }

            updateConnectionStatus(status) {
                const statusElement = document.getElementById('connection-status');
                if (status === 'online') {
                    statusElement.className = 'connection-status status-online';
                    statusElement.innerHTML = '<span>🟢</span> Connected';
                } else {
                    statusElement.className = 'connection-status status-offline';
                    statusElement.innerHTML = '<span>🔴</span> Offline';
                }
            }

            updateOnlineStatus(status) {
                // Update any online status indicators if needed
                // For now, this is handled by the connection status
            }

            formatTime(date) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            showError(message) {
                const errorDiv = document.getElementById('login-error');
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                
                setTimeout(() => {
                    errorDiv.style.display = 'none';
                }, 5000);
            }

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }

        // Initialize dashboard when page loads
        document.addEventListener('DOMContentLoaded', function() {
            window.chatDashboard = new ChatDashboard();
        });
    </script>
</body>
</html>