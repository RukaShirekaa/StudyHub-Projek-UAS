<?php
$active = 'admin_users';
require_once __DIR__ . '/layout/header.php';
?>

<div class="content-header">
    <h2 class="page-title"><i class="fa-solid fa-users" style="color: var(--primary);"></i> Manajemen Pengguna</h2>
    <p class="text-muted">Kelola daftar pengguna dan hak akses</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Pengguna</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Kontak</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Terdaftar</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Role</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted); text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <img src="<?= BASE_URL ?>/assets/<?= (!empty($user['photo']) && $user['photo'] !== 'default.png') ? 'uploads/' . $user['photo'] : 'img/default.png' ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <strong style="display: block;"><?= htmlspecialchars($user['name']) ?></strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($user['prodi'] ?: 'Belum diisi') ?></span>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        <?= htmlspecialchars($user['email']) ?>
                    </td>
                    <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        <?= date('d M Y', strtotime($user['created_at'])) ?>
                    </td>
                    <td style="padding: 1rem;">
                        <form action="<?= BASE_URL ?>/admin/users/role" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <select name="role" class="form-control" style="padding: 0.25rem; font-size: 0.85rem; width: auto;" onchange="this.form.submit()" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </form>
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                            <a href="<?= BASE_URL ?>/admin/users/delete?id=<?= $user['id'] ?>" class="btn btn-outline" style="color: var(--accent-rose); border-color: var(--accent-rose); padding: 0.25rem 0.75rem; font-size: 0.85rem;" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini secara permanen?')">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
