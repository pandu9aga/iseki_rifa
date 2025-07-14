@extends('layouts.app')

@section('content')

<main>
    @include('components.popupEditEmployee')
    <section class="title-button d-flex flex-row">
        <h1 class="text-2xl font-bold">Data Pegawai</h1>
        <section class="btn-group d-flex flex-row">
            <a href="{{ url('/employees/new') }}" class="btn btn-primary">
                Tambah Data
                <i class="material-symbols-rounded">
                    add
                </i>
            </a>
        </section>
    </section>

    <section id="summary" class="flex w-full text-sm items-center">
        <div id="jumlah-by-divisi" class="w-full">
            @foreach ($divisions as $division)
            <p>{{ $division->nama }}: {{ $division->employees_count }}</p>
            @endforeach
        </div>

        <p id="jumlah-data" class="flex justify-end mb-2 text-sm">
            Jumlah Data: {{ count($employees) }}
        </p>
    </section>

    @csrf
    <section class="container-table">
        <table id="employees-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Divisi</th>
                    <th>Tim</th>
                    <th rowspan="2" class="sticky-col-right">Aksi</th>
                </tr>
                <tr>
                    <th><input class="filter" id="filter-nama" type="text" placeholder="Cari Nama" /></th>
                    <th>
                        <select name="status" class="select2 filter" data-placeholder="Pilih status" data-allow-clear="true" style="width: 100%" id="filter-status">
                            <option></option>
                            <option value="Permanent">Permanent</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </th>
                    <th>
                        <select name="divisi" id="filter-divisi" class="select2 filter" data-placeholder="Pilih divisi" data-allow-clear="true" style="width: 100%">
                            <option></option>
                            @foreach ($divisions as $division)
                            <option value="{{ $division->nama }}">{{ $division->nama }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th><input class="filter" id="filter-team" type="text" placeholder="Cari Tim" /></th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $index => $employee)
                <tr data-id="{{ $employee->id }}">
                    <td class="number">{{ $index + 1 }}</td>
                    <td>{{ $employee->nama ?? '-' }}</td>
                    <td>{{ $employee->status ?? '-' }}</td>
                    <td>{{ $employee->division->nama ?? '-' }}</td>
                    <td>{{ $employee->team ?? '-' }}</td>
                    <td class="sticky-col-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-icon edit-row">
                                <i class="material-symbols-rounded btn-primary">
                                    edit_square
                                </i>
                            </button>
                            <button type="button" class="btn btn-icon"
                                onclick="showDeletePopup(this.closest('tr'))" title="Hapus">
                                <i class="material-symbols-rounded delete-row btn-danger">
                                    delete
                                </i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach

                <tr id="no-data-row" class="hidden text-center">
                    <td colspan="8" class="text-gray-500 py-4">Data tidak ditemukan</td>
                </tr>
            </tbody>
        </table>

    </section>
    @include('components.popupDelete')
</main>

<script>
    document.getElementById('confirmDelete').addEventListener('click', function() {
        setTimeout(() => updateJumlahData('employees-table', 'jumlah-data'), 300);
    });

    // Function untuk delete
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

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (rowToDelete) {
            const id = rowToDelete.getAttribute('data-id');
            fetch(`/iseki_rifa/public/employees/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (response.ok) {
                        rowToDelete.remove();
                        updateRowNumbers();
                        hideDeletePopup();
                    } else {
                        alert('Gagal menghapus data');
                    }
                })
                .catch(() => alert('Error saat menghapus data'));
        }
    });

    // Function untuk edit
    const editModal = document.getElementById('editEmployeeModal');
    const editForm = document.getElementById('editEmployeeForm');
    const editNama = document.getElementById('edit-nama');
    const editStatus = document.getElementById('edit-status');
    const editDivisi = document.getElementById('edit-divisi');
    const editTeam = document.getElementById('edit-team');
    const editId = document.getElementById('edit-id');

    // Show Edit Modal
    document.querySelectorAll('.edit-row').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const nama = row.children[1].textContent;
            const status = row.children[2].textContent;
            const divisi = row.children[3].textContent;
            const team = row.children[4].textContent === '-' ? '' : row.children[4].textContent;

            editId.value = id;
            editNama.value = nama;
            editStatus.value = status;
            editDivisi.value = divisi;
            editTeam.value = team;

            showModal('editEmployeeModal');

            $('#edit-status').val(status).trigger('change');
            $('#edit-divisi').val(divisi).trigger('change');
        });
    });

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.replace('hidden', 'flex');
    }

    // Close modal
    function closeEditModal() {
        editModal.classList.replace('flex', 'hidden');
    }

    // Handle form submit
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const id = editId.value;
        const data = {
            nama: editNama.value,
            status: editStatus.value,
            divisi: editDivisi.value,
            team: editTeam.value,
        };

        fetch(`/iseki_rifa/public/employees/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Gagal menyimpan perubahan');
                }
            });
    });

    // Update nomor urut di kolom No setelah hapus baris
    function updateRowNumbers() {
        const numbers = document.querySelectorAll('#employees-table tbody tr .number');
        numbers.forEach((cell, index) => {
            cell.textContent = index + 1;
        });
    }
</script>
@endsection