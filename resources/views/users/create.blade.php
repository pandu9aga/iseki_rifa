@extends('layouts.app')

@section('title', 'Pegawai Baru')

@section('content')
<main>
    @include('components.popupDelete')

    <h1 class="text-2xl font-bold">User Baru</h1>
    <p class="text-sm flex w-full justify-end">Jumlah Data:&nbsp;<span id="jumlah-data">{{ old('nama') ? count(old('nama')) : 1 }}</span></p>


    <form action="{{ route('users.store') }}" method="POST" id="user-form" class="g-5">
        @csrf
        <section class="container-table">
            <table id="users-table">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="table-cell w-5">No</th>
                        <th class="table-cell w-10">Akun</th>
                        <th class="table-cell w-15">Nama</th>
                        <th class="table-cell w-15">Username</th>
                        <th class="table-cell w-10">Divisi</th>
                        <th class="table-cell w-25">Team</th>
                        <th class="table-cell w-15">Password</th>
                        <th class="table-cell w-5 sticky-col-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="number">1</td>
                        <td>
                            <select name="type[]" data-placeholder="Pilih type" class="select2 type" style="width: 100%">
                                <option></option>
                                <option value="leader">leader</option>
                                <option value="admin">admin</option>
                                <option value="super">super</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="nama[]" class="nama w-full" autocomplete="off" placeholder="Masukkan nama...">
                            <span id="error" class="text-red-500"></span>
                        </td>
                        <td>
                            <input type="text" name="username[]" class="nama w-full" autocomplete="off" placeholder="Masukkan username...">
                            <span id="error" class="text-red-500"></span>
                        </td>
                        <td>
                            <select name="divisi[]" data-placeholder="Pilih divisi" class="select2 divisi" style="width: 100%">
                                <option></option>
                                @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="team[0][]" class="select2 team-select w-full" multiple="multiple" data-placeholder="Pilih team" style="width: 100%">
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
                        </td>
                        <td>
                            <input type="password" name="password[]" class="password" autocomplete="off" placeholder="Masukkan password...">
                        </td>
                        <td class="sticky-col-right">
                            <button type="button" class="btn btn-icon danger delete-row">
                                <i class="material-symbols-rounded delete-row btn-danger">
                                    delete
                                </i>
                            </button>
                        </td>
                    </tr>
                    <tr id="row-button" class="hover-none">
                        <td colspan="8">
                            <button type="button" id="add-row" class="btn btn-secondary">
                                <i class="material-symbols-rounded btn-primary">
                                    add
                                </i>
                                Baris Baru
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p id="error-text" class="text-red-500 hidden"></p>

        <div class="">
            <button type="submit" id="submit-data" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</main>

