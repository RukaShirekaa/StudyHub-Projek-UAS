<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="greeting-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <a href="<?= BASE_URL ?>/materials" class="btn" style="background: rgba(255,255,255,0.15); color: white; margin-bottom: 0.85rem; padding: 0.3rem 0.75rem; font-size: 0.85rem; border-radius: var(--radius-full); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h2 style="margin: 0.5rem 0 0.15rem; color: white; display:flex; align-items:center; gap:0.5rem; font-size: 1.4rem;"><i class="fa-solid fa-book"></i> <?= htmlspecialchars($course['name']) ?></h2>
            <p style="color: rgba(255,255,255,0.8); margin:0; font-size: 0.95rem;"><?= htmlspecialchars($course['description']) ?></p>
        </div>
        <button class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);" onclick="openModal('addMaterialModal')"><i class="fa-solid fa-cloud-arrow-up"></i> Unggah Materi</button>
    </div>
</div>

<div class="card">

    <div class="table-responsive">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border-color); text-align:left;">
                    <th style="padding:0.85rem 1rem; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Judul Materi</th>
                    <th style="padding:0.85rem 1rem; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pengunggah</th>
                    <th style="padding:0.85rem 1rem; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tanggal</th>
                    <th style="padding:0.85rem 1rem; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($materials as $m): ?>
                <tr style="border-bottom:1px solid var(--border-color); transition: background var(--transition-fast);" onmouseover="this.style.background='rgba(99,102,241,0.03)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; background: var(--card-bg-1); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--accent-rose); flex-shrink: 0;">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.9rem;"><?= htmlspecialchars($m['title']) ?></strong>
                                <div style="font-size:0.8rem; color:var(--text-muted);"><?= htmlspecialchars($m['description']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem; font-size: 0.9rem;"><?= htmlspecialchars($m['uploader_name']) ?></td>
                    <td style="padding:1rem; font-size: 0.85rem; color: var(--text-muted);"><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                    <td style="padding:1rem;">
                        <div style="display:flex; gap:0.5rem; flex-wrap: wrap;">
                            <a href="<?= BASE_URL ?>/assets/uploads/<?= $m['file_path'] ?>" target="_blank" class="btn btn-outline" style="padding:0.35rem 0.7rem; font-size:0.8rem;"><i class="fa-solid fa-eye"></i> Lihat</a>
                            <a href="<?= BASE_URL ?>/quiz?material_id=<?= $m['id'] ?>" class="btn btn-primary" style="padding:0.35rem 0.7rem; font-size:0.8rem;"><i class="fa-solid fa-brain"></i> Quiz</a>
                            <button type="button" class="btn btn-danger" style="padding:0.35rem 0.7rem; font-size:0.8rem;" onclick="confirmDelete('<?= BASE_URL ?>/materials/delete?id=<?= $m['id'] ?>', 'Yakin ingin menghapus materi ini beserta kuis dan riwayat AI di dalamnya?');"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($materials)): ?>
                <tr><td colspan="4">
                    <div class="empty-state">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p>Belum ada materi untuk mata kuliah ini.</p>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Material Modal -->
<div class="modal-overlay" id="addMaterialModal">
    <div class="modal-content" style="max-width: 520px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.15rem;"><i class="fa-solid fa-cloud-arrow-up" style="color: var(--primary);"></i> Unggah Materi PDF</h3>
            <button class="btn btn-ghost" onclick="closeModal('addMaterialModal')" style="font-size: 1.2rem;"><i class="fa-solid fa-times"></i></button>
        </div>
        <form action="<?= BASE_URL ?>/materials/upload" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <div class="form-group-modern">
                <label>Judul Materi</label>
                <input type="text" name="title" required placeholder="Misal: Bab 1 - Pendahuluan">
            </div>
            <div class="form-group-modern">
                <label>Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat materi..." style="width:100%; padding:0.75rem 1rem; border:2px solid var(--border-color); border-radius:var(--radius-md);"></textarea>
            </div>
            <div class="form-group-modern">
                <label>File PDF</label>
                <input type="file" name="file" accept="application/pdf" required style="width:100%; padding: 0.6rem; border: 2px dashed var(--border-color); border-radius: var(--radius-md); cursor: pointer;">
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fa-solid fa-upload"></i> Unggah</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('addMaterialModal')">Batal</button>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
