<div id="editDateModal" class="overlay hidden">
    <div class="modal-content">
        <h2 class="text-lg font-semibold mb-4">Edit Data Tanggal</h2>
        <form id="editDateForm">
            @csrf
            <input type="hidden" name="id" id="edit-id">

            <div class="form-control">
                <label for="edit-tanggal">Tanggal</label>
                <input type="date" id="edit-tanggal" name="tanggal" class="input w-full">
            </div>

            <div class="form-control">
                <label for="edit-jenis">Jenis Tanggal</label>
                <select id="edit-jenis" name="jenis_tanggal" class="select2 input w-full" data-placeholder="Pilih tipe akun">
                    <option></option>
                    <option value="libur nasional">libur nasional</option>
                    <option value="cuti perusahaan">cuti perusahaan</option>
                    <option value="libur masuk">libur masuk</option>
                    <option value="libur pengganti">libur pengganti</option>
                </select>
            </div>

            <div class="button-group mt-4">
                <button type="button" class="btn btn-neutral" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
