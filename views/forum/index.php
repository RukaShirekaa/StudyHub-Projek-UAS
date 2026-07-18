<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Forum Diskusi</h1>
    <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">Bertanya, menjawab, dan berdiskusi bersama</p>
</div>

<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; justify-content: space-between; align-items: center;">
    <form action="<?= BASE_URL ?>/forum" method="GET" style="flex: 1; min-width: 250px; position: relative; max-width: 400px;">
        <input type="hidden" name="tab" value="<?= htmlspecialchars($_GET['tab'] ?? 'semua') ?>">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        <input type="text" name="search" placeholder="Cari diskusi..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--surface); color: var(--text-main);">
    </form>
    <button class="btn btn-primary" onclick="openModal('addForumModal')">
        <i class="fa-solid fa-plus"></i> Buat Diskusi
    </button>
</div>

<div style="display: flex; gap: 2rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; overflow-x: auto;">
    <?php 
    $currentTab = $_GET['tab'] ?? 'semua'; 
    $sq = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
    ?>
    <a href="<?= BASE_URL ?>/forum?tab=semua<?= $sq ?>" style="padding: 0.75rem 0; font-weight: <?= $currentTab === 'semua' ? '600' : '500' ?>; color: <?= $currentTab === 'semua' ? 'var(--primary)' : 'var(--text-muted)' ?>; <?= $currentTab === 'semua' ? 'border-bottom: 2px solid var(--primary);' : '' ?> white-space: nowrap; text-decoration: none;">Semua</a>
    <a href="<?= BASE_URL ?>/forum?tab=diikuti<?= $sq ?>" style="padding: 0.75rem 0; font-weight: <?= $currentTab === 'diikuti' ? '600' : '500' ?>; color: <?= $currentTab === 'diikuti' ? 'var(--primary)' : 'var(--text-muted)' ?>; <?= $currentTab === 'diikuti' ? 'border-bottom: 2px solid var(--primary);' : '' ?> white-space: nowrap; text-decoration: none;">Diikuti</a>
    <a href="<?= BASE_URL ?>/forum?tab=belum_terjawab<?= $sq ?>" style="padding: 0.75rem 0; font-weight: <?= $currentTab === 'belum_terjawab' ? '600' : '500' ?>; color: <?= $currentTab === 'belum_terjawab' ? 'var(--primary)' : 'var(--text-muted)' ?>; <?= $currentTab === 'belum_terjawab' ? 'border-bottom: 2px solid var(--primary);' : '' ?> white-space: nowrap; text-decoration: none;">Belum Terjawab</a>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
    <!-- Diskusi List -->
    <div style="display:flex; flex-direction:column; gap:1rem;">
        <?php foreach($forums as $i => $f): ?>
            <a href="<?= BASE_URL ?>/forum/show?id=<?= $f['id'] ?>" class="card" style="padding: 1.25rem; text-decoration: none; color: inherit; display: flex; align-items: center; justify-content: space-between; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--surface); transition: all 0.2s ease;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99,102,241,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                        <i class="fa-regular fa-message"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 1.05rem; margin-bottom: 0.25rem; color: var(--text-main);"><?= htmlspecialchars($f['title']) ?></div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            <?= htmlspecialchars($f['course_name']) ?> &bull; <?= $f['reply_count'] ?> komentar &bull; <?= htmlspecialchars($f['author_name']) ?>
                        </div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1.5rem; flex-shrink: 0;">
                    <?php if($f['reply_count'] > 0): ?>
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; color: var(--accent-emerald); background: rgba(16, 185, 129, 0.1); padding: 0.4rem 0.75rem; border-radius: var(--radius-full);">
                            <i class="fa-regular fa-circle-check"></i> Terjawab
                        </span>
                    <?php else: ?>
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; color: var(--accent-amber); background: rgba(245, 158, 11, 0.1); padding: 0.4rem 0.75rem; border-radius: var(--radius-full);">
                            <i class="fa-regular fa-clock"></i> Belum
                        </span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if(empty($forums)): ?>
            <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
                <i class="fa-solid fa-comments" style="font-size: 3rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 1rem;"></i>
                <p style="color: var(--text-muted);">Belum ada diskusi. Mulai diskusi pertama!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Forum Modal -->
<div class="modal-overlay" id="addForumModal">
    <div class="modal-content" style="max-width: 560px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.15rem;"><i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Buat Diskusi Baru</h3>
            <button class="btn btn-ghost" onclick="closeModal('addForumModal')" style="font-size: 1.2rem;"><i class="fa-solid fa-times"></i></button>
        </div>
        <form action="<?= BASE_URL ?>/forum/create" method="POST">
            <div class="form-group-modern">
                <label>Mata Kuliah</label>
                <select name="course_id" required>
                    <?php foreach($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group-modern">
                <label>Judul Diskusi</label>
                <input type="text" name="title" required placeholder="Misal: Cara menghitung kompleksitas algoritma?">
            </div>
            <div class="form-group-modern">
                <label>Isi Diskusi</label>
                <textarea name="content" rows="6" required placeholder="Jelaskan pertanyaan atau topik diskusi..." style="width:100%; padding:0.75rem 1rem; border:2px solid var(--border-color); border-radius:var(--radius-md); resize: vertical;"></textarea>
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fa-solid fa-paper-plane"></i> Posting Diskusi</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('addForumModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
