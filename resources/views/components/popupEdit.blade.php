<div id="editModal" class=" overlay hidden">
    <div class="modal-content">
        <h2 class="text-lg font-semibold mb-4">Edit Data Izin</h2>
        <form id="editForm">
            @csrf
            <input type="hidden" name="id" id="edit-id">

            <div class="form-control">
                <label for="edit-tanggal">Tanggal</label>
                <input type="date" id="edit-tanggal" name="tanggal" class="input w-full">
            </div>

            <div class="form-control">
                <label for="edit-jenis">Jenis Izin</label>
                <select id="edit-jenis" name="jenis" class="select2 input w-full">
                    <option value="Cuti">Cuti</option>
                    <option value="Cuti Setengah Hari Pagi">Cuti Setengah Hari Pagi</option>
                    <option value="Cuti Setengah Hari Siang">Cuti Setengah Hari Siang</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin Keluar">Izin Keluar</option>
                    <option value="Pulang Cepat">Pulang Cepat</option>
                    <option value="Absen">Absen</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Cuti Khusus">Cuti Khusus</option>
                    <option value="Serikat">Serikat</option>
                </select>
            </div>

            <div class="form-control">
                <label for="edit-keterangan">Keterangan</label>
                <input type="text" id="edit-keterangan" name="keterangan" class="input w-full">
            </div>

            <div class="form-control">
                <label for="edit-jam-masuk">Jam Masuk</label>
                <input type="time" id="edit-jam-masuk" name="jam_masuk" class="input w-full">
            </div>

            <div class="form-control">
                <label for="edit-jam-keluar">Jam Keluar</label>
                <input type="time" id="edit-jam-keluar" name="jam_keluar" class="input w-full">
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-neutral" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
    .select2-container {
        z-index: 10000 !important;
    }
</style>

<script>
    $('#edit-jenis').select2({
        dropdownParent: $('#editModal .modal-content')
    });
</script>