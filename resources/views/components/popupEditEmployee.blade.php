<div id="editEmployeeModal" class="overlay hidden">
    <div class="modal-content">
        <h2 class="text-lg font-semibold mb-4">Edit Data</h2>
        <form id="editEmployeeForm">
            @csrf
            <input type="hidden" name="id" id="edit-id">

            <div class="form-control">
                <label for="edit-nama">Nama</label>
                <input type="text" id="edit-nama" name="nama" class="input w-full">
            </div>

            <div class="form-control">
                <label for="edit-status">Status</label>
                <select id="edit-status" name="status" data-placeholder="Pilih status" class="select2 input w-full">
                    <option></option>
                    <option value="Permanent">Permanent</option>
                    <option value="Contract">Contract</option>
                </select>
            </div>

            <div class="form-control">
                <label for="edit-divisi">Divisi</label>
                <select id="edit-divisi" name="divisi" data-placeholder="Pilih divisi" class="select2 input w-full">
                    <option></option>
                    @foreach ($divisions as $division)
                    <option value="{{ $division->nama }}">{{ $division->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label for="edit-team">Team</label>
                <input type="text" id="edit-team" name="team" class="input w-full">
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-neutral" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>