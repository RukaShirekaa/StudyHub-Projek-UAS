</div>
</div>

<!-- Global Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteConfirmModal" style="z-index: 99999;">
    <div class="modal-content" style="max-width: 400px; text-align: center; padding: 2rem;">
        <div style="width: 64px; height: 64px; background: rgba(244, 63, 94, 0.1); color: var(--accent-rose); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.5rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem;">Konfirmasi Hapus</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; line-height: 1.5;" id="deleteConfirmText">Apakah Anda yakin ingin menghapus ini?</p>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeModal('deleteConfirmModal')">Batal</button>
            <a href="#" id="deleteConfirmBtn" class="btn btn-danger" style="flex: 1;"><i class="fa-solid fa-trash"></i> Hapus</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/chat_widget.php'; ?>
</body>

</html>