<?php 
/**
 * @var array $groupedNotes
 * @var array $courses
 * @var array $materials
 */
require_once __DIR__ . '/../layout/header.php'; 
?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Catatan Saya</h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">Kelola catatan belajar dan ringkasan AI Anda.</p>
    </div>
</div>

<div class="notes-layout">
    
    <!-- Left Column: Notes List -->
    <div class="card" style="padding: 0; display: flex; flex-direction: column; height: 100%; border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border-color); background: var(--surface);">
        <!-- Sidebar Header -->
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <div style="position: relative; margin-bottom: 1rem;">
                <form action="<?= BASE_URL ?>/notes" method="GET" style="margin: 0;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Cari catatan..." style="width: 100%; padding: 0.65rem 1rem 0.65rem 2.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-color); color: var(--text-main); font-size: 0.9rem;">
                </form>
            </div>
            <a href="<?= BASE_URL ?>/notes/create" class="btn btn-primary" style="width: 100%; justify-content: center; text-decoration: none;">
                <i class="fa-solid fa-plus"></i> Buat Catatan
            </a>
        </div>

        <!-- Notes List -->
        <div style="flex: 1; overflow-y: auto; padding: 1rem;">
            <?php if (empty($groupedNotes)): ?>
                <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
                    <i class="fa-solid fa-pen-to-square" style="font-size: 2rem; opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                    <p style="font-size: 0.9rem;">Belum ada catatan.</p>
                </div>
            <?php else: ?>
                <?php foreach($groupedNotes as $courseName => $notesGroup): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="margin: 0 0 0.75rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; padding-left: 0.5rem;">
                            <?= htmlspecialchars($courseName) ?>
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <?php foreach($notesGroup as $i => $n): ?>
                                <a href="<?= BASE_URL ?>/notes/edit?id=<?= $n['id'] ?>" style="display: block; padding: 1rem; border-radius: var(--radius-md); border: 1px solid transparent; text-decoration: none; transition: all 0.2s ease; background: transparent; border-color: var(--border-color);">
                                    <h5 style="margin: 0 0 0.25rem; font-size: 1rem; color: var(--text-main); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($n['title']) ?></h5>
                                    <p style="margin: 0 0 0.5rem; font-size: 0.85rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.4;">
                                        <?= htmlspecialchars(strip_tags($n['content'])) ?>
                                    </p>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        <?= date('d M Y, H:i', strtotime($n['updated_at'])) ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Editor -->
    <div class="card" style="padding: 0; display: flex; flex-direction: column; height: 100%; border-radius: var(--radius-lg); border: 1px solid var(--primary); background: var(--surface); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);">
        <!-- Editor Header -->
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" form="createNoteForm" name="title" placeholder="Judul Catatan Baru..." required style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); background: transparent; border: none; outline: none; width: 100%; margin-bottom: 0.5rem; padding: 0;">
                <select name="course_id" form="createNoteForm" required style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(99,102,241,0.1); color: var(--primary); padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 600; border: none; outline: none; cursor: pointer;">
                    <option value="" disabled selected>Pilih Mata Kuliah...</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('aiModal').style.display='flex'" style="color: var(--primary); border-color: var(--primary); font-size: 0.85rem; padding: 0.25rem 0.75rem; margin-left: 0.5rem; border-radius: var(--radius-full);">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Tulis dengan AI
                </button>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?= BASE_URL ?>/notes" class="btn btn-ghost" style="color: var(--text-muted);">Batal</a>
                <button type="submit" form="createNoteForm" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Buat
                </button>
            </div>
        </div>
        
        <!-- Editor Content -->
        <form id="createNoteForm" action="<?= BASE_URL ?>/notes/create" method="POST" style="flex: 1; display: flex; flex-direction: column; position: relative;" onsubmit="document.getElementById('hiddenContent').value = quill.root.innerHTML">
            <input type="hidden" name="content" id="hiddenContent">
            <div id="editor-container" style="flex: 1; border: none;"></div>
            
            <!-- AI Typing Indicator -->
            <div id="aiTypingIndicator" style="display: none; position: fixed; bottom: 30px; right: 30px; background: var(--primary); color: white; padding: 0.75rem 1.25rem; border-radius: 30px; font-size: 0.95rem; font-weight: 600; box-shadow: 0 10px 25px rgba(99,102,241,0.4); align-items: center; gap: 0.75rem; z-index: 9999;">
                <i class="fa-solid fa-robot fa-bounce"></i> AI sedang menulis...
                <button type="button" onclick="stopAiTyping()" style="background: rgba(255,255,255,0.2); border: none; color: white; border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; margin-left: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'" title="Hentikan">
                    <i class="fa-solid fa-stop" style="font-size: 0.75rem;"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal AI -->
