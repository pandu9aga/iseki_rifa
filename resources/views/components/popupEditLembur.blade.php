<div id="editLemburModal" class="overlay hidden">
    <div class="modal-content">
        <h2 class="text-lg font-semibold mb-4">Edit Data</h2>
        <form id="editLemburForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-lembur-id">

            <div class="form-control">
                <label class="block mb-2 font-semibold">Karyawan</label>
                {{-- <select id="edit-employee_id" class="form-control select2" disabled>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nama }} - {{ $emp->division->nama ?? '-' }}
                        </option>
                    @endforeach
                </select> --}}
                <input type="text" id="edit-employee_name" class="form-control w-full mb-3" disabled>
                <input type="hidden" id="edit-employee_id" class="form-control w-full mb-3" required>
            </div>

            <div class="form-control">
                <label class="block mb-2 font-semibold">Tanggal Lembur</label>
                <input type="date" id="edit-tanggal_lembur" class="form-control w-full mb-3" required>
            </div>

            <div class="row">
                <div class="col">
                    <label class="block mb-2 font-semibold">Jam Mulai</label>
                    <input type="time" id="edit-jam_mulai" class="form-control w-full mb-3">
                </div>
                <div class="col">
                    <label class="block mb-2 font-semibold">Jam Selesai</label>
                    <input type="time" id="edit-jam_selesai" class="form-control w-full mb-3">
                </div>
            </div>

            <div class="form-control">
                <label class="block mb-2 font-semibold">Durasi (Jam)</label>
                <input type="number" id="edit-durasi_lembur" class="form-control w-full mb-3" step="0.01">
            </div>

            <div class="form-control">
                <label class="block mb-2 font-semibold">Keterangan</label>
                <textarea id="edit-keterangan_lembur" class="form-control w-full mb-3"></textarea>
            </div>

            <div class="form-control">
                <label class="block mb-2 font-semibold">Makan</label>
                <select id="edit-makan_lembur" class="form-control w-full mb-3">
                    <option value="ya">ya</option>
                    <option value="tidak">tidak</option>
                </select>
            </div>

            <div class="button-group">
                <button type="button" onclick="closeEditModal('editLemburModal')" class="btn btn-secondary mt-2">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-800"
            onclick="closeEditModal('editLemburModal')">&times;</button>
    </div>
</div>
