<div id="editUserModal" class="overlay hidden">
    <div class="modal-content">
        <h2 class="text-lg font-semibold mb-4">Edit Data User</h2>
        <form id="editUserForm">
            @csrf
            <input type="hidden" name="id" id="edit-id">

            <div class="form-control">
                <label for="edit-type">Tipe Akun</label>
                <select id="edit-type" name="type" class="select2 input w-full" data-placeholder="Pilih tipe akun">
                    <option></option>
                    <option value="admin">Admin</option>
                    <option value="leader">Leader</option>
                    <option value="super">Super</option>
                </select>
            </div>

            <div class="form-control">
                <label for="edit-name">Nama</label>
                <input type="text" id="edit-name" name="name" class="input w-full">
            </div>

            <div class="form-control">
                <label for="edit-username">Username</label>
                <input type="text" id="edit-username" name="username" class="input w-full">
            </div>

            <div class="form-control">
                <label for="edit-division">Divisi</label>
                <select id="edit-division" name="division" class="select2 input w-full" data-placeholder="Pilih divisi">
                    <option></option>
                    @foreach ($divisions as $division)
                    <option value="{{ $division->nama }}">{{ $division->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label for="edit-team">Team</label>
                <select id="edit-team" name="team[]" class="select2 input w-full" multiple="multiple" data-placeholder="Pilih team">
                    <option></option>
                    <option value="painting a">Painting A</option>
                    <option value="painting b">Painting B</option>
                    <option value="transmisi">Transmisi</option>
                    <option value="main line">Main Line</option>
                    <option value="sub engine">Sub Engine</option>
                    <option value="sub assy">Sub Assy</option>
                    <option value="inspeksi">Inspeksi</option>
                    <option value="mower collector">Mower Collector</option>
                    <option value="dst">DST</option>
                </select>
            </div>

            <div class="form-control">
                <label for="edit-password">Password Baru (Opsional)</label>
                <input type="password" id="edit-password" name="password" class="input w-full" placeholder="Biarkan kosong jika tidak ingin mengubah">
            </div>

            <div class="button-group mt-4">
                <button type="button" class="btn btn-neutral" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
