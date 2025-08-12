@extends('layouts.app')

@section('title', 'Pegawai Baru')

@section('content')
<main>
    @include('components.popupDelete')

    <h1 class="text-2xl font-bold">Pegawai Baru</h1>
    <p class="text-sm flex w-full justify-end">Jumlah Data:&nbsp;<span id="jumlah-data">{{ old('nama') ? count(old('nama')) : 1 }}</span></p>


    <form action="{{ route('employees.store') }}" method="POST" id="employee-form" class="g-5">
        @csrf
        <section class="container-table">
            <table id="employees-table">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="table-cell w-4">No</th>
                        <th class="table-cell w-31">Nama</th>
                        <th class="table-cell w-16">Nik</th>
                        <th class="table-cell w-16">Status</th>
                        <th class="table-cell w-16">Divisi</th>
                        <th class="table-cell w-15">Tim</th>
                        <th class="table-cell w-4 sticky-col-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="number">1</td>
                        <td>
                            <input type="text" name="nama[]" class="nama w-full" autocomplete="off" placeholder="Masukkan nama...">
                            <span id="error" class="text-red-500"></span>
                        </td>
                        <td>
                            <input type="text" name="nik[]" class="nik" autocomplete="off" placeholder="Masukkan nik...">
                            <span id="error" class="text-red-500"></span>
                        </td>
                        <td>
                            <select name="status[]" data-placeholder="Pilih status" class="select2 status" style="width: 100%">
                                <option></option>
                                <option value="Contract">Contract</option>
                                <option value="Permanent">Permanent</option>
                            </select>
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
                            <input type="text" name="team[]" class="team" autocomplete="off" placeholder="Masukkan team...">
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
                        <td colspan="7">
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
    const tableBody = document.querySelector('#employees-table tbody');
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

    document.getElementById('employee-form').addEventListener('submit', function(e) {
        const errorText = document.getElementById('error-text');
        const namaInputs = document.querySelectorAll('input[name="nama[]"]');
        const nikInputs = document.querySelectorAll('input[name="nik[]"]');
        const statusInputs = document.querySelectorAll('select[name="status[]"]');
        const divisiInputs = document.querySelectorAll('select[name="divisi[]"]');
        const teamInputs = document.querySelectorAll('input[name="team[]"]');

        let isValid = true;

        namaInputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
            }
        });

        nikInputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
            }
        });

        statusInputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
            }
        });

        divisiInputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
            }
        });

        // teamInputs.forEach((select) => {
        //     if (!select.value) {
        //         isValid = false;
        //     }
        // });

        if (!isValid) {
            e.preventDefault();
            errorText.classList.remove('hidden');
            errorText.textContent = 'Data tidak boleh ada yang kosong. Pastikan kembali data setiap baris.';
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
        row.innerHTML = `
            <td class="number"></td>
            <td>
                <input type="text" name="nama[]" class="nama w-full" autocomplete="off" placeholder="Masukkan nama..." list="nama_datalist">
                <p class="error-help hidden"></p>
            </td>
            <td>
                <input type="text" name="nik[]" class="nik" autocomplete="off" placeholder="Masukkan nik..." list="nik_datalist">
                <p class="error-help hidden"></p>
            </td>
            <td>
                <select name="status[]" data-placeholder="Pilih status" class="select2 status" style="width: 100%">
                    <option></option>
                    <option value="Contract">Contract</option>
                    <option value="Permanent">Permanent</option>
                </select>
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
                <input type="text" name="team[]" class="team" autocomplete="off" placeholder="Masukkan team...">
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
