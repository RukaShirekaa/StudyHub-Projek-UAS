<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Hasil Pencarian</h1>
    <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">
        Menampilkan hasil untuk: <strong style="color: var(--primary);"><?= htmlspecialchars($query) ?></strong>
    </p>
</div>

<?php if (trim($query) === ''): ?>
    <div class="card" style="padding: 3rem; text-align: center; color: var(--text-muted);">
        <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
        <h3>Mulai Pencarian</h3>
        <p>Ketikkan kata kunci di kolom pencarian atas untuk menemukan materi, forum, atau catatan.</p>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Materi Kuliah Results -->
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-book" style="color: var(--primary);"></i> Materi Kuliah (<?= count($courses) ?>)
            </h2>
            <?php if (empty($courses)): ?>
                <div class="card" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">Tidak ada materi ditemukan.</div>
            <?php else: ?>
                <div class="course-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                    <?php foreach($courses as $c): ?>
                        <div class="card" style="display: flex; flex-direction: column; height: 100%;">
                            <div style="padding: 1.5rem; flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                    <div class="icon-bg"><i class="fa-solid fa-book-open"></i></div>
                                    <span class="badge badge-primary"><?= htmlspecialchars($c['code']) ?></span>
                                </div>
                                <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);"><?= htmlspecialchars($c['name']) ?></h3>
                                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; margin: 0;"><?= htmlspecialchars(substr($c['description'], 0, 100)) ?>...</p>
                            </div>
                            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); background: rgba(0,0,0,0.02);">
                                <a href="<?= BASE_URL ?>/materials/course?id=<?= $c['id'] ?>" class="btn btn-outline" style="width: 100%; justify-content: center;">Lihat Materi</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Forum Diskusi Results -->
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-comments" style="color: var(--secondary);"></i> Forum Diskusi (<?= count($forums) ?>)
            </h2>
            <?php if (empty($forums)): ?>
                <div class="card" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">Tidak ada diskusi ditemukan.</div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach($forums as $f): ?>
                        <a href="<?= BASE_URL ?>/forum/show?id=<?= $f['id'] ?>" class="card" style="padding: 1.25rem; text-decoration: none; color: inherit; display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div class="icon-bg" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 0.25rem;"><?= htmlspecialchars($f['title']) ?></h3>
                                    <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                                        <span><i class="fa-solid fa-user"></i> <?= htmlspecialchars($f['author_name']) ?></span>
                                        <span><i class="fa-solid fa-book"></i> <?= htmlspecialchars($f['course_name']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Catatan Results -->
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--success);"></i> Catatan Saya (<?= count($notes) ?>)
            </h2>
            <?php if (empty($notes)): ?>
                <div class="card" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">Tidak ada catatan ditemukan.</div>
            <?php else: ?>
                <div class="course-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
                    <?php foreach($notes as $n): ?>
                        <a href="<?= BASE_URL ?>/notes/edit?id=<?= $n['id'] ?>" class="card" style="padding: 1.25rem; text-decoration: none; color: inherit;">
                            <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 0.5rem;"><?= htmlspecialchars($n['title']) ?></h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; margin: 0;"><?= htmlspecialchars(substr($n['content'], 0, 80)) ?>...</p>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
