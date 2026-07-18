<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Mata Kuliah</h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">Pilih mata kuliah untuk melihat dan mengelola materi.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addCourseModal')">
        <i class="fa-solid fa-plus"></i> Tambah Mata Kuliah
    </button>
</div>

<form action="<?= BASE_URL ?>/materials" method="GET" style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; align-items: center;">
    <div style="flex: 1; min-width: 250px; position: relative; max-width: 400px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        <input type="text" name="search" placeholder="Cari mata kuliah..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--surface); color: var(--text-main);">
    </div>
    <select name="sort" onchange="this.form.submit()" style="padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--surface); color: var(--text-main); min-width: 150px;">
        <option value="terbaru" <?= (($_GET['sort'] ?? '') === 'terbaru') ? 'selected' : '' ?>>Terbaru</option>
        <option value="az" <?= (($_GET['sort'] ?? '') === 'az') ? 'selected' : '' ?>>A-Z</option>
    </select>
</form>

<div class="course-grid">
    <?php foreach($courses as $i => $course): ?>
    <div class="card interactive-course-card" style="padding: 0; overflow: hidden; border-radius: var(--radius-lg); text-decoration: none; color: inherit; display: block; border: 1px solid var(--border-color); position: relative;">
        <a href="<?= BASE_URL ?>/materials/course?id=<?= $course['id'] ?>" style="position: absolute; inset: 0; z-index: 1;"></a>
        <?php 
            $gradients = [
                'linear-gradient(135deg, #1e1b4b, #312e81)',
                'linear-gradient(135deg, #020617, #0f172a)',
                'linear-gradient(135deg, #172554, #1e3a8a)',
                'linear-gradient(135deg, #083344, #164e63)',
                'linear-gradient(135deg, #1e293b, #334155)'
            ];
            $icons = ['fa-book', 'fa-laptop-code', 'fa-flask', 'fa-calculator', 'fa-chart-pie', 'fa-globe', 'fa-language', 'fa-microscope', 'fa-brain', 'fa-scale-balanced', 'fa-atom', 'fa-chart-line', 'fa-code', 'fa-square-root-variable'];
            $bg = $gradients[$course['id'] % count($gradients)];
            $icon = $icons[$course['id'] % count($icons)];
        ?>
        <div class="course-icon-container" style="height: 120px; background: <?= $bg ?>; display: flex; align-items: center; justify-content: center; position: relative;">
            <i class="fa-solid <?= $icon ?>" style="font-size: 2.5rem; color: rgba(255,255,255,0.2);"></i>
            <button type="button" class="btn btn-danger" style="position: absolute; top: 0.75rem; right: 0.75rem; padding: 0.35rem 0.6rem; font-size: 0.85rem; z-index: 10; background: rgba(239, 68, 68, 0.85); backdrop-filter: blur(4px); border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.15);" onclick="confirmDelete('<?= BASE_URL ?>/course/delete?id=<?= $course['id'] ?>', 'Yakin ingin menghapus mata kuliah ini? SEMUA materi, kuis, dan forum di dalamnya akan ikut terhapus!');">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
        <div style="padding: 1.25rem;">
            <h4 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($course['name']) ?></h4>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-family: 'Fira Code', monospace; margin-bottom: 1rem;"><?= htmlspecialchars($course['code']) ?></div>
            <div style="display: flex; gap: 0.5rem; align-items: center; position: relative; z-index: 10;">
                <span style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">Lihat Materi</span>
                <i class="fa-solid fa-arrow-right course-arrow-icon" style="color: var(--text-muted); font-size: 0.85rem;"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if(empty($courses)): ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
            <i class="fa-solid fa-folder-plus" style="font-size: 3.5rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
            <p style="color: var(--text-muted);">Belum ada mata kuliah. Tambahkan yang pertama!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Add Course Modal -->
<div class="modal-overlay" id="addCourseModal">
    <div class="modal-content" style="max-width: 420px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.15rem;"><i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Tambah Mata Kuliah</h3>
            <button class="btn btn-ghost" onclick="closeModal('addCourseModal')" style="font-size: 1.2rem;"><i class="fa-solid fa-times"></i></button>
        </div>
        <form action="<?= BASE_URL ?>/course/add" method="POST">
            <div class="form-group-modern">
                <label>Kode MK</label>
                <input type="text" name="code" required placeholder="Misal: IF101">
            </div>
            <div class="form-group-modern">
                <label>Nama MK</label>
                <input type="text" name="name" required placeholder="Misal: Pemrograman Web">
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fa-solid fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('addCourseModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
