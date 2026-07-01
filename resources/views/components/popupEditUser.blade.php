<div id="editUserModal" class="overlay hidden">
    <div class="modal-content">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--primary);">manage_accounts</i>
            Edit User
        </div>
        <form id="editUserForm">
            @csrf
            <input type="hidden" name="id" id="edit-id">

            <div class="form-control">
                <label for="edit-type">Tipe</label>
                <select id="edit-type" name="type">
                    <option value="leader">Leader</option>
                    <option value="admin">Admin</option>
                    <option value="super">Super</option>
                </select>
            </div>

            <div class="form-control">
                <label for="edit-name">Nama</label>
                <input type="text" id="edit-name" name="name">
            </div>

            <div class="form-control">
                <label for="edit-username">Username</label>
                <input type="text" id="edit-username" name="username">
            </div>

            <div class="row" style="gap:0.75rem;">
                <div class="form-control" style="flex:1;">
                    <label for="edit-division">Divisi</label>
                    <select id="edit-division" name="division">
                        @foreach($divisions as $divisi)
                            <option value="{{ $divisi->nama }}">{{ $divisi->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control" style="flex:1;">
                    <label for="edit-team">Team</label>
                    <select id="edit-team" name="team[]" multiple style="width:100%">
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
            </div>

            <div class="form-control">
                <label for="edit-password">Password (biarkan kosong jika tidak diganti)</label>
                <input type="password" id="edit-password" name="password" placeholder="Password baru">
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-neutral" onclick="closeModal('editUserModal')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="material-symbols-rounded">save</i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function initEditUserSelect2() {
    if ($('#edit-type').hasClass('select2-hidden-accessible')) return;
    $('#edit-type, #edit-division').select2({ dropdownParent: $('#editUserModal .modal-content'), width: '100%' });
    $('#edit-team').select2({ dropdownParent: $('#editUserModal .modal-content'), width: '100%' });
}
$(document).ready(initEditUserSelect2);
</script>
