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

        .staff-identity { margin-top: .35rem; color: var(--gray-600); font-size: .9rem; }
        .assignment-controls { display: none; align-items: center; gap: .55rem; flex-wrap: wrap; margin-top: .75rem; }
        .assignment-controls select, .agent-form input, .agent-row input[type="text"], .agent-row input[type="password"] { padding: .6rem .7rem; border: 1px solid var(--border); border-radius: 7px; font: inherit; }
        .assignment-controls button, .agent-form button, .agent-row button { border: 0; border-radius: 7px; padding: .6rem .9rem; color: white; background: var(--nova-primary); font: inherit; font-weight: 600; cursor: pointer; }
        .assignment-status, .agent-feedback { color: var(--gray-600); font-size: .9rem; }
        .team-management { margin-top: 1.5rem; padding: 1.25rem; border: 1px solid var(--border); border-radius: 10px; background: var(--gray-50); }
        .team-management h2 { margin-top: 0; color: var(--nova-primary); }
        .agent-form { display: grid; grid-template-columns: repeat(3, minmax(140px, 1fr)) auto; gap: .6rem; margin: 1rem 0; }
        .agent-list { display: grid; gap: .65rem; }
        .agent-row { display: grid; grid-template-columns: minmax(130px, 1fr) minmax(130px, 1fr) minmax(150px, 1fr) auto auto auto; align-items: center; gap: .55rem; padding: .75rem; border: 1px solid var(--border); border-radius: 8px; background: white; }
        .agent-row .agent-login-name { color: var(--gray-600); font-size: .8rem; }
        .agent-row label { display: flex; align-items: center; gap: .35rem; white-space: nowrap; font-size: .85rem; }
        .agent-row .delete-agent { background: #b91c1c; }
        .chat-assignment-label { margin-top: .25rem; color: var(--nova-primary); font-size: .75rem; font-weight: 600; }
        @media (max-width: 900px) {
            .agent-form { grid-template-columns: 1fr 1fr; }
            .agent-row { grid-template-columns: 1fr 1fr; }
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

        .connection-status .status-dot { display: inline-block; width: 0.5rem; height: 0.5rem; margin-right: 0.45rem; border-radius: 50%; background: #f97316; vertical-align: middle; }
        .status-online .status-dot { background: #22c55e; }
        .status-offline .status-dot { background: #f97316; }
    </style>
</head>
<body style="background: var(--gray-100); min-height: 100vh;">
    <!-- Admin Login Modal -->
    <div id="admin-login" class="admin-login">
        <div style="margin-bottom: 2rem;">
            <img src="/images/Nova_logo_1757778806804.png" alt="Nova Logo" style="width: 60px; height: 60px;">
            <h2 style="margin: 1rem 0; color: var(--nova-primary);">Chat Dashboard</h2>
            <p style="color: var(--gray-600);">Enter your staff account details to access the live chat inbox</p>
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
                <p id="staff-identity" class="staff-identity"></p>
            </div>
            <div>
                <span id="connection-status" class="connection-status status-offline">
                    <span class="status-dot" aria-hidden="true"></span> Connecting...
                </span>
            </div>
        </div>

        <div class="chat-dashboard">
            <div class="active-chats">
                <h3 id="chat-section-title" style="margin-bottom: 1rem; color: var(--nova-primary);">Direct Chats</h3>
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
                    <div id="assignment-controls" class="assignment-controls">
                        <label for="assignment-select">Assign chat to</label>
                        <select id="assignment-select" aria-label="Assign chat to agent"></select>
                        <button type="button" id="save-assignment">Save assignment</button>
                        <span id="assignment-status" class="assignment-status" aria-live="polite"></span>
                    </div>
                </div>

                <div id="conversation-messages" class="conversation-messages">
                    <div style="text-align: center; color: var(--gray-500); margin: 2rem;">
                        <p>Welcome to the Nova direct-chat dashboard</p>
                        <p>Real-time chat management for Nova Debt Counselling</p>
                    </div>
                </div>

                <div id="chat-input-area" class="admin-response" style="display: none;">
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

        <section id="team-management" class="team-management" style="display: none;">
            <h2>Agent accounts</h2>
            <p style="color: var(--gray-600);">Create agents, reset their passwords, update display names, or deactivate/remove access. New agent passwords must be at least 12 characters.</p>
            <form id="create-agent-form" class="agent-form">
                <input id="new-agent-display" type="text" placeholder="Agent display name" maxlength="60" required>
                <input id="new-agent-username" type="text" placeholder="Username (lowercase)" minlength="3" maxlength="32" pattern="[a-z0-9._-]+" required>
                <input id="new-agent-password" type="password" placeholder="Temporary password (12+ chars)" minlength="12" maxlength="128" autocomplete="new-password" required>
                <button type="submit">Add agent</button>
            </form>
            <p id="agent-feedback" class="agent-feedback" aria-live="polite"></p>
            <div id="agent-list" class="agent-list"><p style="color: var(--gray-500);">Loading agent accounts…</p></div>
        </section>
    </div>

    <script>
        class ChatDashboard {
            constructor() {
                this.isAuthenticated = false;
                this.adminClientId = null;
                this.username = '';
                this.displayName = '';
                this.role = 'visitor';
                this.agents = [];
                this.activeAgents = [];
                this.assignments = {};
                this.currentChatId = null;
                this.activeChats = new Map();
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
                        this.username = data.data.username || username;
                        this.displayName = data.data.displayName || this.username;
                        this.role = data.data.role || 'admin';
                        this.showDashboard();
                        this.loadTeamData();
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
                document.getElementById('staff-identity').textContent = `Signed in as ${this.displayName} (${this.role})`;
                document.getElementById('team-management').style.display = this.role === 'admin' ? 'block' : 'none';
                this.bindDashboardEvents();
                this.updateConnectionStatus('online');
            }

            bindDashboardEvents() {
                const sendBtn = document.getElementById('send-response');
                const responseInput = document.getElementById('response-input');

                sendBtn.addEventListener('click', () => this.sendResponse());
                responseInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.sendResponse();
                });
                document.getElementById('save-assignment').addEventListener('click', () => this.saveAssignment());
                document.getElementById('create-agent-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.createAgent();
                });
                document.getElementById('agent-list').addEventListener('click', (e) => this.handleAgentAction(e));

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
                            if (data.data.assignments) {
                                this.assignments = data.data.assignments;
                                this.populateAssignmentSelect();
                            }
                            this.handleNewMessages(data.data.messages || []);
                            if (this.role === 'agent' && Array.isArray(data.data.assignedChatIds)) {
                                this.syncAssignedChats(data.data.assignedChatIds);
                            }
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
                    const chatId = message.isFromAdmin ? message.targetClientId : message.clientId;
                    if (chatId) {
                        const isNew = this.addOrUpdateChat(message, chatId);
                        if (isNew && this.currentChatId === chatId) this.displayMessage(message);
                    }

                    if (message.timestamp && (!this.lastMessageTime || Date.parse(message.timestamp) > Date.parse(this.lastMessageTime))) {
                        this.lastMessageTime = message.timestamp;
                    }
                });

                this.refreshChatList();
            }

            addOrUpdateChat(message, conversationId = null) {
                const chatId = conversationId || (message.isFromAdmin ? message.targetClientId : message.clientId);
                if (!chatId) return false;
                let chat = this.activeChats.get(chatId);

                if (!chat) {
                    chat = {
                        id: chatId,
                        userId: message.userId || chatId,
                        userName: message.isFromAdmin ? 'Visitor' : (message.userName || 'Visitor'),
                        messages: [],
                        lastActivity: new Date(),
                        lastMessage: ''
                    };
                    this.activeChats.set(chatId, chat);
                }

                const messageId = message.id || `${message.timestamp || ''}|${chatId}|${message.isFromAdmin ? 'admin' : 'visitor'}|${message.message || ''}`;
                if (chat.messages.some(existing => existing.id === messageId)) return false;
                chat.messages.push({
                    id: messageId,
                    message: message.message,
                    timestamp: message.timestamp,
                    isFromAdmin: message.isFromAdmin || false
                });

                chat.lastMessage = message.message;
                chat.lastActivity = message.timestamp ? new Date(message.timestamp) : new Date();
                if (!message.isFromAdmin && message.userName) chat.userName = message.userName;
                return true;
            }

            refreshChatList() {
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
                    nameDiv.textContent = chat.userName || 'Visitor';

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

                    const assignmentDiv = document.createElement('div');
                    assignmentDiv.className = 'chat-assignment-label';
                    const assignedUsername = this.assignments[chatId] || '';
                    const assignedAgent = this.agents.find(agent => agent.username === assignedUsername);
                    assignmentDiv.textContent = this.role === 'agent'
                        ? 'Assigned to you'
                        : (assignedUsername ? `Assigned to: ${assignedAgent ? assignedAgent.displayName : assignedUsername}` : 'Unassigned');

                    const contentWrapper = document.createElement('div');
                    contentWrapper.className = 'chat-content';
                    contentWrapper.appendChild(nameDiv);
                    contentWrapper.appendChild(messageDiv);
                    contentWrapper.appendChild(timeDiv);
                    contentWrapper.appendChild(assignmentDiv);

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
                document.getElementById('assignment-controls').style.display = this.role === 'admin' ? 'flex' : 'none';
                this.populateAssignmentSelect();
                document.getElementById('chat-input-area').style.display = 'flex';

                this.refreshChatList();
                this.loadChatMessages(chatId);
            }

            loadChatMessages(chatId) {
                const chat = this.activeChats.get(chatId);
                const messagesContainer = document.getElementById('conversation-messages');

                if (!chat || !chat.messages || chat.messages.length === 0) {
                    messagesContainer.innerHTML = `
                        <div style="text-align: center; color: var(--gray-500); margin: 2rem;">
                            <p>Conversation with visitor</p>
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
                            const savedMessage = data.data || {};
                            const adminMessage = {
                                id: savedMessage.messageId,
                                message: message,
                                timestamp: savedMessage.timestamp || new Date().toISOString(),
                                isFromAdmin: true,
                                targetClientId: this.currentChatId
                            };
                            if (this.addOrUpdateChat(adminMessage, this.currentChatId)) this.displayMessage(adminMessage);
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

            async loadTeamData() {
                if (this.role === 'admin') await this.loadAgentAccounts();
                await this.loadAssignments();
            }

            async loadAgentAccounts() {
                try {
                    const response = await fetch('/api/admin-users.php');
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Could not load agent accounts.');
                    this.agents = data.data.agents || [];
                    this.renderAgentAccounts();
                    this.refreshChatList();
                } catch (error) {
                    document.getElementById('agent-feedback').textContent = error.message;
                }
            }

            async loadAssignments() {
                try {
                    const response = await fetch('/api/admin-assignments.php');
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Could not load chat assignments.');
                    this.assignments = data.data.assignments || {};
                    this.activeAgents = data.data.agents || [];
                    this.populateAssignmentSelect();
                    this.refreshChatList();
                } catch (error) {
                    const status = document.getElementById('assignment-status');
                    if (status) status.textContent = error.message;
                }
            }

            populateAssignmentSelect() {
                const select = document.getElementById('assignment-select');
                if (!select) return;
                const selected = this.currentChatId ? (this.assignments[this.currentChatId] || '') : '';
                select.replaceChildren();
                const unassigned = document.createElement('option');
                unassigned.value = '';
                unassigned.textContent = 'Unassigned';
                select.appendChild(unassigned);
                this.activeAgents.forEach(agent => {
                    const option = document.createElement('option');
                    option.value = agent.username;
                    option.textContent = `${agent.displayName} (@${agent.username})`;
                    select.appendChild(option);
                });
                select.value = selected;
            }

            async saveAssignment() {
                if (this.role !== 'admin' || !this.currentChatId) return;
                const status = document.getElementById('assignment-status');
                status.textContent = 'Saving…';
                try {
                    const response = await fetch('/api/admin-assignments.php', {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ chatId: this.currentChatId, username: document.getElementById('assignment-select').value })
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Assignment failed.');
                    if (data.data.username) this.assignments[this.currentChatId] = data.data.username;
                    else delete this.assignments[this.currentChatId];
                    status.textContent = data.message || 'Assignment saved.';
                    this.refreshChatList();
                } catch (error) {
                    status.textContent = error.message;
                }
            }

            async createAgent() {
                const form = document.getElementById('create-agent-form');
                const feedback = document.getElementById('agent-feedback');
                const payload = {
                    action: 'create',
                    displayName: document.getElementById('new-agent-display').value.trim(),
                    username: document.getElementById('new-agent-username').value.trim(),
                    password: document.getElementById('new-agent-password').value
                };
                feedback.textContent = 'Creating account…';
                try {
                    const response = await fetch('/api/admin-users.php', {
                        method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Could not create agent.');
                    form.reset();
                    feedback.textContent = data.message || 'Agent account created.';
                    await this.loadAgentAccounts();
                    await this.loadAssignments();
                } catch (error) {
                    feedback.textContent = error.message;
                }
            }

            renderAgentAccounts() {
                const list = document.getElementById('agent-list');
                list.replaceChildren();
                if (!this.agents.length) {
                    const empty = document.createElement('p');
                    empty.textContent = 'No agent accounts yet.';
                    list.appendChild(empty);
                    return;
                }
                this.agents.forEach(agent => {
                    const row = document.createElement('div');
                    row.className = 'agent-row';
                    row.dataset.username = agent.username;
                    const name = document.createElement('div');
                    name.className = 'agent-login-name';
                    name.textContent = `@${agent.username}`;
                    const display = document.createElement('input');
                    display.type = 'text'; display.name = 'displayName'; display.maxLength = 60;
                    display.value = agent.displayName; display.setAttribute('aria-label', `Display name for ${agent.username}`);
                    const password = document.createElement('input');
                    password.type = 'password'; password.name = 'password'; password.minLength = 12; password.maxLength = 128;
                    password.placeholder = 'Reset password (12+ chars)'; password.autocomplete = 'new-password';
                    const activeLabel = document.createElement('label');
                    const active = document.createElement('input'); active.type = 'checkbox'; active.name = 'active'; active.checked = agent.active;
                    activeLabel.append(active, document.createTextNode('Active'));
                    const save = document.createElement('button'); save.type = 'button'; save.dataset.action = 'save'; save.textContent = 'Save';
                    const remove = document.createElement('button'); remove.type = 'button'; remove.dataset.action = 'delete'; remove.className = 'delete-agent'; remove.textContent = 'Delete';
                    row.append(name, display, password, activeLabel, save, remove);
                    list.appendChild(row);
                });
            }

            async handleAgentAction(event) {
                const button = event.target.closest('button[data-action]');
                if (!button) return;
                const row = button.closest('.agent-row');
                if (!row) return;
                const username = row.dataset.username;
                const feedback = document.getElementById('agent-feedback');
                if (button.dataset.action === 'delete') {
                    if (!window.confirm(`Delete the agent account @${username}? Assigned chats will become unassigned.`)) return;
                    await this.submitAgentChange({ action: 'delete', username }, feedback);
                    return;
                }
                const password = row.querySelector('input[name="password"]').value;
                await this.submitAgentChange({
                    action: 'update', username,
                    displayName: row.querySelector('input[name="displayName"]').value.trim(),
                    active: row.querySelector('input[name="active"]').checked,
                    password
                }, feedback);
            }

            async submitAgentChange(payload, feedback) {
                feedback.textContent = 'Saving…';
                try {
                    const response = await fetch('/api/admin-users.php', {
                        method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Could not update agent account.');
                    feedback.textContent = data.message || 'Agent account updated.';
                    await this.loadAgentAccounts();
                    await this.loadAssignments();
                } catch (error) {
                    feedback.textContent = error.message;
                }
            }

            syncAssignedChats(chatIds) {
                const permitted = new Set(chatIds);
                for (const chatId of this.activeChats.keys()) {
                    if (!permitted.has(chatId)) this.activeChats.delete(chatId);
                }
                const inputArea = document.getElementById('chat-input-area');
                if (this.currentChatId && !permitted.has(this.currentChatId)) {
                    this.currentChatId = null;
                    document.getElementById('conversation-title').textContent = 'Select an assigned chat';
                    document.getElementById('conversation-status').textContent = 'Your inbox contains chats assigned to your account.';
                    document.getElementById('conversation-messages').replaceChildren();
                    inputArea.style.display = 'none';
                } else {
                    inputArea.style.display = this.currentChatId ? 'flex' : 'none';
                }
                this.refreshChatList();
            }

            updateConnectionStatus(status) {
                const statusElement = document.getElementById('connection-status');
                if (status === 'online') {
                    statusElement.className = 'connection-status status-online';
                    statusElement.innerHTML = '<span class="status-dot" aria-hidden="true"></span> Connected';
                } else {
                    statusElement.className = 'connection-status status-offline';
                    statusElement.innerHTML = '<span class="status-dot" aria-hidden="true"></span> Offline';
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
