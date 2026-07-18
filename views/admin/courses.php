<?php
$active = 'admin_courses';
require_once __DIR__ . '/layout/header.php';
?>

<div class="content-header">
    <h2 class="page-title"><i class="fa-solid fa-layer-group" style="color: var(--primary);"></i> Manajemen Mata Kuliah</h2>
    <p class="text-muted">Kelola daftar mata kuliah di sistem</p>
</div>

<!-- Add Course Form -->
<div class="card" style="margin-bottom: 2rem;">
    <h3 class="section-title"><i class="fa-solid fa-plus-circle"></i> Tambah Mata Kuliah</h3>
    <form action="<?= BASE_URL ?>/admin/courses/add" method="POST" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <div class="form-group-modern" style="flex: 1; min-width: 150px;">
            <label>Kode MK</label>
            <input type="text" name="code" required placeholder="Misal: IF101">
        </div>
        <div class="form-group-modern" style="flex: 2; min-width: 250px;">
            <label>Nama Mata Kuliah</label>
            <input type="text" name="name" required placeholder="Misal: Algoritma dan Pemrograman">
        </div>
        <div class="form-group-modern" style="flex: 2; min-width: 200px;">
            <label>Pemilik (User)</label>
            <select name="user_id" class="form-control" required style="padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); width: 100%;">
                <?php foreach($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group-modern" style="flex: 3; min-width: 300px;">
            <label>Deskripsi (Opsional)</label>
            <input type="text" name="description" placeholder="Deskripsi singkat...">
        </div>
        <div style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.85rem 1.5rem;"><i class="fa-solid fa-save"></i> Tambah</button>
        </div>
    </form>
</div>

<!-- Courses List Grouped by User -->
<div class="user-courses-accordion">
    <?php
    // Group courses by user
    $coursesByUser = [];
    foreach($courses as $c) {
        $coursesByUser[$c['user_id']][] = $c;
    }
    
    foreach($users as $user): 
        $userCourses = $coursesByUser[$user['id']] ?? [];
    ?>
    <div class="card" style="margin-bottom: 1rem; padding: 0;">
        <div class="user-header" onclick="toggleCourses(<?= $user['id'] ?>)" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; transition: background 0.2s;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--primary-glow); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <strong style="display: block; font-size: 1.1rem;"><?= htmlspecialchars($user['name']) ?></strong>
                    <div style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="badge badge-primary"><?= count($userCourses) ?> Mata Kuliah</span>
                <i class="fa-solid fa-chevron-down" id="icon-<?= $user['id'] ?>" style="transition: transform 0.3s; color: var(--text-muted);"></i>
            </div>
        </div>
        
        <div id="courses-<?= $user['id'] ?>" style="display: none; border-top: 1px solid var(--border-color);">
            <?php if(count($userCourses) > 0): ?>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Kode</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Nama Mata Kuliah</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Pemilik</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted);">Jumlah Materi</th>
                    <th style="padding: 1rem; font-size: 0.85rem; color: var(--text-muted); text-align: center;">Aksi</th>
                </tr>
            </thead>
                    <tbody>
                        <?php foreach($userCourses as $course): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;"><strong><?= htmlspecialchars($course['code']) ?></strong></td>
                            <td style="padding: 1rem;">
                                <strong><?= htmlspecialchars($course['name']) ?></strong>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;"><?= htmlspecialchars($course['description'] ?: 'Tidak ada deskripsi') ?></div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                                <i class="fa-solid fa-user" style="font-size: 0.75rem;"></i> <?= htmlspecialchars($course['owner_name'] ?: 'Tidak diketahui') ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge badge-primary"><?= $course['material_count'] ?> Materi</span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <button onclick="editCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['code']) ?>', '<?= htmlspecialchars($course['name']) ?>', '<?= htmlspecialchars($course['description'] ?? '') ?>', <?= $course['user_id'] ?? 0 ?>)" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.85rem; margin-right: 0.5rem;">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </button>
                                <a href="<?= BASE_URL ?>/admin/courses/delete?id=<?= $course['id'] ?>" class="btn btn-outline" style="color: var(--accent-rose); border-color: var(--accent-rose); padding: 0.25rem 0.75rem; font-size: 0.85rem;" onclick="return confirm('Yakin ingin menghapus mata kuliah ini? Semua materi yang terkait juga akan terhapus.')">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>Pengguna ini belum memiliki mata kuliah.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

<!-- Edit Form Overlay (Hidden by default) -->
<div id="editCourseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 class="section-title no-line" style="margin: 0;">Edit Mata Kuliah</h3>
            <button onclick="document.getElementById('editCourseModal').style.display='none'" class="btn-icon"><i class="fa-solid fa-times"></i></button>
        </div>
        <form action="<?= BASE_URL ?>/admin/courses/edit" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group-modern">
                <label>Kode MK</label>
                <input type="text" name="code" id="edit_code" required>
            </div>
            <div class="form-group-modern">
                <label>Nama Mata Kuliah</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-group-modern">
                <label>Pemilik (User)</label>
                <select name="user_id" id="edit_user_id" class="form-control" required style="padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); width: 100%;">
                    <?php foreach($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group-modern">
                <label>Deskripsi</label>
                <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
function toggleCourses(userId) {
    const content = document.getElementById('courses-' + userId);
    const icon = document.getElementById('icon-' + userId);
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

function editCourse(id, code, name, desc, userId) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_desc').value = desc;
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('editCourseModal').style.display = 'flex';
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
