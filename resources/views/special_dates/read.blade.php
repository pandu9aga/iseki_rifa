@extends('layouts.app')

@section('content')

<main>
    @include('components.popupEditDate')
    <section class="title-button d-flex flex-row">
        <h1 class="text-2xl font-bold">Data Tanggal</h1>
        <section class="btn-group d-flex flex-row">
            <a href="{{ url('/dates/new') }}" class="btn btn-primary">
                Tambah Data
                <i class="material-symbols-rounded">
                    add
                </i>
            </a>
        </section>
    </section>

    <section id="summary" class="flex w-full text-sm items-center">
        <p id="jumlah-data" class="flex justify-end mb-2 text-sm">
            Jumlah Data: {{ count($dates) }}
        </p>
    </section>

    @csrf
    <section class="container-table table-scroll-wrapper">
        <table id="dates-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th>Tanggal</th>
                    <th>Jenis Tanggal</th>
                    <th rowspan="2" class="sticky-col-right">Aksi</th>
                </tr>
                <tr>
                    <th>
                        <select class="select2 filter" name="tanggal" id="filter-tanggal" data-placeholder="Pilih jenis tanggal" data-allow-clear="true" style="width: 100%">
                            <option></option>
                            @for ($i = date('Y') + 1; $i >= 2020; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </th>
                    <th>
                        <select name="jenis_tanggal" id="filter-jenis" class="select2 filter" data-placeholder="Pilih jenis tanggal" data-allow-clear="true" style="width: 100%">
                            <option></option>
                            <option value="libur nasional">libur nasional</option>
                            <option value="cuti perusahaan">cuti perusahaan</option>
                            <option value="libur masuk">libur masuk</option>
                            <option value="libur pengganti">libur pengganti</option>
                        </select>
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($dates as $index => $date)
                <tr data-id="{{ $date->id }}">
                    <td class="number">{{ $index + 1 }}</td>
                    <td>{{ $date->tanggal ?? '-' }}</td>
                    <td>{{ $date->jenis_tanggal ?? '-' }}</td>
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
                    <td colspan="4" class="text-gray-500 py-4">Data tidak ditemukan</td>
                </tr>
            </tbody>
        </table>

    </section>
    @include('components.popupDelete')
</main>

<script>
    document.getElementById('confirmDelete').addEventListener('click', function() {
        setTimeout(() => updateJumlahData('dates-table', 'jumlah-data'), 300);
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
            fetch(`/iseki_rifa/public/dates/${id}`, {
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
    const editModal = document.getElementById('editDateModal');
    const editForm = document.getElementById('editDateForm');
    const editTanggal = document.getElementById('edit-tanggal');
    const editJenis = document.getElementById('edit-jenis');
    const editId = document.getElementById('edit-id');

    // Show Edit Modal
    document.querySelectorAll('.edit-row').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const tanggal = row.children[1].textContent.trim();
            const jenis_tanggal = row.children[2].textContent.trim();

            editId.value = id;
            editTanggal.value = tanggal;
            editJenis.value = jenis_tanggal;

            showModal('editDateModal');

            $('#edit-tanggal').val(tanggal).trigger('change');
            $('#edit-jenis').val(jenis_tanggal).trigger('change');
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
            tanggal: editTanggal.value,
            jenis_tanggal: editJenis.value,
        };

        fetch(`/iseki_rifa/public/dates/${id}`, {
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
        const numbers = document.querySelectorAll('#dates-table tbody tr .number');
        numbers.forEach((cell, index) => {
            cell.textContent = index + 1;
        });
    }
</script>
@endsection