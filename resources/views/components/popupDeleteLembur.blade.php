<div id="popupDeleteLembur" class="overlay hidden">
    <div class="modal-content" style="max-width:24rem;">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--danger);">warning</i>
            Konfirmasi Hapus
        </div>
        <p style="color:var(--text-secondary);">Apakah kamu yakin ingin menghapus data lembur ini?</p>
        <div class="button-group">
            <button class="btn btn-neutral" onclick="closeModal('popupDeleteLembur')">Batal</button>
            <button id="confirmDeleteLembur" class="btn btn-danger">
                <i class="material-symbols-rounded">delete</i>
                Hapus
            </button>
        </div>
    </div>
</div>
