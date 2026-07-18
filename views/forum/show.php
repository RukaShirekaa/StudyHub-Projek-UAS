<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Forum Post -->
<div class="card" style="margin-bottom:1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap: wrap; gap: 1rem;">
        <div style="flex: 1;">
            <a href="<?= BASE_URL ?>/forum" class="btn btn-ghost" style="margin-bottom: 0.75rem; padding: 0.3rem 0.75rem; font-size: 0.85rem; border-radius: var(--radius-full); border: 1px solid var(--border-color);">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h2 style="margin: 0.5rem 0 0; color:var(--text-main); font-size: 1.35rem; line-height: 1.3;"><?= htmlspecialchars($forum['title']) ?></h2>
        </div>
        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $forum['user_id']): ?>
            <a href="javascript:void(0)" class="btn btn-danger" style="padding:0.4rem 0.85rem; font-size:0.85rem;" onclick="confirmDelete('<?= BASE_URL ?>/forum/delete?id=<?= $forum['id'] ?>', 'Yakin ingin menghapus diskusi ini? Semua balasan akan ikut terhapus.')">
                <i class="fa-solid fa-trash"></i> Hapus
            </a>
        <?php endif; ?>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin: 1rem 0 1.25rem;">
        <span class="badge badge-primary"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($forum['author_name']) ?></span>
        <span class="badge badge-warning"><i class="fa-solid fa-folder"></i> <?= htmlspecialchars($forum['course_name']) ?></span>
        <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-clock"></i> <?= date('d M Y H:i', strtotime($forum['created_at'])) ?></span>
    </div>

    <div style="line-height:1.7; color:var(--text-main); font-size:1rem; padding: 1.25rem; background: rgba(99, 102, 241, 0.03); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
        <?= nl2br(htmlspecialchars($forum['content'])) ?>
    </div>
</div>

<!-- Replies -->
<h3 class="section-title" style="margin-bottom: 1rem;">
    <i class="fa-solid fa-comments" style="color: var(--secondary);"></i> Balasan
    <span class="badge badge-primary" style="margin-left: 0.25rem;"><?= count($replies) ?></span>
</h3>

<div style="display:flex; flex-direction:column; gap:0.85rem; margin-bottom:1.5rem;">
    <?php if(empty($replies)): ?>
        <div class="card">
            <div class="empty-state" style="padding: 2rem;">
                <i class="fa-regular fa-comment-dots"></i>
                <p>Belum ada balasan. Jadilah yang pertama membalas!</p>
            </div>
        </div>
    <?php endif; ?>
    <?php foreach($replies as $i => $r): ?>
        <div class="card animate-in" style="<?= $r['is_best_answer'] ? 'border-left: 4px solid var(--secondary); background: rgba(20, 184, 166, 0.03);' : '' ?>">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; background: var(--card-bg-1); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.8rem; font-weight: 700;">
                        <?= strtoupper(substr($r['author_name'], 0, 1)) ?>
                    </div>
                    <strong style="color:var(--text-main); font-size: 0.9rem;"><?= htmlspecialchars($r['author_name']) ?></strong>
                </div>
                <span style="font-size:0.75rem; color:var(--text-muted);"><i class="fa-regular fa-clock"></i> <?= date('d M Y H:i', strtotime($r['created_at'])) ?></span>
            </div>
            <div style="color:var(--text-main); line-height:1.6; font-size: 0.95rem; padding-left: 2.5rem;">
                <?= nl2br(htmlspecialchars($r['content'])) ?>
            </div>
            <?php if($r['is_best_answer']): ?>
                <div style="margin-top:0.85rem; padding-left: 2.5rem;">
                    <span class="badge badge-success" style="padding: 0.3rem 0.75rem;">
                        <i class="fa-solid fa-check-circle"></i> Jawaban Terbaik
                    </span>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Reply Form -->
<div class="card">
    <h4 style="margin-bottom:1rem; font-size: 1rem;"><i class="fa-solid fa-reply" style="color: var(--primary);"></i> Tulis Balasan</h4>
    <form action="<?= BASE_URL ?>/forum/reply" method="POST">
        <input type="hidden" name="forum_id" value="<?= $forum['id'] ?>">
        <textarea name="content" rows="4" required style="width:100%; padding:1rem; border:2px solid var(--border-color); border-radius:var(--radius-md); margin-bottom:1rem; outline:none; resize: vertical; font-size: 0.95rem; line-height: 1.6;" placeholder="Ketik balasan Anda di sini..."></textarea>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Kirim Balasan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
