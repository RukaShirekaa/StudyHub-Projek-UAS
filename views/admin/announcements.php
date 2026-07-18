<?php
$active = 'admin_announcements';
require_once __DIR__ . '/layout/header.php';
?>

<div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 class="page-title"><i class="fa-solid fa-bullhorn" style="color: var(--primary);"></i> Pengumuman</h2>
        <p class="text-muted">Kirim dan kelola pengumuman untuk notifikasi pengguna</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addAnnouncementModal')">
        <i class="fa-solid fa-plus"></i> Buat Pengumuman
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted); width: 200px;">Waktu</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Pesan Pengumuman</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($announcements)): ?>
                <tr>
                    <td colspan="2" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada pengumuman yang dikirim.</td>
                </tr>
                <?php else: ?>
                <?php foreach($announcements as $a): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem; color: var(--text-muted); font-size: 0.85rem; white-space: nowrap;">
                        <?= date('d M Y, H:i', strtotime($a['created_at'])) ?>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa-solid fa-bullhorn" style="font-size: 1.1rem;"></i>
                            </div>
                            <div>
                                <strong style="display: block; margin-bottom: 0.25rem;">Pengumuman Admin</strong>
                                <span style="font-size: 0.9rem; line-height: 1.4; display: block; color: var(--text-main);"><?= nl2br(htmlspecialchars($a['message'])) ?></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Pengumuman -->
<div class="modal-overlay" id="addAnnouncementModal">
    <div class="modal-content" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Buat Pengumuman Baru</h3>
            <button class="btn btn-ghost" onclick="closeModal('addAnnouncementModal')" style="font-size: 1.2rem;"><i class="fa-solid fa-times"></i></button>
        </div>
        <form action="<?= BASE_URL ?>/admin/announcements/send" method="POST">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Target Pengguna</label>
                <select name="target" class="form-control" style="width: 100%; padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--surface);">
                    <option value="all">Semua Pengguna</option>
                    <option value="students">Hanya Mahasiswa</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Pesan Pengumuman</label>
                <textarea name="message" class="form-control" rows="4" style="width: 100%; padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--surface);" required></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" class="btn btn-outline" onclick="closeModal('addAnnouncementModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
