<div id="editDateModal" class="overlay hidden">
    <div class="modal-content" style="max-width:24rem;">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--primary);">calendar_month</i>
            Edit Tanggal
        </div>
        <form id="editDateForm">
            @csrf
            <input type="hidden" name="id" id="edit-date-id">

            <div class="form-control">
                <label for="edit-date-tanggal">Tanggal</label>
                <input type="date" id="edit-date-tanggal" name="tanggal">
            </div>

            <div class="form-control">
                <label for="edit-date-jenis">Jenis Tanggal</label>
                <select id="edit-date-jenis" name="jenis_tanggal">
                    <option value="Libur">Libur</option>
                    <option value="Libur Bersama">Libur Bersama</option>
                </select>
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-neutral" onclick="closeModal('editDateModal')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="material-symbols-rounded">save</i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
