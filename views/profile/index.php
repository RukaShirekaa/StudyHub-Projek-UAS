<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="profile-layout" style="display:flex; gap:1.5rem; align-items:flex-start;">
    
    <!-- Profile Info & Edit -->
    <div style="flex: 1; display:flex; flex-direction:column; gap:1.25rem;">
        <div class="card" style="position:relative;">
        
        <!-- VIEW MODE -->
        <div id="profile-view">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 class="section-title no-line" style="margin:0;"><i class="fa-solid fa-user-circle" style="color: var(--primary);"></i> Profil Saya</h3>
                <button class="btn btn-outline" onclick="toggleEditMode(true)" style="font-size: 0.85rem;"><i class="fa-solid fa-edit"></i> Edit Profil</button>
            </div>
            
            <div style="text-align:center; margin-bottom:1.5rem;">
                <div style="position: relative; display: inline-block;">
                    <img src="<?= BASE_URL ?>/assets/<?= (!empty($user['photo']) && $user['photo'] !== 'default.png') ? 'uploads/' . $user['photo'] : 'img/default.png' ?>" alt="Profile" style="width:110px; height:110px; border-radius:var(--radius-full); object-fit:cover; border:4px solid transparent; background-image: var(--primary-gradient); background-origin: border-box; background-clip: content-box, border-box; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);">
                </div>
                <h2 style="margin:1rem 0 0.15rem; font-size:1.35rem; color:var(--text-main);"><?= htmlspecialchars($user['name']) ?></h2>
                <p style="color:var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-envelope" style="font-size: 0.8rem; margin-right: 0.25rem;"></i><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <!-- Bio -->
            <div style="background: var(--bg-color); padding: 1.1rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1rem; border: 1px solid var(--border-color);">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:0.5rem;"><i class="fa-solid fa-quote-left" style="margin-right:0.35rem;"></i> Tentang Saya</div>
                <?php if(!empty($user['bio'])): ?>
                <p style="color:var(--text-main); font-size:0.9rem; line-height:1.6; margin:0;"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                <?php else: ?>
                <p style="color:var(--text-muted); font-size:0.9rem; font-style:italic; margin:0;">Belum ada bio. Klik Edit Profil untuk menambahkan.</p>
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

        <!-- EDIT MODE (Hidden by default) -->
        <div id="profile-edit" style="display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 class="section-title no-line" style="margin:0;"><i class="fa-solid fa-edit" style="color: var(--primary);"></i> Edit Profil</h3>
                <button class="btn btn-outline" onclick="toggleEditMode(false)" style="font-size: 0.85rem;"><i class="fa-solid fa-times"></i> Batal</button>
            </div>
            
            <form action="<?= BASE_URL ?>/profile/update" method="POST" enctype="multipart/form-data">
                <div style="text-align:center; margin-bottom:2rem;">
                    <img src="<?= BASE_URL ?>/assets/<?= (!empty($user['photo']) && $user['photo'] !== 'default.png') ? 'uploads/' . $user['photo'] : 'img/default.png' ?>" alt="Profile" style="width:110px; height:110px; border-radius:var(--radius-full); object-fit:cover; border:4px solid var(--primary); margin-bottom:1rem;">
                    <div>
                        <label class="btn btn-outline" style="cursor:pointer; font-size:0.85rem;">
                            <i class="fa-solid fa-camera"></i> Ubah Foto
                            <input type="file" name="photo" accept="image/*" style="display:none;" onchange="this.form.submit()">
                        </label>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="form-group-modern">
                    <label>Email (Tidak dapat diubah)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>
                <div class="form-group-modern">
                    <label>Program Studi (Prodi)</label>
                    <input type="text" name="prodi" value="<?= htmlspecialchars($user['prodi'] ?? '') ?>" placeholder="Misal: Teknik Informatika">
                </div>
                <div class="form-group-modern">
                    <label>Bio / Tentang Saya</label>
                    <textarea name="bio" rows="4" placeholder="Tulis sedikit tentang diri Anda..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>
                <div class="form-group-modern">
                    <label>Semester</label>
                    <input type="number" name="semester" value="<?= htmlspecialchars($user['semester'] ?? '') ?>" placeholder="Misal: 3">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; padding:0.85rem; margin-top: 0.5rem;"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            </form>

            <hr style="border:0; border-top:1px solid var(--border-color); margin: 2rem 0 1.5rem;">
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 class="section-title no-line" style="margin:0;"><i class="fa-solid fa-lock" style="color: var(--primary);"></i> Ganti Password</h3>
            </div>
            <form action="<?= BASE_URL ?>/profile/change-password" method="POST">
                <div class="form-group-modern">
                    <label>Password Lama</label>
                    <div style="position: relative;">
                        <input type="password" name="old_password" required placeholder="Masukkan password lama" style="padding-right: 40px;">
                        <i class="fa-solid fa-eye" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="const input = this.previousElementSibling; if(input.type === 'password'){ input.type = 'text'; this.classList.remove('fa-eye'); this.classList.add('fa-eye-slash'); } else { input.type = 'password'; this.classList.remove('fa-eye-slash'); this.classList.add('fa-eye'); }"></i>
                    </div>
                </div>
                <div class="form-group-modern">
                    <label>Password Baru</label>
                    <div style="position: relative;">
                        <input type="password" name="new_password" required placeholder="Minimal 6 karakter" minlength="6" style="padding-right: 40px;">
                        <i class="fa-solid fa-eye" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="const input = this.previousElementSibling; if(input.type === 'password'){ input.type = 'text'; this.classList.remove('fa-eye'); this.classList.add('fa-eye-slash'); } else { input.type = 'password'; this.classList.remove('fa-eye-slash'); this.classList.add('fa-eye'); }"></i>
                    </div>
                </div>
                <div class="form-group-modern">
                    <label>Konfirmasi Password Baru</label>
                    <div style="position: relative;">
                        <input type="password" name="confirm_password" required placeholder="Ulangi password baru" minlength="6" style="padding-right: 40px;">
                        <i class="fa-solid fa-eye" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="const input = this.previousElementSibling; if(input.type === 'password'){ input.type = 'text'; this.classList.remove('fa-eye'); this.classList.add('fa-eye-slash'); } else { input.type = 'password'; this.classList.remove('fa-eye-slash'); this.classList.add('fa-eye'); }"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; padding:0.85rem; margin-top: 0.5rem;"><i class="fa-solid fa-key"></i> Perbarui Password</button>
            </form>
        </div>
        </div>
        
    </div>

    <!-- Stats & History -->
    <div style="flex: 2; display:flex; flex-direction:column; gap:1.25rem;">
        
        <!-- Statistik Belajar -->
        <div class="card">
            <h3 class="section-title"><i class="fa-solid fa-chart-bar" style="color: var(--primary);"></i> Statistik Belajar</h3>
            <div class="stats-grid-4" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem;">
                <div class="card stat-card-gradient animate-in" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-1); color: var(--card-text-1);">
                    <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-1);">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div style="text-align: left;">
                        <div class="stat-number" style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;" data-counter="<?= $stats['total_notes'] ?>"><?= $stats['total_notes'] ?></div>
                        <div class="stat-label text-muted-override" style="font-size: 0.8rem;">Catatan</div>
                    </div>
                </div>
                <div class="card stat-card-gradient animate-in" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-2); color: var(--card-text-2);">
                    <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-2);">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <div style="text-align: left;">
                        <div class="stat-number" style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;" data-counter="<?= $stats['total_quizzes'] ?>"><?= $stats['total_quizzes'] ?></div>
                        <div class="stat-label text-muted-override" style="font-size: 0.8rem;">Quiz Selesai</div>
                    </div>
                </div>
                <div class="card stat-card-gradient animate-in" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-3); color: var(--card-text-3);">
                    <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-3);">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div style="text-align: left;">
                        <div class="stat-number" style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;" data-counter="<?= $stats['avg_score'] ?>"><?= $stats['avg_score'] ?></div>
                        <div class="stat-label text-muted-override" style="font-size: 0.8rem;">Rata-rata</div>
                    </div>
                </div>
                <div class="card stat-card-gradient animate-in" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg-4); color: var(--card-text-4);">
                    <div class="icon-bg" style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: var(--card-text-4);">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div style="text-align: left;">
                        <div class="stat-number" style="font-size: 1.5rem; font-weight: 700; line-height: 1.2;" data-counter="<?= $stats['streak'] ?>"><?= $stats['streak'] ?></div>
                        <div class="stat-label text-muted-override" style="font-size: 0.8rem;">Streak</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Quiz -->
        <div class="card">
            <h3 class="section-title"><i class="fa-solid fa-history" style="color: var(--secondary);"></i> Riwayat Quiz Terbaru</h3>
            <?php if(empty($quizHistory)): ?>
                <div class="empty-state" style="padding: 2rem;">
                    <i class="fa-solid fa-trophy"></i>
                    <p>Belum ada riwayat quiz. Mulai quiz pertamamu!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:2px solid var(--border-color); text-align:left;">
                                <th style="padding:0.75rem 1rem; font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Materi</th>
                                <th style="padding:0.75rem 1rem; font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Nilai</th>
                                <th style="padding:0.75rem 1rem; font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(array_slice($quizHistory, 0, 5) as $qh): ?>
                            <tr style="border-bottom:1px solid var(--border-color); transition: background var(--transition-fast);" onmouseover="this.style.background='rgba(99,102,241,0.03)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:0.85rem 1rem;">
                                    <strong style="font-size: 0.9rem;"><?= htmlspecialchars($qh['material_title']) ?></strong>
                                    <div style="font-size:0.75rem; color:var(--text-muted);"><?= $qh['total_questions'] ?> Soal</div>
                                </td>
                                <td style="padding:0.85rem 1rem;">
                                    <span class="badge <?= $qh['score'] >= 70 ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.85rem; padding: 0.25rem 0.65rem;">
                                        <?= round($qh['score']) ?>
                                    </span>
                                </td>
                                <td style="padding:0.85rem 1rem; font-size:0.85rem; color:var(--text-muted);"><?= date('d M Y', strtotime($qh['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function toggleEditMode(showEdit) {
    document.getElementById('profile-view').style.display = showEdit ? 'none' : 'block';
    document.getElementById('profile-edit').style.display = showEdit ? 'block' : 'none';
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
