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
                    <select id="edit-employee-status" name="status">
                        <option value="Direct">Direct</option>
                        <option value="Non Direct">Non Direct</option>
                    </select>
                </div>
                <div class="form-control" style="flex:1;">
                    <label for="edit-employee-divisi">Divisi</label>
                    <select id="edit-employee-divisi" name="divisi">
                        @foreach($divisions as $divisi)
                            <option value="{{ $divisi->nama }}">{{ $divisi->nama }}</option>
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

            <div class="form-control">
                <label for="edit-employee-photo">Foto Pegawai</label>
                <div class="flex items-center gap-3 my-2" id="edit-photo-preview-container" style="display:flex; align-items:center; gap:0.75rem; margin: 0.5rem 0;">
                    <img id="edit-photo-preview" src="" alt="Foto Employee" class="hidden" style="width:60px; height:60px; object-fit:cover; border-radius:50%; border:1px solid #d1d5db;">
                    <div id="edit-photo-placeholder" style="width:60px; height:60px; border-radius:50%; background:#f3f4f6; border:1px solid #d1d5db; display:flex; align-items:center; justify-content:center; color:#9ca3af;">
                        <i class="material-symbols-rounded">person</i>
                    </div>
                </div>
                <input type="file" id="edit-employee-photo" name="photo_employee" accept="image/*">
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
