<?php
// Only show if user is logged in
if (!isset($_SESSION['user_id'])) return;

// Only show the floating widget on the dashboard (Beranda)
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('/\/dashboard\/?$/', $currentUri)) {
    return;
}
?>
<style>
    /* Chat Widget Styles */
    .chat-widget-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    .chat-widget-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--primary-gradient, linear-gradient(135deg, #6366f1, #8b5cf6));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.35);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        animation: pulseGlow 2.5s infinite;
    }

    @keyframes pulseGlow {
        0% { box-shadow: 0 4px 20px rgba(99, 102, 241, 0.35); }
        50% { box-shadow: 0 4px 30px rgba(99, 102, 241, 0.5), 0 0 0 8px rgba(99, 102, 241, 0.08); }
        100% { box-shadow: 0 4px 20px rgba(99, 102, 241, 0.35); }
    }

    .chat-widget-btn:hover {
        transform: scale(1.08);
        animation: none;
        box-shadow: 0 6px 25px rgba(99, 102, 241, 0.45);
    }

    .chat-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: linear-gradient(135deg, #ef4444, #f43f5e);
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        font-size: 11px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--bg-color, white);
        animation: scaleIn 0.3s ease;
    }

    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }
    
    .fab-wrapper {
        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        gap: 15px;
        position: absolute;
        bottom: 0;
        right: 0;
    }
    
    .fab-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        opacity: 0;
        pointer-events: none;
        transform: translateX(30px) scale(0.9);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .fab-actions.open {
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0) scale(1);
    }
    
    .fab-action-btn {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        height: 48px;
        width: 48px;
        border-radius: 24px;
        border: none;
        color: white;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 0;
        white-space: nowrap;
        text-decoration: none;
        outline: none;
    }
    
    .fab-action-btn i {
        font-size: 18px;
        min-width: 48px;
        text-align: center;
    }
    
    .fab-action-btn span {
        font-family: inherit;
        font-weight: 600;
        font-size: 14px;
        opacity: 0;
        max-width: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .fab-action-btn:hover {
        width: 125px;
        padding-right: 18px;
    }
    
    .fab-action-btn.amba:hover {
        width: 165px;
    }
    
    .fab-action-btn:hover span {
        opacity: 1;
        max-width: 120px;
    }
    
    .fab-action-btn.global {
        background: var(--primary-gradient, linear-gradient(135deg, #6366f1, #8b5cf6));
    }
    
    .fab-action-btn.amba {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .chat-widget-btn i {
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .chat-widget-btn.open i {
        transform: rotate(315deg) scale(1.1); /* Plus sign to Cross */
    }

    .chat-window {
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 360px;
        height: 480px;
        background: var(--surface, white);
        border-radius: 1rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border-color, #e2e8f0);
        display: none;
        flex-direction: column;
        overflow: hidden;
        transform-origin: bottom right;
        animation: chatWindowIn 0.3s ease forwards;
    }

    @keyframes chatWindowIn {
        from { opacity: 0; transform: scale(0.9) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .chat-window.open {
        display: flex;
    }

    .chat-header {
        background: var(--primary-gradient, linear-gradient(135deg, #6366f1, #8b5cf6));
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .amba-theme .chat-header {
        background: linear-gradient(135deg, #10b981, #059669); /* Greenish theme for Amba AI */
    }

    .amba-theme .chat-message.self {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .amba-theme .chat-input-area button {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .chat-header h4 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .close-chat {
        cursor: pointer;
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        font-size: 14px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .close-chat:hover { background: rgba(255, 255, 255, 0.25); }

    .chat-messages {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
        background: var(--bg-color, #f8fafc);
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .chat-message {
        max-width: 80%;
        padding: 0.65rem 1rem;
        border-radius: 1rem;
        font-size: 0.85rem;
        line-height: 1.45;
        position: relative;
        animation: fadeInUp 0.2s ease-out forwards;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-message .sender {
        font-size: 0.7rem;
        font-weight: 700;
        margin-bottom: 3px;
        color: var(--text-muted, #64748b);
    }

    .chat-message.self {
        align-self: flex-end;
        background: var(--primary-gradient, linear-gradient(135deg, #6366f1, #8b5cf6));
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .chat-message.self .sender { color: rgba(255, 255, 255, 0.7); }

    .chat-message.other {
        align-self: flex-start;
        background: var(--surface, white);
        color: var(--text-main, #334155);
        border: 1px solid var(--border-color, #e2e8f0);
        border-bottom-left-radius: 4px;
    }
    
    .chat-message.other.amba {
        background: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .chat-message .time {
        font-size: 0.65rem;
        opacity: 0.6;
        text-align: right;
        margin-top: 4px;
        display: block;
    }
    
    /* Typing Indicator */
    .typing-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 0;
    }
    
    .typing-indicator span {
        width: 6px;
        height: 6px;
        background-color: currentColor;
        border-radius: 50%;
        opacity: 0.6;
        animation: typingBlink 1.4s infinite ease-in-out both;
    }
    
    .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes typingBlink {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    .chat-message .sender a:hover {
        text-decoration: underline !important;
    }

    .chat-input-area {
        padding: 0.85rem;
        background: var(--surface, white);
        border-top: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        gap: 0.5rem;
    }

    .chat-input-area input {
        flex: 1;
        padding: 0.6rem 1rem;
        border: 1.5px solid var(--border-color, #e2e8f0);
        border-radius: 2rem;
        outline: none;
        font-family: inherit;
        font-size: 0.85rem;
        background: var(--bg-color, #f8fafc) !important;
        color: var(--text-main, #334155) !important;
        transition: border-color 0.3s;
    }

    .chat-input-area input:focus {
        border-color: var(--primary, #6366f1) !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
    }
    
    .amba-theme .chat-input-area input:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }

    .chat-input-area button {
        background: var(--primary-gradient, linear-gradient(135deg, #6366f1, #8b5cf6));
        color: white;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
        font-size: 0.85rem;
    }
    .chat-input-area button:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
    }
    
    .chat-input-area button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        .chat-window {
            width: calc(100vw - 40px);
            right: -10px;
            height: 420px;
        }
    }
</style>

<div class="chat-widget-container">
    <div class="fab-wrapper">
        <div class="chat-widget-btn" id="mainFabBtn" onclick="toggleChatMenu()">
            <i class="fa-solid fa-comment-dots"></i>
            <div class="chat-badge" id="chatBadge" style="display:none;">0</div>
        </div>
        <div class="fab-actions" id="chatMenu">
            <button class="fab-action-btn amba" onclick="openAmbaChat()">
                <i class="fa-solid fa-robot"></i>
                <span>Tanya Amba AI</span>
            </button>
            <button class="fab-action-btn global" onclick="openGlobalChat()">
                <i class="fa-solid fa-earth-americas"></i>
                <span>All Chat</span>
            </button>
        </div>
    </div>

    <!-- Global Chat Window -->
    <div class="chat-window" id="globalChatWindow">
        <div class="chat-header">
            <h4><i class="fa-solid fa-earth-americas"></i> All Chat</h4>
            <button class="close-chat" onclick="closeAll()"><i class="fa-solid fa-times"></i></button>
        </div>
        <div class="chat-messages" id="globalChatMessages">
            <!-- Messages will be loaded here -->
        </div>
        <form class="chat-input-area" id="globalChatForm" onsubmit="sendGlobalChatMessage(event)">
            <input type="text" id="globalChatInput" placeholder="Ketik pesan..." autocomplete="off">
            <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
    
    <!-- Amba AI Window -->
    <div class="chat-window amba-theme" id="ambaChatWindow">
        <div class="chat-header">
            <h4><i class="fa-solid fa-robot"></i> Amba AI</h4>
            <div style="display: flex; gap: 0.5rem;">
                <button class="close-chat" onclick="clearAmbaChat()" title="Hapus Riwayat" style="background: rgba(0,0,0,0.1);"><i class="fa-solid fa-trash"></i></button>
                <button class="close-chat" onclick="closeAll()"><i class="fa-solid fa-times"></i></button>
            </div>
        </div>
        <div class="chat-messages" id="ambaChatMessages">
            <!-- Messages will be loaded here -->
            <div class="chat-message other amba">
                <div class="sender">Amba AI</div>
                Halo! Saya Amba AI, asisten pintar Anda di StudyHub. Ada yang bisa saya bantu terkait fitur website ini?
            </div>
        </div>
        <form class="chat-input-area" id="ambaChatForm" onsubmit="sendAmbaChatMessage(event)">
            <input type="text" id="ambaChatInput" placeholder="Tanya Amba..." autocomplete="off">
            <button type="submit" id="ambaChatBtn"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
    

</div>

<script>
    let menuOpen = false;
    let globalChatOpen = false;
    let ambaChatOpen = false;
    
    let lastSeenMsgId = parseInt(localStorage.getItem('chat_last_seen') || 0);
    let lastFetchedId = 0;
    const currentUserId = <?= $_SESSION['user_id'] ?>;

    // Start polling immediately for Global Chat
    setInterval(loadGlobalMessages, 3000);
    loadGlobalMessages();

    function toggleChatMenu() {
        if (globalChatOpen || ambaChatOpen) {
            closeAll();
            return;
        }
        
        menuOpen = !menuOpen;
        const menuEl = document.getElementById('chatMenu');
        const mainBtn = document.getElementById('mainFabBtn');
        if (menuOpen) {
            menuEl.classList.add('open');
            mainBtn.classList.add('open');
            mainBtn.querySelector('i').className = 'fa-solid fa-plus'; // change to plus so rotate makes it an X
        } else {
            menuEl.classList.remove('open');
            mainBtn.classList.remove('open');
            mainBtn.querySelector('i').className = 'fa-solid fa-comment-dots';
        }
    }
    
    function closeAll() {
        document.getElementById('chatMenu').classList.remove('open');
        const mainBtn = document.getElementById('mainFabBtn');
        mainBtn.classList.remove('open');
        mainBtn.querySelector('i').className = 'fa-solid fa-comment-dots';
        document.getElementById('globalChatWindow').classList.remove('open');
        document.getElementById('ambaChatWindow').classList.remove('open');
        menuOpen = false;
        globalChatOpen = false;
        ambaChatOpen = false;
    }

    function openGlobalChat() {
        closeAll();
        globalChatOpen = true;
        document.getElementById('globalChatWindow').classList.add('open');
        document.getElementById('chatBadge').style.display = 'none';
        
        if (lastFetchedId > lastSeenMsgId) {
            lastSeenMsgId = lastFetchedId;
            localStorage.setItem('chat_last_seen', lastSeenMsgId);
        }
        
        setTimeout(() => {
            document.getElementById('globalChatInput').focus();
            const container = document.getElementById('globalChatMessages');
            container.scrollTop = container.scrollHeight;
        }, 100);
        
        lastFetchedId = 0; 
        loadGlobalMessages();
    }
    
    function openAmbaChat() {
        closeAll();
        ambaChatOpen = true;
        document.getElementById('ambaChatWindow').classList.add('open');
        
        loadAmbaMessages();
        
        setTimeout(() => {
            document.getElementById('ambaChatInput').focus();
            const container = document.getElementById('ambaChatMessages');
            container.scrollTop = container.scrollHeight;
        }, 100);
    }

    // --- GLOBAL CHAT LOGIC ---
    function loadGlobalMessages() {
        fetch('<?= BASE_URL ?>/chat/messages')
            .then(res => res.json())
            .then(data => {
                if(data.error) return;
                
                let unreadCount = 0;
                let maxId = 0;

                data.forEach(msg => {
                    const msgId = parseInt(msg.id);
                    if (msgId > maxId) maxId = msgId;
                    if (msgId > lastSeenMsgId && msg.user_id != currentUserId) {
                        unreadCount++;
                    }
                });

                if (maxId <= lastFetchedId && lastFetchedId !== 0) {
                    return;
                }

                if (globalChatOpen) {
                    const container = document.getElementById('globalChatMessages');
                    const isScrolledToBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 20;
                    
                    container.innerHTML = '';
                    data.forEach(msg => {
                        const isSelf = msg.user_id == currentUserId;
                        const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        const div = document.createElement('div');
                        div.className = 'chat-message ' + (isSelf ? 'self' : 'other');
                        div.innerHTML = `
                            ${!isSelf ? '<div class="sender"><a href="<?= BASE_URL ?>/profile/view?id='+msg.user_id+'" style="color: inherit; text-decoration: none;">'+escapeHtml(msg.user_name)+'</a></div>' : ''}
                            ${escapeHtml(msg.message)}
                            <span class="time">${time}</span>
                        `;
                        container.appendChild(div);
                    });
                    
                    lastSeenMsgId = maxId;
                    localStorage.setItem('chat_last_seen', lastSeenMsgId);
                    document.getElementById('chatBadge').style.display = 'none';
                    
                    if (isScrolledToBottom || lastFetchedId === 0) {
                        container.scrollTop = container.scrollHeight;
                    }
                } else if (unreadCount > 0) {
                    const badge = document.getElementById('chatBadge');
                    badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                    badge.style.display = 'flex';
                }

                lastFetchedId = maxId;
            });
    }

    function sendGlobalChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('globalChatInput');
        const message = input.value.trim();
        if(!message) return;
        
        input.value = '';
        
        const formData = new FormData();
        formData.append('message', message);
        
        fetch('<?= BASE_URL ?>/chat/send', {
            method: 'POST',
            body: formData
        }).then(() => {
            loadGlobalMessages();
            setTimeout(() => {
                const container = document.getElementById('globalChatMessages');
                container.scrollTop = container.scrollHeight;
            }, 100);
        });
    }
    
    // --- AMBA AI LOGIC ---
    function loadAmbaMessages() {
        fetch('<?= BASE_URL ?>/amba/messages')
            .then(res => res.json())
            .then(data => {
                if(data.error) return;
                
                const container = document.getElementById('ambaChatMessages');
                
                // Keep the welcome message, clear the rest
                container.innerHTML = `
                    <div class="chat-message other amba">
                        <div class="sender">Amba AI</div>
                        Halo! Saya Amba AI, asisten pintar Anda di StudyHub. Ada yang bisa saya bantu terkait fitur website ini?
                    </div>
                `;
                
                data.forEach(msg => {
                    const isSelf = msg.role === 'user';
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const div = document.createElement('div');
                    div.className = 'chat-message ' + (isSelf ? 'self' : 'other amba');
                    div.innerHTML = `
                        ${!isSelf ? '<div class="sender">Amba AI</div>' : ''}
                        ${escapeHtml(msg.message)}
                        <span class="time">${time}</span>
                    `;
                    container.appendChild(div);
                });
                
                container.scrollTop = container.scrollHeight;
            });
    }

    function sendAmbaChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('ambaChatInput');
        const btn = document.getElementById('ambaChatBtn');
        const message = input.value.trim();
        if(!message) return;
        
        input.value = '';
        input.disabled = true;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
        
        // Optimistically add user message
        const container = document.getElementById('ambaChatMessages');
        const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        const userDiv = document.createElement('div');
        userDiv.className = 'chat-message self';
        userDiv.innerHTML = `
            ${escapeHtml(message)}
            <span class="time">${time}</span>
        `;
        container.appendChild(userDiv);
        
        // Add typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message other amba';
        typingDiv.id = 'ambaTypingIndicator';
        typingDiv.innerHTML = `
            <div class="sender">Amba AI</div>
            <div class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
        `;
        container.appendChild(typingDiv);
        container.scrollTop = container.scrollHeight;
        
        const formData = new FormData();
        formData.append('message', message);
        
        fetch('<?= BASE_URL ?>/amba/send', {
            method: 'POST',
            body: formData
        }).then(res => res.json())
          .then(data => {
              input.disabled = false;
              btn.disabled = false;
              btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
              input.focus();
              
              const typingInd = document.getElementById('ambaTypingIndicator');
              if (typingInd) typingInd.remove();
              
              if (data.play_yt) {
                  window.open(data.play_yt, '_blank');
              }
              
              if (data.redirect) {
                  // Execute immediate redirect if AI commanded it
                  window.location.href = '<?= BASE_URL ?>' + data.redirect;
                  return;
              }
              
              loadAmbaMessages();
          }).catch(err => {
              input.disabled = false;
              btn.disabled = false;
              btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
              const typingInd = document.getElementById('ambaTypingIndicator');
              if (typingInd) typingInd.remove();
          });
    }
    
    function clearAmbaChat() {
        confirmDelete('<?= BASE_URL ?>/amba/clear', 'Hapus semua percakapan dengan Amba AI?');
    }
    
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }
</script>
