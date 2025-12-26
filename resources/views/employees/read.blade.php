@extends('layouts.app')

@section('content')
    <main>
        @include('components.popupEditEmployee')
        @include('components.popupDelete')

        <section class="title-button d-flex flex-row justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold">Data Pegawai</h1>
                @if (isset($tahun))
                    <p class="text-sm text-gray-600 mt-1">Nilai Tahun: {{ $tahun }}</p>
                @endif
            </div>
            <section class="btn-group d-flex flex-row gap-2">
                <a href="{{ url('/employees/new') }}" class="btn btn-primary">
                    Tambah Data
                    <i class="material-symbols-rounded">add</i>
                </a>
                <a href="{{ route('penilaian.index') }}" class="btn btn-primary">
                    Penilaian Tahunan
                    <i class="material-symbols-rounded">grading</i>
                </a>
            </section>
        </section>

        <!-- 🔸 FILTER TAHUN -->
        <form method="GET" class="mb-4 flex gap-3 flex-wrap bg-gray-50 p-3 rounded">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tahun Penilaian</label>
                <select name="tahun" class="form-select mt-1" onchange="this.form.submit()">
                    @foreach ($tahunOptions as $opt)
                        <option value="{{ $opt }}" @selected($opt == $tahun)>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <section id="summary" class="flex w-full text-sm items-center mb-4">
            <div id="jumlah-by-divisi" class="w-full">
                @foreach ($divisions as $division)
                    <p>{{ $division->nama }}: {{ $division->employees_count }}</p>
                @endforeach
            </div>
            <p id="jumlah-data" class="flex justify-end text-sm">
                Jumlah Data: {{ count($employees) }}
            </p>
        </section>

        @csrf
        <section class="container-table table-scroll-wrapper">
            <table id="employees-table" class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="px-3 py-2 text-left">No</th>
                        <th class="px-3 py-2 text-left">Nama</th>
                        <th class="px-3 py-2 text-left">Nilai</th>
                        <th class="px-3 py-2 text-left">NIK</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Divisi</th>
                        <th class="px-3 py-2 text-left">Tim</th>
                        <th rowspan="2" class="px-3 py-2 text-left sticky-col-right">Aksi</th>
                    </tr>
                    <tr>
                        <th><input class="filter w-full px-2 py-1 border rounded" data-column="1" type="text"
                                placeholder="Cari Nama" /></th>
                        <th><input class="filter w-full px-2 py-1 border rounded" data-column="2" type="text"
                                placeholder="Cari Nilai" /></th>
                        <th><input class="filter w-full px-2 py-1 border rounded" data-column="3" type="text"
                                placeholder="Cari NIK" /></th>
                        <th>
                            <select class="filter w-full px-2 py-1 border rounded" data-column="4" data-exact="true">
                                <option value="">Semua</option>
                                <option value="Direct">Direct</option>
                                <option value="Non Direct">Non Direct</option>
                            </select>
                        </th>
                        <th>
                            <select class="filter w-full px-2 py-1 border rounded" data-column="5" data-exact="true">
                                <option value="">Semua Divisi</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->nama }}">{{ $division->nama }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th><input class="filter w-full px-2 py-1 border rounded" data-column="6" type="text"
                                placeholder="Cari Tim" /></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $index => $employee)
                        <tr data-id="{{ $employee->id }}" class="border-t hover:bg-gray-50">
                            <td class="px-3 py-2 number">{{ $index + 1 }}</td>
                            <td class="px-3 py-2">{{ $employee->nama ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->nilaiTahunan->first()?->nilai ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->nik ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->status ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->division?->nama ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->team ?? '-' }}</td>
                            <td class="px-3 py-2 sticky-col-right">
                                <div class="btn-group flex gap-1">
                                    <button type="button" class="btn btn-icon edit-row">
                                        <i class="material-symbols-rounded text-blue-600">edit_square</i>
                                    </button>
                                    <button type="button" class="btn btn-icon"
                                        onclick="showDeletePopup(this.closest('tr'))" title="Hapus">
                                        <i class="material-symbols-rounded delete-row btn-danger">delete</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($employees->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500">Tidak ada data karyawan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const table = document.getElementById('employees-table');
            const filters = table.querySelectorAll('.filter');

            // Jalankan filter saat halaman dimuat
            applyFilters();

            // Pasang event listener
            filters.forEach(filter => {
                filter.addEventListener('input', applyFilters);
                filter.addEventListener('change', applyFilters);
            });

            function applyFilters() {
                const rows = table.querySelectorAll('tbody tr:not(#no-data-row)');
                let visibleCount = 0;

                rows.forEach(row => {
                    let show = true;
                    filters.forEach(filter => {
                        const colIndex = parseInt(filter.dataset.column);
                        const filterValue = filter.value.toLowerCase().trim();
                        const isExact = filter.dataset.exact === "true";
                        const cell = row.cells[colIndex];

                        if (filterValue && cell) {
                            const cellText = cell.textContent.toLowerCase().trim();

                            if (isExact) {
                                // Perbandingan eksak untuk select
                                if (cellText !== filterValue) {
                                    show = false;
                                }
                            } else {
                                // Perbandingan parsial untuk input teks
                                if (!cellText.includes(filterValue)) {
                                    show = false;
                                }
                            }
                        }
                    });

                    row.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                // Update jumlah data
                document.getElementById('jumlah-data').textContent = `Jumlah Data: ${visibleCount}`;

                // Tampilkan pesan "tidak ada data" jika perlu
                const noDataRow = document.querySelector('#employees-table tbody tr:last-child[id="no-data-row"]');
                if (noDataRow) {
                    noDataRow.classList.toggle('hidden', visibleCount > 0);
                }
            }

            // ==== DELETE ====
            window.showDeletePopup = function(row) {
                const popup = document.getElementById('popupDelete');
                if (popup) {
                    popup.dataset.targetId = row.dataset.id;
                    popup.classList.replace('hidden', 'flex');
                }
            };

            function hideDeletePopup() {
                const popup = document.getElementById('popupDelete');
                if (popup) popup.classList.replace('flex', 'hidden');
            }

            document.getElementById('cancelDelete')?.addEventListener('click', hideDeletePopup);
            document.getElementById('confirmDelete')?.addEventListener('click', function() {
                const popup = document.getElementById('popupDelete');
                const id = popup?.dataset.targetId;
                if (!id || !csrfToken) return;

                fetch(`/iseki_rifa/public/employees/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            document.querySelector(`tr[data-id="${id}"]`)?.remove();
                            hideDeletePopup();
                            applyFilters();
                        } else {
                            alert('Gagal menghapus data');
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan saat menghapus'));
            });

            // ==== EDIT ====
            document.querySelectorAll('.edit-row').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    const cells = row.querySelectorAll('td');

                    const id = row.dataset.id;
                    const nama = cells[1].textContent;
                    const nik = cells[3].textContent;
                    const status = cells[4].textContent;
                    const divisi = cells[5].textContent;
                    const team = cells[6].textContent === '-' ? '' : cells[6].textContent;

                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-nama').value = nama;
                    document.getElementById('edit-nik').value = nik;
                    document.getElementById('edit-status').value = status;
                    document.getElementById('edit-divisi').value = divisi;
                    document.getElementById('edit-team').value = team;

                    if (typeof $ !== 'undefined') {
                        $('#edit-status').val(status).trigger('change');
                        $('#edit-divisi').val(divisi).trigger('change');
                    }

                    document.getElementById('editEmployeeModal').classList.replace('hidden',
                        'flex');
                });
            });

            window.closeEditModal = function() {
                document.getElementById('editEmployeeModal')?.classList.replace('flex', 'hidden');
            };

            document.getElementById('editEmployeeForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('edit-id').value;
                const data = {
                    nama: document.getElementById('edit-nama').value,
                    nik: document.getElementById('edit-nik').value,
                    status: document.getElementById('edit-status').value,
                    divisi: document.getElementById('edit-divisi').value,
                    team: document.getElementById('edit-team').value,
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
                    })
                    .catch(() => alert('Terjadi kesalahan saat menyimpan'));
            });
        });
    </script>
@endsection
