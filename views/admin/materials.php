<?php
$active = 'admin_materials';
require_once __DIR__ . '/layout/header.php';
?>

<div class="content-header">
    <h2 class="page-title"><i class="fa-solid fa-file-pdf" style="color: var(--primary);"></i> Manajemen Materi</h2>
    <p class="text-muted">Pantau dan kelola materi yang diunggah oleh mahasiswa</p>
</div>

<!-- Materials List Grouped by User -->
<div class="user-materials-accordion">
    <?php
    // Group materials by user
    $materialsByUser = [];
    foreach($materials as $m) {
        $materialsByUser[$m['uploaded_by']][] = $m;
    }
    
    foreach($users as $user): 
        $userMaterials = $materialsByUser[$user['id']] ?? [];
    ?>
    <div class="card" style="margin-bottom: 1rem; padding: 0;">
        <div class="user-header" onclick="toggleMaterials(<?= $user['id'] ?>)" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; transition: background 0.2s;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(99, 102, 241, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-user-circle"></i>
                </div>
                <div>
                    <strong style="display: block; font-size: 1.1rem;"><?= htmlspecialchars($user['name']) ?></strong>
                    <div style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="badge badge-secondary"><?= count($userMaterials) ?> Materi</span>
                <i class="fa-solid fa-chevron-down" id="icon-mat-<?= $user['id'] ?>" style="transition: transform 0.3s; color: var(--text-muted);"></i>
            </div>
        </div>
        
        <div id="materials-<?= $user['id'] ?>" style="display: none; border-top: 1px solid var(--border-color);">
            <?php if(count($userMaterials) > 0): ?>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Materi</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Mata Kuliah</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Pengunggah</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Waktu</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted); text-align: center;">Aksi</th>
                </tr>
            </thead>
                    <tbody>
                        <?php foreach($userMaterials as $m): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                                    <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fa-solid fa-file-pdf" style="font-size: 1.25rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="display: block;"><?= htmlspecialchars($m['title']) ?></strong>
                                        <span style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-top: 0.25rem; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($m['description'] ?: 'Tidak ada deskripsi') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem; font-size: 0.9rem;">
                                <span class="badge badge-secondary"><?= htmlspecialchars($m['course_name']) ?></span>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                                <i class="fa-solid fa-user" style="font-size: 0.75rem;"></i> <?= htmlspecialchars($m['uploader_name']) ?>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                <?= date('d M Y', strtotime($m['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="<?= BASE_URL ?>/assets/uploads/<?= ltrim($m['file_path'], '/') ?>" target="_blank" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.85rem; margin-right: 0.5rem;" title="Lihat/Download">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/admin/materials/delete?id=<?= $m['id'] ?>" class="btn btn-outline" style="color: var(--accent-rose); border-color: var(--accent-rose); padding: 0.25rem 0.75rem; font-size: 0.85rem;" onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini secara permanen dari server?')" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>Pengguna ini belum mengunggah materi apa pun.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
function toggleMaterials(userId) {
    const content = document.getElementById('materials-' + userId);
    const icon = document.getElementById('icon-mat-' + userId);
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
