<div id="editLemburModal" class="overlay hidden">
    <div class="modal-content">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--primary);">edit</i>
            Edit Data Lembur
        </div>
        <form id="editLemburForm">
            @csrf
            <input type="hidden" name="id" id="edit-lembur-id">

            <div class="form-control">
                <label for="edit-lembur-pegawai">Pegawai</label>
                <select id="edit-lembur-pegawai" name="employee_id">
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row" style="gap:0.75rem;">
                <div class="form-control" style="flex:2;">
                    <label for="edit-lembur-tanggal">Tanggal</label>
                    <input type="date" id="edit-lembur-tanggal" name="tanggal">
                </div>
                <div class="form-control" style="flex:1;">
                    <label for="edit-lembur-jam">Jam</label>
                    <input type="time" id="edit-lembur-jam" name="waktu">
                </div>
                <div class="form-control" style="flex:1;">
                    <label for="edit-lembur-durasi">Durasi (jam)</label>
                    <input type="number" id="edit-lembur-durasi" name="durasi" step="0.5">
                </div>
            </div>

            <div class="form-control">
                <label for="edit-lembur-pekerjaan">Pekerjaan</label>
                <select id="edit-lembur-pekerjaan" name="pekerjaan">
                    <option value="Produksi">Produksi</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Kaizen">Kaizen</option>
                    <option value="5S">5S</option>
                    <option value="Pekerjaan Leader/PIC Lembur">Leader/PIC</option>
                </select>
            </div>

            <div class="form-control">
                <label for="edit-lembur-makan">Makan</label>
                <input type="text" id="edit-lembur-makan" name="makan">
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-neutral" onclick="closeModal('editLemburModal')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="material-symbols-rounded">save</i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $('#edit-lembur-pegawai').select2({
        dropdownParent: $('#editLemburModal .modal-content')
    });
</script>
