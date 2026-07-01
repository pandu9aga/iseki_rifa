<div id="editEmployeeModal" class="overlay hidden">
    <div class="modal-content">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--primary);">badge</i>
            Edit Pegawai
        </div>
        <form id="editEmployeeForm">
            @csrf
            <input type="hidden" name="id" id="edit-employee-id">

            <div class="form-control">
                <label for="edit-employee-nama">Nama</label>
                <input type="text" id="edit-employee-nama" name="nama">
            </div>

            <div class="form-control">
                <label for="edit-employee-nik">NIK</label>
                <input type="text" id="edit-employee-nik" name="nik">
            </div>

            <div class="row" style="gap:0.75rem;">
                <div class="form-control" style="flex:1;">
                    <label for="edit-employee-status">Status</label>
                    <select id="edit-employee-status" name="is_active">
                        <option value="1">Aktif</option>
                        <option value="0">Non-aktif</option>
                    </select>
                </div>
                <div class="form-control" style="flex:1;">
                    <label for="edit-employee-divisi">Divisi</label>
                    <select id="edit-employee-divisi" name="divisi_id">
                        @foreach($divisions as $divisi)
                            <option value="{{ $divisi->id }}">{{ $divisi->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-control">
                <label for="edit-employee-team">Team</label>
                <input type="text" id="edit-employee-team" name="team" placeholder="Nama team">
            </div>

            <div class="form-control">
                <label for="edit-employee-password">Password (biarkan kosong jika tidak diganti)</label>
                <input type="password" id="edit-employee-password" name="password" placeholder="Password baru">
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-neutral" onclick="closeModal('editEmployeeModal')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="material-symbols-rounded">save</i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