<div id="aiModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 class="section-title no-line" style="margin: 0;"><i class="fa-solid fa-wand-magic-sparkles" style="color: var(--primary);"></i> AI Generator</h3>
            <button type="button" onclick="document.getElementById('aiModal').style.display='none'" class="btn-icon"><i class="fa-solid fa-times"></i></button>
        </div>
        <div class="form-group-modern">
            <label>Pilih Materi PDF</label>
            <select id="aiMaterialId" class="form-control" style="padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); width: 100%;">
                <option value="">-- Pilih Materi --</option>
                <?php foreach($materials as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['title']) ?> (<?= htmlspecialchars($m['course_name']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="button" onclick="generateNoteAi()" id="btnGenerateAi" class="btn btn-primary" style="width: 100%; margin-top: 1rem;"><i class="fa-solid fa-bolt"></i> Generate Sekarang</button>
        <div id="aiLoading" style="display: none; text-align: center; margin-top: 1rem; color: var(--text-muted);">
            <i class="fa-solid fa-circle-notch fa-spin"></i> AI sedang membaca materi dan menulis catatan...
        </div>
</div>
    </div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.ql-toolbar.ql-snow {
    border: none !important;
    border-bottom: 1px solid var(--border-color) !important;
    background: transparent !important;
    padding: 0.75rem 1.25rem !important;
    font-family: inherit;
}
.ql-container.ql-snow {
    border: none !important;
    font-family: inherit;
}
.ql-editor {
    color: var(--text-main) !important;
    font-size: 0.95rem !important;
    line-height: 1.6 !important;
    padding: 1.25rem !important;
}
.ql-snow .ql-stroke { stroke: var(--text-muted) !important; }
.ql-snow .ql-fill { fill: var(--text-muted) !important; }
.ql-snow .ql-picker { color: var(--text-muted) !important; }
.ql-snow .ql-active .ql-stroke { stroke: var(--primary) !important; }
.ql-snow .ql-active .ql-fill { fill: var(--primary) !important; }
</style>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Mulai mengetik catatan Anda di sini...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });

    // Fix AI Modal JS which was overwritten or placed incorrectly
    let aiTypingActive = false;
    let originalHtml = '';
    let currentHtml = '';

    function typeWriter(text, i, cb) {
        if (!aiTypingActive) {
            document.getElementById('aiTypingIndicator').style.display = 'none';
            if (cb) cb();
            return;
        }

        if (i < text.length) {
            let char = text.charAt(i);
            
            // Skip typing delay for HTML tags to prevent rendering glitches
            if (char === '<') {
                let closingIndex = text.indexOf('>', i);
                if (closingIndex !== -1) {
                    currentHtml += text.substring(i, closingIndex + 1);
                    i = closingIndex;
                } else {
                    currentHtml += char;
                }
            } else {
                currentHtml += char;
            }

            quill.root.innerHTML = originalHtml + currentHtml;
            
            // Auto scroll down
            const editor = document.querySelector('.ql-editor');
            if (editor) {
                editor.scrollTop = editor.scrollHeight;
            }

            setTimeout(function() {
                typeWriter(text, i + 1, cb)
            }, 10); // Kecepatan ngetik
        } else {
            aiTypingActive = false;
            document.getElementById('aiTypingIndicator').style.display = 'none';
            if (cb) cb();
        }
    }

    function stopAiTyping() {
        aiTypingActive = false;
    }

    function generateNoteAi() {
        const materialId = document.getElementById('aiMaterialId').value;
        if (!materialId) {
            alert("Pilih materi terlebih dahulu!");
            return;
        }

        document.getElementById('btnGenerateAi').disabled = true;
        document.getElementById('aiLoading').style.display = 'block';

        const formData = new FormData();
        formData.append('material_id', materialId);

        fetch('<?= BASE_URL ?>/notes/generate_ai', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('btnGenerateAi').disabled = false;
            document.getElementById('aiLoading').style.display = 'none';
            
            if (data.success) {
                document.getElementById('aiModal').style.display = 'none';
                // Clear content
                quill.setText("");
                // Auto focus
                quill.focus();
                
                // Show indicator and start typing
                document.getElementById('aiTypingIndicator').style.display = 'flex';
                aiTypingActive = true;
                originalHtml = quill.root.innerHTML;
                currentHtml = '';
                
                typeWriter(data.text, 0);
            } else {
                alert(data.error || "Gagal membuat catatan.");
            }
        })
        .catch(error => {
            document.getElementById('btnGenerateAi').disabled = false;
            document.getElementById('aiLoading').style.display = 'none';
            alert("Terjadi kesalahan sistem.");
            console.error(error);
        });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