<script>
    const tableBody = document.querySelector('#users-table tbody');
    const jumlahDataElement = document.getElementById('jumlah-data');

    // Fungsi untuk menghitung data
    function updateRowNumbers() {
        const rows = tableBody.querySelectorAll('tr:not(#row-button)');
        rows.forEach((row, index) => {
            const noCell = row.querySelector('td.number');
            if (noCell) noCell.textContent = index + 1;
        });
        updateJumlahData();
    }

    // Fungsi untuk update jumlah data
    function updateJumlahData() {
        const rows = tableBody.querySelectorAll('tr:not(#row-button)');
        jumlahDataElement.textContent = rows.length;
    }

    document.getElementById('user-form').addEventListener('submit', function(e) {
        const errorText = document.getElementById('error-text');
        const typeInputs = document.querySelectorAll('input[name="type[]"]');
        const namaInputs = document.querySelectorAll('input[name="nama[]"]');
        const usernameInputs = document.querySelectorAll('input[name="username[]"]');
        const divisiInputs = document.querySelectorAll('select[name="divisi[]"]');
        const teamInputs = document.querySelectorAll('select[name^="team["]');
        const passwordInputs = document.querySelectorAll('input[name="password[]"]');

        let isValid = true;
        let errorMessage = '';

        typeInputs.forEach((select) => {
            if (!select.value.trim()) {
                isValid = false;
                errorMessage = 'Data tidak boleh ada yang kosong. Pastikan kembali data setiap baris.';
            }
        });

        namaInputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
                errorMessage = 'Data tidak boleh ada yang kosong. Pastikan kembali data setiap baris.';
            }
        });

        usernameInputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
                errorMessage = 'Data tidak boleh ada yang kosong. Pastikan kembali data setiap baris.';
            }
        });

        document.querySelectorAll('#users-table tbody tr').forEach(row => {
            if (row.id === 'row-button') return; // lewati baris tombol

            const type = row.querySelector('select[name="type[]"]')?.value?.trim();
            const divisi = row.querySelector('select[name="divisi[]"]')?.value?.trim();
            const teamSelect = row.querySelector('select[name^="team["]');
            const selectedTeams = Array.from(teamSelect?.selectedOptions || []).map(opt => opt.value).filter(v => v);

            if (type !== 'admin' && !divisi) {
                isValid = false;
                errorMessage = 'Divisi wajib diisi, kecuali jika tipe akun adalah admin.';
            }

            if (type !== 'admin' && selectedTeams.length === 0) {
                isValid = false;
                errorMessage = 'Team wajib diisi, kecuali jika tipe akun adalah admin.';
            }
        });

        passwordInputs.forEach((input) => {
            const length = input.value.length;
            if (length > 0 && length < 8) {
                isValid = false;
                errorMessage = 'Password minimal 8 karakter jika diisi.';
            }
        });

        if (!isValid) {
            e.preventDefault();
            errorText.classList.remove('hidden');
            errorText.textContent = errorMessage;
        } else if (namaInputs.length <= 0) {
            e.preventDefault();
            errorText.classList.remove('hidden');
            errorText.textContent = 'Minimal 1 baris data untuk disimpan.';
        } else {
            errorText.classList.add('hidden');
            errorText.textContent = '';
        }
    });

    // Fungsi untuk membuat baris baru
    function addRow() {
        const row = document.createElement('tr');
        const rowIndex = tableBody.querySelectorAll('tr:not(#row-button)').length;
        row.innerHTML = `
            <td class="number"></td>
            <td>
                <select name="type[]" data-placeholder="Pilih type" class="select2 type" style="width: 100%">
                    <option></option>
                    <option value="leader">leader</option>
                    <option value="admin">admin</option>
                    <option value="super">super</option>
                </select>
            </td>
            <td>
                <input type="text" name="nama[]" class="nama w-full" autocomplete="off" placeholder="Masukkan nama...">
                <span id="error" class="text-red-500"></span>
            </td>
            <td>
                <input type="text" name="username[]" class="nama w-full" autocomplete="off" placeholder="Masukkan username...">
                <span id="error" class="text-red-500"></span>
            </td>
            <td>
                <select name="divisi[]" data-placeholder="Pilih divisi" class="select2 divisi" style="width: 100%">
                    <option></option>
                    @foreach ($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->nama }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="team[${rowIndex}][]" class="select2 team-select w-full" multiple="multiple" data-placeholder="Pilih team">
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
            </td>
            <td>
                <input type="password" name="password[]" class="password" autocomplete="off" placeholder="Masukkan password...">
            </td>
            <td class="sticky-col-right">
                <button type="button" class="btn btn-icon danger delete-row">
                    <i class="material-symbols-rounded delete-row btn-danger">
                        delete
                    </i>
                </button>
            </td>
        `;
        tableBody.insertBefore(row, document.getElementById('row-button'));
        window.updateRowNumbers();

        // Fokus ke row baru
        const inputNamaBaru = row.querySelector('input.nama');
        if (inputNamaBaru) {
            setTimeout(() => inputNamaBaru.focus(), 50);
        }

        $(row).find('.select2').select2();
        $(row).find('.team-select').select2({
            width: '100%'
        });
    }

    document.getElementById('add-row').addEventListener('click', addRow);

    tableBody.addEventListener('keydown', function(e) {
        if (e.target.matches('input.nama') && e.key === 'Enter') {
            e.preventDefault();

            const currentRow = e.target.closest('tr');
            if (!currentRow) return;

            const nextRow = currentRow.nextElementSibling;
            if (nextRow && nextRow.id !== 'row-button') {
                const nextInput = nextRow.querySelector('input.nama');
                if (nextInput) {
                    nextInput.focus();
                    return;
                }
            }

            addRow();
        }
    });

    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.delete-row')) {
            const row = e.target.closest('tr');
            if (row && row.id !== 'row-button') {
                showDeletePopup(row);
            }
        }
    });

    let rowToDelete = null;

    function showDeletePopup(row) {
        rowToDelete = row;
        document.getElementById('popupDelete').classList.replace('hidden', 'flex');
    }

    function hideDeletePopup() {
        rowToDelete = null;
        document.getElementById('popupDelete').classList.replace('flex', 'hidden');
    }

    document.getElementById('cancelDelete').addEventListener('click', hideDeletePopup);
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (rowToDelete) {
            rowToDelete.remove();
            updateRowNumbers();
        }
        hideDeletePopup();
    });
</script>
@endsection
