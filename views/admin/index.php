<?php
$active = 'admin_dashboard';
require_once __DIR__ . '/layout/header.php';
?>

<div class="content-header">
    <h2 class="page-title"><i class="fa-solid fa-gauge" style="color: var(--primary);"></i> Admin Dashboard</h2>
    <p class="text-muted">Ringkasan sistem StudyHub</p>
</div>

<!-- Stat Cards -->
<div class="stats-grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-1); color: var(--card-text-1);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-1);">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_users'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Total Pengguna</div>
        </div>
    </div>
    
    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-2); color: var(--card-text-2);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-2);">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_courses'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Mata Kuliah</div>
        </div>
    </div>

    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-3); color: var(--card-text-3);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-3);">
            <i class="fa-solid fa-file-pdf"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_materials'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Total Materi</div>
        </div>
    </div>

    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-4); color: var(--card-text-4);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-4);">
            <i class="fa-solid fa-comments"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_forums'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Topik Forum</div>
        </div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title no-line" style="margin: 0;"><i class="fa-solid fa-user-plus" style="color: var(--secondary);"></i> Pengguna Baru Terdaftar</h3>
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline" style="font-size: 0.85rem;">Lihat Semua</a>
    </div>
    
    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: var(--text-muted);">Nama</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: var(--text-muted);">Email</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: var(--text-muted);">Role</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: var(--text-muted);">Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recentUsers as $user): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem;"><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                    <td style="padding: 1rem; color: var(--text-muted);"><?= htmlspecialchars($user['email']) ?></td>
                    <td style="padding: 1rem;">
                        <?php if($user['role'] === 'admin'): ?>
                            <span class="badge badge-primary">Admin</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Student</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        <?= date('d M Y, H:i', strtotime($user['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
