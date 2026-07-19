<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="display:flex; gap:2rem; flex-wrap:wrap;">
    
    <!-- Profile Info -->
    <div class="glass-card" style="flex:1; min-width:300px; position:relative;">
        <div id="profile-view">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 class="section-title" style="margin:0;">Profil Pengguna</h3>
            </div>
            
            <div style="text-align:center; margin-bottom:1.5rem;">
                <img src="<?= BASE_URL ?>/assets/<?= $user['photo'] !== 'default.png' ? 'uploads/' . $user['photo'] : 'img/default.png' ?>" alt="Profile" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:4px solid var(--primary); margin-bottom:1rem;">
                <h2 style="margin:0; font-size:1.5rem; color:var(--text-main);"><?= htmlspecialchars($user['name']) ?></h2>
                <p style="color:var(--text-muted); font-size:0.9rem;"><i class="fa-solid fa-envelope" style="font-size:0.8rem; margin-right:0.25rem;"></i><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <!-- Bio -->
            <div style="background: var(--bg-color); padding: 1.1rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1rem; border: 1px solid var(--border-color);">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:0.5rem;"><i class="fa-solid fa-quote-left" style="margin-right:0.35rem;"></i> Tentang Saya</div>
                <?php if(!empty($user['bio'])): ?>
                <p style="color:var(--text-main); font-size:0.9rem; line-height:1.6; margin:0;"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                <?php else: ?>
                <p style="color:var(--text-muted); font-size:0.9rem; font-style:italic; margin:0;">Pengguna belum menambahkan bio.</p>
                <?php endif; ?>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.85rem; background: var(--bg-color); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size:0.85rem; color:var(--text-muted);"><i class="fa-solid fa-graduation-cap" style="width: 18px;"></i> Program Studi</span>
                    <span style="font-weight:600; color:var(--text-main); font-size: 0.9rem;"><?= htmlspecialchars($user['prodi'] ?: '-') ?></span>
                </div>
                <div style="height: 1px; background: var(--border-color);"></div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size:0.85rem; color:var(--text-muted);"><i class="fa-solid fa-calendar" style="width: 18px;"></i> Semester</span>
                    <span style="font-weight:600; color:var(--text-main); font-size: 0.9rem;"><?= htmlspecialchars($user['semester'] ?: '-') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats & History -->
    <div style="flex:2; min-width:300px; display:flex; flex-direction:column; gap:2rem;">
        
        <!-- Statistik Belajar -->
        <div class="glass-card">
            <h3 class="section-title">Statistik Belajar</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap:1rem;">
                <div style="background:var(--card-bg-1); padding:1.5rem; border-radius:1rem; text-align:center;">
                    <div style="font-size:2rem; font-weight:700; color:var(--primary);"><?= $stats['total_notes'] ?></div>
                    <div style="font-size:0.875rem; color:var(--text-muted); margin-top:0.5rem;">Catatan Dibuat</div>
                </div>
                <div style="background:var(--card-bg-2); padding:1.5rem; border-radius:1rem; text-align:center;">
                    <div style="font-size:2rem; font-weight:700; color:var(--secondary);"><?= $stats['total_quizzes'] ?></div>
                    <div style="font-size:0.875rem; color:var(--text-muted); margin-top:0.5rem;">Quiz Selesai</div>
                </div>
                <div style="background:var(--card-bg-3); padding:1.5rem; border-radius:1rem; text-align:center;">
                    <div style="font-size:2rem; font-weight:700; color:#d97706;"><?= $stats['avg_score'] ?></div>
                    <div style="font-size:0.875rem; color:var(--text-muted); margin-top:0.5rem;">Nilai Rata-rata</div>
                </div>
                <div style="background:var(--card-bg-4); padding:1.5rem; border-radius:1rem; text-align:center;">
                    <div style="font-size:2rem; font-weight:700; color:#db2777;"><?= $stats['streak'] ?> <i class="fa-solid fa-fire"></i></div>
                    <div style="font-size:0.875rem; color:var(--text-muted); margin-top:0.5rem;">Streak Belajar</div>
                </div>
            </div>
        </div>

        <!-- Riwayat Quiz -->
        <div class="glass-card">
            <h3 class="section-title">Riwayat Quiz Terbaru</h3>
            <?php if(empty($quizHistory)): ?>
                <p style="color:var(--text-muted); text-align:center; padding:2rem;">Belum ada riwayat quiz.</p>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:2px solid #e2e8f0; text-align:left;">
                            <th style="padding:1rem;">Materi</th>
                            <th style="padding:1rem;">Nilai</th>
                            <th style="padding:1rem;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_slice($quizHistory, 0, 5) as $qh): ?>
                        <tr style="border-bottom:1px solid #e2e8f0;">
                            <td style="padding:1rem;">
                                <strong><?= htmlspecialchars($qh['material_title']) ?></strong>
                                <div style="font-size:0.75rem; color:var(--text-muted);"><?= $qh['total_questions'] ?> Soal</div>
                            </td>
                            <td style="padding:1rem;">
                                <span style="font-weight:700; color: <?= $qh['score'] >= 70 ? 'var(--secondary)' : '#ef4444' ?>;"><?= round($qh['score']) ?></span>
                            </td>
                            <td style="padding:1rem; font-size:0.875rem; color:var(--text-muted);"><?= date('d M Y', strtotime($qh['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
