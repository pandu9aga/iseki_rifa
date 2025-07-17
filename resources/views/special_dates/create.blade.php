@extends('layouts.app')

@section('title', 'Tanggal Khusus')

@section('content')
<main>
    @include('components.popupDelete')

    <h1 class="text-2xl font-bold">Tanggal Khusus</h1>
    <p class="text-sm flex w-full justify-end">Jumlah Data:&nbsp;<span id="jumlah-data">{{ old('nama') ? count(old('nama')) : 1 }}</span></p>


    <form action="{{ route('dates.store') }}" method="POST" id="date-form" class="g-5">
        @csrf
        <section class="container-table">
            <table id="dates-table">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="table-cell w-5">No</th>
                        <th class="table-cell w-45">Tanggal</th>
                        <th class="table-cell w-45">Jenis Tanggal</th>
                        <th class="table-cell w-5 sticky-col-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="number">1</td>
                        <td>
                            <input type="date" name="tanggal[]" class="nama w-full" autocomplete="off" placeholder="Masukkan tanggal...">
                            <span id="error" class="text-red-500"></span>
                        </td>
                        <td>
                            <select name="jenis_tanggal[]" data-placeholder="Pilih jenis tanggal" class="select2 type" style="width: 100%">
                                <option></option>
                                <option value="libur nasional">libur nasional</option>
                                <option value="cuti perusahaan">cuti perusahaan</option>
                                <option value="libur masuk">libur masuk</option>
                                <option value="libur pengganti">libur pengganti</option>
                            </select>
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
                        <td colspan="6">
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
    const tableBody = document.querySelector('#dates-table tbody');
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

    document.getElementById('date-form').addEventListener('submit', function(e) {
        const errorText = document.getElementById('error-text');
        const tanggalInputs = document.querySelectorAll('input[name="tanggal[]"]');
        const jenisInputs = document.querySelectorAll('input[name="jenis_tanggal[]"]');

        let isValid = true;
        let errorMessage = '';

        tanggalInputs.forEach((select) => {
            if (!select.value.trim()) {
                isValid = false;
                errorMessage = 'Data tidak boleh ada yang kosong. Pastikan kembali data setiap baris.';
            }
        });

        jenisInputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
                errorMessage = 'Data tidak boleh ada yang kosong. Pastikan kembali data setiap baris.';
            }
        });

        if (!isValid) {
            e.preventDefault();
            errorText.classList.remove('hidden');
            errorText.textContent = errorMessage;
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
                <input type="date" name="tanggal[]" class="nama w-full" autocomplete="off" placeholder="Masukkan tanggal...">
                <span id="error" class="text-red-500"></span>
            </td>
            <td>
                <select name="jenis_tanggal[]" data-placeholder="Pilih jenis tanggal" class="select2 type" style="width: 100%">
                    <option></option>
                    <option value="libur nasional">libur nasional</option>
                    <option value="cuti perusahaan">cuti perusahaan</option>
                    <option value="libur masuk">libur masuk</option>
                    <option value="libur pengganti">libur pengganti</option>
                </select>
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
