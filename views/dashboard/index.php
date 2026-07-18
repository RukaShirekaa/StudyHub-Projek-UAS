<?php require_once __DIR__ . '/../layout/header.php'; ?>

<?php
    $userName = htmlspecialchars($_SESSION['user_name'] ?? 'Mahasiswa');
?>

<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Halo, <?= $userName ?>! 👋</h1>
    <p style="color: var(--text-muted);" class="text-muted-override font-size: 1rem;">Semangat belajarnya hari ini!</p>
</div>

<!-- Stat Cards -->
<div class="stats-grid-5" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 2.5rem;">
    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-1); color: var(--card-text-1);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-1);">
            <i class="fa-solid fa-book"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_materi'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Total Materi</div>
        </div>
    </div>
    
    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-2); color: var(--card-text-2);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-2);">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_catatan'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Total Catatan</div>
        </div>
    </div>

    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-3); color: var(--card-text-3);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-3);">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_diskusi'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Total Diskusi</div>
        </div>
    </div>

    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-4); color: var(--card-text-4);">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-4);">
            <i class="fa-solid fa-brain"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['total_quiz'] ?></div>
            <div style="font-size: 0.8rem;" class="text-muted-override">Total Quiz</div>
        </div>
    </div>

    <div class="card stat-card-gradient" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, #ff8a00, #ff5722); color: #fff;">
        <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: rgba(255, 255, 255, 0.25);">
            <i class="fa-solid fa-fire"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;"><?= $stats['streak'] ?></div>
            <div style="font-size: 0.8rem; opacity: 0.9;">Hari Beruntun</div>
        </div>
    </div>
</div>

<!-- Mata Kuliah Saya -->
<div style="margin-bottom: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0;">Mata Kuliah Saya</h3>
        <a href="<?= BASE_URL ?>/materials" style="font-size: 0.9rem; color: var(--primary); font-weight: 600; text-decoration: none;">Lihat semua</a>
    </div>
    
    <div class="course-grid">
        <?php
        $gradients = [
            'linear-gradient(135deg, #1e1b4b, #312e81)',
            'linear-gradient(135deg, #020617, #0f172a)',
            'linear-gradient(135deg, #172554, #1e3a8a)',
            'linear-gradient(135deg, #083344, #164e63)',
            'linear-gradient(135deg, #1e293b, #334155)'
        ];
        $icons = ['fa-book', 'fa-laptop-code', 'fa-flask', 'fa-calculator', 'fa-chart-pie', 'fa-globe', 'fa-language', 'fa-microscope', 'fa-brain', 'fa-scale-balanced', 'fa-atom', 'fa-chart-line', 'fa-code', 'fa-square-root-variable'];
        ?>
        <?php foreach($allCourses as $c): ?>
        <div class="card interactive-course-card" style="padding: 0; overflow: hidden; border-radius: var(--radius-lg); text-decoration: none; color: inherit; display: block; border: 1px solid var(--border-color); position: relative;">
            <a href="<?= BASE_URL ?>/materials/course?id=<?= $c['id'] ?>" style="position: absolute; inset: 0; z-index: 1;"></a>
            <?php 
                $bg = $gradients[$c['id'] % count($gradients)];
                $icon = $icons[$c['id'] % count($icons)];
            ?>
            <div class="course-icon-container" style="height: 120px; background: <?= $bg ?>; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid <?= $icon ?>" style="font-size: 2.5rem; color: rgba(255,255,255,0.2);"></i>
            </div>
            <div style="padding: 1.25rem;">
                <h4 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($c['name']) ?></h4>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-family: 'Fira Code', monospace; margin-bottom: 1rem;"><?= htmlspecialchars($c['code'] ?? '01') ?></div>
                <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 10;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">Lihat Materi</span>
                        <i class="fa-solid fa-arrow-right course-arrow-icon" style="color: var(--text-muted); font-size: 0.85rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Two Columns: Materi Terbaru & Forum Terbaru -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 400px), 1fr)); gap: 1.5rem;">
    <!-- Materi Terbaru -->
    <div>
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem;">Materi Terbaru</h3>
        <div class="card" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <?php if(empty($recentMaterials)): ?>
                <div style="text-align: center; color: var(--text-muted);" class="text-muted-override padding: 1rem;">Belum ada materi.</div>
            <?php else: ?>
                <?php foreach($recentMaterials as $m): ?>
                <a href="<?= BASE_URL ?>/materials" style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: all 0.2s ease;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <i class="fa-solid fa-file-pdf" style="color: #ef4444; font-size: 1.5rem;"></i>
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);"><?= htmlspecialchars($m['title']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);" class="text-muted-override"><?= htmlspecialchars($m['course_name']) ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Forum Terbaru -->
    <div>
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem;">Forum Terbaru</h3>
        <div class="card" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <?php if(empty($recentTopics)): ?>
                <div style="text-align: center; color: var(--text-muted);" class="text-muted-override padding: 1rem;">Belum ada diskusi forum.</div>
            <?php else: ?>
                <?php foreach($recentTopics as $t): ?>
                <a href="<?= BASE_URL ?>/forum/show?id=<?= $t['id'] ?>" style="text-decoration: none; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; gap: 1rem; transition: all 0.2s ease;">
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-full); background: rgba(99, 102, 241, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0.15rem; color: var(--text-main);"><?= htmlspecialchars($t['title']) ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);" class="text-muted-override">
                            Oleh <?= htmlspecialchars($t['author_name']) ?> • <?= date('d M Y', strtotime($t['created_at'])) ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
