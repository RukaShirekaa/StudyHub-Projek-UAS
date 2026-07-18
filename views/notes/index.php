<?php 
/**
 * @var array $groupedNotes
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
                                <a href="<?= BASE_URL ?>/notes/edit?id=<?= $n['id'] ?>" style="display: block; padding: 1rem; border-radius: var(--radius-md); border: 1px solid transparent; text-decoration: none; transition: all 0.2s ease; background: <?= (isset($note) && $note['id'] == $n['id']) ? 'var(--bg-color)' : 'transparent' ?>; border-color: <?= (isset($note) && $note['id'] == $n['id']) ? 'var(--primary)' : 'var(--border-color)' ?>;">
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

    <!-- Right Column: Empty State -->
    <div class="card" style="padding: 0; display: flex; flex-direction: column; height: 100%; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--surface); align-items: center; justify-content: center; text-align: center;">
        <i class="fa-solid fa-file-pen" style="font-size: 4rem; color: var(--text-muted); opacity: 0.2; margin-bottom: 1.5rem;"></i>
        <h3 style="font-size: 1.2rem; color: var(--text-main); margin-bottom: 0.5rem;">Pilih Catatan</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Pilih catatan dari daftar di sebelah kiri untuk melihat atau mengedit isinya.</p>
    </div>
</div>


<?php require_once __DIR__ . '/../layout/footer.php'; ?>
