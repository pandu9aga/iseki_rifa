@extends('layouts.app')

@section('content')

<main>
    @include('components.popupEditUser')
    <section class="title-button d-flex flex-row">
        <h1 class="text-2xl font-bold">Data User</h1>
        <section class="btn-group d-flex flex-row">
            <a href="{{ url('/users/new') }}" class="btn btn-primary">
                Tambah Data
                <i class="material-symbols-rounded">
                    add
                </i>
            </a>
        </section>
    </section>

    <section id="summary" class="flex w-full text-sm items-center">
        <div id="jumlah-by-divisi" class="w-full">
            @foreach($types as $type)
                <p>{{ ucfirst($type->type) }}: {{ $type->total }}</p>
            @endforeach
        </div>

        <p id="jumlah-data" class="flex justify-end mb-2 text-sm">
            Jumlah Data: {{ count($users) }}
        </p>
    </section>

    @csrf
    <section class="container-table">
        <table id="users-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th>Akun</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Divisi</th>
                    <th rowspan="2" class="sticky-col-right">Aksi</th>
                </tr>
                <tr>
                    <th>
                        <select name="type" id="filter-type" class="select2 filter" data-placeholder="Pilih tipe akun" data-allow-clear="true" style="width: 100%">
                            <option></option>
                            <option value="admin">admin</option>
                            <option value="leader">leader</option>
                        </select>
                    </th>
                    <th><input class="filter" id="filter-nama" type="text" placeholder="Cari Nama" style="width: 100%"/></th>
                    <th><input class="filter" id="filter-username" type="text" placeholder="Cari Username" style="width: 100%"/></th>
                    <th>
                        <select name="divisi" id="filter-divisi" class="select2 filter" data-placeholder="Pilih divisi" data-allow-clear="true" style="width: 100%">
                            <option></option>
                            @foreach ($divisions as $division)
                            <option value="{{ $division->nama }}">{{ $division->nama }}</option>
                            @endforeach
                        </select>
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $index => $user)
                <tr data-id="{{ $user->id }}">
                    <td class="number">{{ $index + 1 }}</td>
                    <td>{{ $user->type ?? '-' }}</td>
                    <td>{{ $user->name ?? '-' }}</td>
                    <td>{{ $user->username ?? '-' }}</td>
                    <td>{{ $user->division ?? '-' }}</td>
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
        setTimeout(() => updateJumlahData('users-table', 'jumlah-data'), 300);
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
            fetch(`/iseki_rifa/public/users/${id}`, {
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

    // Function untuk edit user
    const editModal = document.getElementById('editUserModal');
    const editForm = document.getElementById('editUserForm');
    const editType = document.getElementById('edit-type');
    const editName = document.getElementById('edit-name');
    const editUsername = document.getElementById('edit-username');
    const editDivision = document.getElementById('edit-division');
    const editId = document.getElementById('edit-id');

    // Show Edit Modal
    document.querySelectorAll('.edit-row').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const type = row.children[1].textContent.trim();
            const name = row.children[2].textContent.trim();
            const username = row.children[3].textContent.trim();
            const division = row.children[4].textContent.trim();

            editId.value = id;
            editType.value = type;
            editName.value = name;
            editUsername.value = username;
            editDivision.value = division;

            showModal('editUserModal');

            $('#edit-type').val(type).trigger('change');
            $('#edit-division').val(division).trigger('change');
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
            name: editName.value,
            username: editUsername.value,
            type: editType.value,
            division: editDivision.value,
        };

        const password = document.getElementById('edit-password').value;
        if (password.trim() !== '') {
            data.password = password;
        }

        fetch(`/iseki_rifa/public/users/${id}`, {
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