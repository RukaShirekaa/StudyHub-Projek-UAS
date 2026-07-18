<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .ai-chat-container { display:flex; flex-direction:column; height: 75vh; border: none; background: transparent; }
    .ai-chat-header { padding: 1rem 0; border-bottom: 2px solid var(--border-color); display:flex; align-items:center; gap: 1rem; justify-content: space-between; flex-wrap: wrap; }
    .ai-chat-messages { flex:1; padding: 1.5rem 0; overflow-y:auto; display:flex; flex-direction:column; gap:1.5rem; }
    .ai-msg-bubble { max-width: 90%; line-height: 1.7; word-break: break-word; font-size: 0.95rem; animation: fadeInUp 0.3s ease forwards; }
    .ai-msg-user { background: var(--primary-gradient); color: white; align-self: flex-end; padding: 0.85rem 1.25rem; border-radius: var(--radius-lg) var(--radius-lg) var(--radius-sm) var(--radius-lg); box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2); }
    .ai-msg-ai { background: transparent; color: var(--text-main); align-self: flex-start; padding: 0; display:flex; gap: 0.85rem; max-width: 95%; }
    .ai-avatar { width: 34px; height: 34px; border-radius: var(--radius-full); background: var(--primary-gradient); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25); }
    .ai-msg-ai .content { flex: 1; background: var(--surface); border: 1px solid var(--border-color); padding: 0.85rem 1.25rem; border-radius: var(--radius-sm) var(--radius-lg) var(--radius-lg) var(--radius-lg); }
    .ai-msg-ai .content p { margin-bottom: 0.75rem; }
    .ai-msg-ai .content p:last-child { margin-bottom: 0; }
    .ai-msg-ai .content ul, .ai-msg-ai .content ol { margin-left: 1.5rem; margin-bottom: 0.75rem; }
    .ai-msg-ai .content code { background: rgba(99, 102, 241, 0.08); padding: 0.15rem 0.4rem; border-radius: var(--radius-sm); font-size: 0.875rem; }
    .ai-msg-ai .content pre { background: var(--bg-color); padding: 1rem; border-radius: var(--radius-md); overflow-x: auto; margin: 0.5rem 0; }
    .ai-msg-ai .content pre code { background: transparent; padding: 0; }
    .ai-chat-input { padding: 1rem 0 0 0; border-top: none; background: transparent; display:flex; gap: 0.5rem; position: relative; }
    .ai-chat-input input { flex:1; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: var(--radius-full); outline:none; font-size: 0.95rem; transition: all var(--transition-normal); background: var(--surface) !important; }
    .ai-chat-input input:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 3px var(--primary-glow) !important; }
    .ai-chat-input button { width: 44px; height: 44px; border: none; border-radius: var(--radius-full); background: var(--primary-gradient); color: white; display:flex; align-items:center; justify-content:center; cursor:pointer; transition: all var(--transition-normal); flex-shrink: 0; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25); }
    .ai-chat-input button:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35); }
    .ai-chat-input button:disabled { opacity: 0.5; cursor: not-allowed; }

    /* Typing Indicator */
    .typing-indicator { display: inline-flex; align-items: center; gap: 4px; padding: 5px 0; }
    .typing-indicator span { width: 6px; height: 6px; background-color: currentColor; border-radius: 50%; opacity: 0.6; animation: typingBlink 1.4s infinite ease-in-out both; }
    .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
    @keyframes typingBlink { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

    @media (max-width: 768px) {
        .ai-chat-container { height: 65vh; }
        .ai-msg-bubble { max-width: 95%; }
        .ai-msg-ai { max-width: 100%; }
    }
</style>

<div class="greeting-card" style="background: var(--primary-gradient); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
    <h2 style="margin:0; color: white; display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-robot"></i> AI Study Assistant</h2>
    <p style="color: rgba(255,255,255,0.8); margin-top: 0.25rem; margin-bottom: 1.25rem; font-size: 0.95rem;">Pilih materi kuliah untuk berdiskusi dengan AI yang sudah membaca file PDF tersebut.</p>
    
    <form action="" method="GET" style="display:flex; gap:0.75rem; align-items:center; flex-wrap: wrap;">
        <select name="material_id" required style="flex:1; min-width: 200px; padding:0.85rem 1rem; border:none; background: rgba(255,255,255,0.9); border-radius:var(--radius-md); color: #1e293b; font-weight: 500;">
            <option value="">-- Pilih Materi --</option>
            <?php foreach($materials as $m): ?>
                <option value="<?= $m['id'] ?>" <?= (isset($material_id) && $material_id == $m['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['course_name']) ?> - <?= htmlspecialchars($m['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 0.85rem 1.5rem;"><i class="fa-solid fa-comments"></i> Mulai Chat</button>
    </form>
</div>

<?php if (isset($material_id) && $material_id): ?>
<div class="ai-chat-container">
    <div class="ai-chat-header">
        <div style="display:flex; align-items:center; gap:0.85rem; flex:1; min-width: 200px;">
            <div class="ai-avatar" style="width: 42px; height: 42px; font-size: 1.1rem;">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div>
                <h4 style="margin:0; font-size: 1rem;">StudyHub AI</h4>
                <span style="font-size:0.75rem; color:var(--text-muted); display: flex; align-items: center; gap: 0.3rem;">
                    <span style="width: 6px; height: 6px; background: var(--accent-emerald); border-radius: 50%; display: inline-block;"></span>
                    <span style="white-space: nowrap;">Online — membahas materi</span>
                </span>
            </div>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap: wrap; justify-content: flex-end;">
            <a href="javascript:void(0)" class="btn btn-danger" style="font-size:0.8rem; padding:0.4rem 0.85rem;" onclick="confirmDelete('<?= BASE_URL ?>/assistant/clear?material_id=<?= $material_id ?>', 'Yakin ingin menghapus seluruh percakapan ini?')">
                <i class="fa-solid fa-trash"></i> Bersihkan
            </a>
            <a href="<?= BASE_URL ?>/assistant" class="btn btn-outline" style="font-size:0.8rem; padding:0.4rem 0.85rem;">
                <i class="fa-solid fa-xmark"></i> Tutup
            </a>
        </div>
    </div>
    <div class="ai-chat-messages" id="chatBox">
        <div class="ai-msg-bubble ai-msg-ai">
            <div class="ai-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="content">Halo! 👋 Saya adalah AI Assistant. Saya sudah siap membantu Anda memahami materi ini. Ada yang ingin ditanyakan?</div>
        </div>
        <?php foreach($chatHistory as $chat): ?>
            <?php if($chat['role'] === 'user'): ?>
                <div class="ai-msg-bubble ai-msg-user">
                    <?= htmlspecialchars($chat['message']) ?>
                </div>
            <?php else: ?>
                <div class="ai-msg-bubble ai-msg-ai">
                    <div class="ai-avatar"><i class="fa-solid fa-robot"></i></div>
                    <div class="content markdown-content"><?= htmlspecialchars($chat['message']) ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <form class="ai-chat-input" id="assistantChatForm" onsubmit="sendAssistantMessage(event)">
        <input type="hidden" id="material_id" value="<?= $material_id ?>">
        <input type="text" id="chatInput" placeholder="Tanya sesuatu atau minta buatkan rangkuman..." required autocomplete="off">
        <button type="submit" id="sendBtn"><i class="fa-solid fa-paper-plane"></i></button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Function to parse markdown and add buttons to a specific element
    function processAiMessage(el) {
        let rawText = el.textContent || el.innerText;
        let hasPdf = false;
        let hasDoc = false;
        
        if (rawText.includes('[DOWNLOAD:PDF]')) {
            hasPdf = true;
            rawText = rawText.replace(/\[DOWNLOAD:PDF\]/g, '');
        }
        if (rawText.includes('[DOWNLOAD:DOC]')) {
            hasDoc = true;
            rawText = rawText.replace(/\[DOWNLOAD:DOC\]/g, '');
        }
        
        el.innerHTML = marked.parse(rawText.trim());
        
        if (hasPdf || hasDoc) {
            let btnContainer = document.createElement('div');
            btnContainer.style.marginTop = '1rem';
            btnContainer.style.paddingTop = '1rem';
            btnContainer.style.borderTop = '1px solid var(--border-color)';
            btnContainer.style.display = 'flex';
            btnContainer.style.gap = '0.5rem';
            
            if (hasPdf) {
                let btn = document.createElement('button');
                btn.className = 'btn btn-outline';
                btn.style.padding = '0.4rem 0.85rem';
                btn.style.fontSize = '0.8rem';
                btn.innerHTML = '<i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i> Download PDF';
                btn.onclick = () => exportToPDF(el, 'Rangkuman_AI.pdf');
                btnContainer.appendChild(btn);
            }
            if (hasDoc) {
                let btn = document.createElement('button');
                btn.className = 'btn btn-outline';
                btn.style.padding = '0.4rem 0.85rem';
                btn.style.fontSize = '0.8rem';
                btn.innerHTML = '<i class="fa-solid fa-file-word" style="color: #3b82f6;"></i> Download Word';
                btn.onclick = () => exportToWord(el, 'Rangkuman_AI.doc');
                btnContainer.appendChild(btn);
            }
            el.appendChild(btnContainer);
        }
    }

    // Format initial markdown
    document.querySelectorAll('.markdown-content').forEach(el => processAiMessage(el));

    // Handle AJAX chat submission
    function sendAssistantMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const btn = document.getElementById('sendBtn');
        const materialId = document.getElementById('material_id').value;
        const message = input.value.trim();
        
        if (!message) return;
        
        input.value = '';
        input.disabled = true;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
        
        const chatBox = document.getElementById('chatBox');
        
        // Optimistically add user message
        const userBubble = document.createElement('div');
        userBubble.className = 'ai-msg-bubble ai-msg-user';
        userBubble.textContent = message;
        chatBox.appendChild(userBubble);
        
        // Add typing indicator
        const typingBubble = document.createElement('div');
        typingBubble.className = 'ai-msg-bubble ai-msg-ai';
        typingBubble.id = 'aiTypingIndicator';
        typingBubble.innerHTML = `
            <div class="ai-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="content">
                <div class="typing-indicator"><span></span><span></span><span></span></div>
            </div>
        `;
        chatBox.appendChild(typingBubble);
        chatBox.scrollTop = chatBox.scrollHeight;
        
        const formData = new FormData();
        formData.append('material_id', materialId);
        formData.append('message', message);
        
        fetch('<?= BASE_URL ?>/assistant/chat', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        }).then(res => res.json())
          .then(data => {
              input.disabled = false;
              btn.disabled = false;
              btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
              input.focus();
              
              const typingInd = document.getElementById('aiTypingIndicator');
              if (typingInd) typingInd.remove();
              
              if (data.success && data.ai_message) {
                  const aiBubble = document.createElement('div');
                  aiBubble.className = 'ai-msg-bubble ai-msg-ai';
                  
                  const avatar = document.createElement('div');
                  avatar.className = 'ai-avatar';
                  avatar.innerHTML = '<i class="fa-solid fa-robot"></i>';
                  
                  const content = document.createElement('div');
                  content.className = 'content markdown-content';
                  content.textContent = data.ai_message;
                  
                  aiBubble.appendChild(avatar);
                  aiBubble.appendChild(content);
                  chatBox.appendChild(aiBubble);
                  
                  processAiMessage(content);
                  chatBox.scrollTop = chatBox.scrollHeight;
              }
          }).catch(err => {
              input.disabled = false;
              btn.disabled = false;
              btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
              const typingInd = document.getElementById('aiTypingIndicator');
              if (typingInd) typingInd.remove();
          });
    }

    function exportToPDF(contentElement, filename) {
        let clone = contentElement.cloneNode(true);
        if (clone.lastElementChild && clone.lastElementChild.tagName === 'DIV' && clone.lastElementChild.style.borderTop) {
            clone.removeChild(clone.lastElementChild);
        }
        
        // Convert to string with inline styles for safe rendering in html2pdf's internal iframe
        let htmlString = `
            <div style="background-color: #ffffff; color: #000000; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; padding: 20px;">
                <style>
                    * { color: #000000 !important; }
                    pre { background: #f4f4f4 !important; padding: 10px; border-radius: 5px; white-space: pre-wrap; }
                    code { background: #f4f4f4 !important; padding: 2px 4px; border-radius: 3px; font-family: monospace; }
                    ul, ol { padding-left: 20px; margin-bottom: 15px; }
                    p { margin-bottom: 10px; }
                    h1, h2, h3 { margin-top: 20px; margin-bottom: 10px; }
                </style>
                ${clone.innerHTML}
            </div>
        `;
        
        var opt = {
            margin:       0.5,
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        
        html2pdf().set(opt).from(htmlString).save();
    }
    
    function exportToWord(contentElement, filename) {
        let clone = contentElement.cloneNode(true);
        if (clone.lastElementChild && clone.lastElementChild.tagName === 'DIV' && clone.lastElementChild.style.borderTop) {
            clone.removeChild(clone.lastElementChild);
        }
        
        var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body style='font-family: Arial, sans-serif; line-height: 1.5;'>";
        var postHtml = "</body></html>";
        var html = preHtml + clone.innerHTML + postHtml;

        var blob = new Blob(['\ufeff', html], {
            type: 'application/msword'
        });
        
        var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
        var downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        
        if(navigator.msSaveOrOpenBlob){
            navigator.msSaveOrOpenBlob(blob, filename);
        }else{
            downloadLink.href = url;
            downloadLink.download = filename;
            downloadLink.click();
        }
        document.body.removeChild(downloadLink);
    }

    // Scroll chat to bottom
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>
<?php else: ?>
    <?php if (!empty($recentChats)): ?>
    <div style="margin-top: 2rem;">
        <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-main); font-weight: 600;">Riwayat Diskusi Terakhir</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
            <?php foreach($recentChats as $chat): ?>
                <a href="<?= BASE_URL ?>/assistant?material_id=<?= $chat['id'] ?>" class="card interactive-course-card" style="text-decoration: none; padding: 1.25rem; display: block; border: 1px solid var(--border-color); border-radius: var(--radius-lg); transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                        <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(99, 102, 241, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($chat['title']) ?></h4>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($chat['course_name']) ?></span>
                        </div>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-align: right;">
                        <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($chat['last_chat'])) ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 4rem 1rem; color: var(--text-muted); background: var(--surface); border-radius: var(--radius-lg); border: 1px dashed var(--border-color); margin-top: 2rem;">
        <i class="fa-solid fa-comments" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
        <p>Anda belum memiliki riwayat diskusi dengan AI.<br>Pilih materi di atas untuk mulai bertanya.</p>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
