<div id="editModal" class="overlay hidden">
    <div class="modal-content">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--primary);">edit</i>
            Edit Data Izin
        </div>
        <form id="editForm">
            @csrf
            <input type="hidden" name="id" id="edit-id">

            <div class="form-control">
                <label for="edit-tanggal">Tanggal</label>
                <input type="date" id="edit-tanggal" name="tanggal">
            </div>

            <div class="form-control">
                <label for="edit-jenis">Jenis Izin</label>
                <select id="edit-jenis" name="jenis">
                    <option value="Cuti">Cuti</option>
                    <option value="Cuti Setengah Hari Pagi">Cuti Setengah Hari Pagi</option>
                    <option value="Cuti Setengah Hari Siang">Cuti Setengah Hari Siang</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin Keluar">Izin Keluar</option>
                    <option value="Pulang Cepat">Pulang Cepat</option>
                    <option value="Pulang Cepat Dengan Surat">Pulang Cepat Dengan Surat</option>
                    <option value="Absen">Absen</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Cuti Khusus">Cuti Khusus</option>
                    <option value="Serikat">Serikat</option>
                    <option value="Salah Fingerprint">Salah Fingerprint</option>
                </select>
            </div>

            <div class="form-control">
                <label for="edit-keterangan">Keterangan</label>
                <input type="text" id="edit-keterangan" name="keterangan">
            </div>

            <div class="row" style="gap:0.75rem;">
                <div class="form-control" style="flex:1;">
                    <label for="edit-jam-masuk">Jam Masuk</label>
                    <input type="time" id="edit-jam-masuk" name="jam_masuk">
                </div>
                <div class="form-control" style="flex:1;">
                    <label for="edit-jam-keluar">Jam Keluar</label>
                    <input type="time" id="edit-jam-keluar" name="jam_keluar">
                </div>
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-neutral" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="material-symbols-rounded">save</i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $('#edit-jenis').select2({
        dropdownParent: $('#editModal .modal-content')
    });
</script>
