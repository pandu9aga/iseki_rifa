@extends('layouts.app')

@section('content')
    <main>

        <!-- Modal Edit -->
        @include('components.popupEditLembur')

        <!-- Modal Delete -->
        @include('components.popupDeleteLembur')

        <!-- Judul & tombol tambah -->
        <div class="mb-4">
            <br>
            <h3 class="font-bold text-lg">Data Lembur</h3>
        </div>

        {{-- Tombol sesuai role --}}
        <section class="btn-group flex gap-2 mb-4 items-center justify-between">
            <div class="flex gap-2">
                @userType('leader')
                    <a href="{{ route('lemburs.create') }}" class="btn btn-primary">
                        <span>Tambah Data</span>
                        <i class="material-symbols-rounded">add</i>
                    </a>
                    <a href="#" class="btn btn-primary" id="export-lembur-btn">
                        <i class="fas fa-file-excel"></i> Export Lembur
                    </a>
                @enduserType
                @userType('admin')
                    <a href="#" class="btn btn-primary" id="export-lembur-btn">
                        <i class="fas fa-file-excel"></i> Export Lembur
                    </a>
                    <a href="{{ route('laporan.lembur.index') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> Laporan Lembur
                    </a>
                @enduserType

            </div>

            <!-- Kotak total durasi -->
            <div id="total-durasi-box"
                class="bg-gray-100 border border-gray-300 px-4 py-2 rounded shadow text-sm font-semibold">
                Total Durasi: <span id="total-durasi">0</span> jam
            </div>
        </section>

        <!-- Tabel -->
        <section class="container-table">
            <table class="table-auto w-full border border-gray-300 mt-4" id="lembur-table">
                <thead>
                    <tr>
                        <th class="sticky-col-left">No</th>
                        <th>Nama<br><input type="text" class="filter" data-column="1" placeholder="Cari Nama"></th>
                        <th>Divisi<br><input type="text" class="filter" data-column="2" placeholder="Cari Divisi"></th>
                        <th>
                            Tanggal<br>
                            <input id="customDate" name="customDate" type="date" class="filter" data-column="3">
                            <button type="button" id="toggleType" class="btn btn-secondary btn-sm mt-1">Month</button>
                        </th>
                        <th>Jam<br><input type="text" class="filter" data-column="4" placeholder="Jam"></th>
                        <th>Durasi<br><input type="text" class="filter" data-column="5" placeholder="Durasi"></th>
                        <th>Pekerjaan<br><input type="text" class="filter" data-column="6" placeholder="Pekerjaan"></th>
                        <th>Makan<br><input type="text" class="filter" data-column="7" placeholder="Makan"></th>
                        <th>Approval<br><input type="text" class="filter" data-column="8" placeholder="Approval"></th>
                        <th class="sticky-col-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lemburs as $row)
                        <tr data-id="{{ $row->id_lembur }}">
                            <td class="sticky-col-left">{{ $loop->iteration }}</td>
                            <td>{{ $row->employee->nama }}</td>
                            <td>{{ $row->employee->division->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal_lembur)->format('d-m-Y') }}</td>
                            <td>{{ $row->waktu_lembur }}</td>
                            <td>{{ $row->durasi_lembur }}</td>
                            <td>{{ $row->keterangan_lembur }}</td>
                            <td>{{ $row->makan_lembur }}</td>
                            <td>{{ $row->approval_lembur }}</td>
                            <td class="sticky-col-right">
                                @userType('leader')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-icon edit-btn" data-id="{{ $row->id_lembur }}"
                                            data-employee="{{ $row->employee->id }}"
                                            data-tanggal="{{ \Carbon\Carbon::parse($row->tanggal_lembur)->format('Y-m-d') }}"
                                            data-waktu="{{ $row->waktu_lembur }}" data-durasi="{{ $row->durasi_lembur }}"
                                            data-keterangan="{{ $row->keterangan_lembur }}"
                                            data-makan="{{ $row->makan_lembur }}">
                                            <i class="material-symbols-rounded btn-primary">edit_square</i>
                                        </button>

                                        <button type="button" class="btn btn-icon danger delete-row"
                                            onclick="showDeletePopup(this.closest('tr'))" title="Hapus">
                                            <i class="material-symbols-rounded delete-row btn-danger">delete</i>
                                        </button>
                                    </div>
                                @enduserType
                                @userType('admin')
                                    <span class="text-gray-500 italic">Export Only</span>
                                @enduserType
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        <br><br>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const table = document.getElementById('lembur-table');
            if (!table) return;

            const filters = table.querySelectorAll('.filter');

            // set default tanggal hari ini
            const today = new Date().toISOString().split('T')[0];
            const dateFilter = document.getElementById('customDate');
            if (dateFilter) {
                dateFilter.value = today;
                filterTable();
            }

            // Toggle date/month
            const toggleBtn = document.getElementById('toggleType');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    if (dateFilter.type === "date") {
                        dateFilter.type = "month";
                        this.textContent = "Date";
                    } else {
                        dateFilter.type = "date";
                        this.textContent = "Month";
                    }
                    filterTable();
                });
            }

            // filter per kolom
            filters.forEach(input => {
                input.addEventListener('input', filterTable);
                input.addEventListener('change', filterTable);
            });

            function filterTable() {
                const rows = table.tBodies[0].rows;
                let counter = 1;
                let totalDurasi = 0;

                for (let row of rows) {
                    let show = true;
                    filters.forEach(filter => {
                        const colIndex = filter.dataset.column;
                        const filterValue = filter.value.toLowerCase();
                        const cell = row.cells[colIndex];

                        if (filterValue && cell) {
                            let cellText = cell.textContent.toLowerCase();

                            if (colIndex == "3") {
                                const parts = cellText.split("-");
                                if (parts.length === 3) {
                                    cellText = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                }
                            }

                            if (!cellText.includes(filterValue)) show = false;
                        }
                    });

                    // tambahan filter fleksibel untuk toggle date/month
                    if (dateFilter && dateFilter.value) {
                        const [day, month, year] = row.cells[3].textContent.trim().split("-");
                        if (dateFilter.type === "date") {
                            const filterDate = new Date(dateFilter.value);
                            const rowDate = new Date(`${year}-${month}-${day}`);
                            if (rowDate.toDateString() !== filterDate.toDateString()) show = false;
                        } else if (dateFilter.type === "month") {
                            const [fYear, fMonth] = dateFilter.value.split("-");
                            if (month !== fMonth || year !== fYear) show = false;
                        }
                    }

                    if (show) {
                        row.style.display = '';
                        row.querySelector('td.sticky-col-left').textContent = counter++;

                        // ambil kolom durasi (kolom ke-5, index 5)
                        let durasiText = row.cells[5].textContent.trim();
                        let durasiNum = parseFloat(durasiText.replace(',', '.')) || 0;
                        totalDurasi += durasiNum;
                    } else {
                        row.style.display = 'none';
                    }
                }

                // update kotak total durasi
                // update kotak total durasi (tanpa desimal)
                document.getElementById('total-durasi').textContent = Math.round(totalDurasi);
            }

            // Export Lembur
            document.getElementById('export-lembur-btn').addEventListener('click', function(e) {
                e.preventDefault();
                const tanggal = dateFilter ? dateFilter.value : '';
                let url = "{{ route('export.lembur') }}";
                if (tanggal) url += '?tanggal=' + tanggal;
                window.location.href = url;
            });

            // Modal Delete
            let rowToDelete = null;
            window.showDeletePopup = function(row) {
                rowToDelete = row;
                document.getElementById('popupDelete').classList.replace('hidden', 'flex');
            };
            window.hideDeletePopup = function() {
                rowToDelete = null;
                document.getElementById('popupDelete').classList.replace('flex', 'hidden');
            };
            document.getElementById('cancelDelete').addEventListener('click', hideDeletePopup);
            document.getElementById('confirmDelete').addEventListener('click', () => {
                if (rowToDelete) {
                    const id = rowToDelete.dataset.id;
                    fetch(`/iseki_rifa/public/lembur/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    }).then(res => {
                        if (res.ok) {
                            rowToDelete.remove();
                            hideDeletePopup();
                            updateRowNumbers();
                        } else alert('Gagal menghapus data');
                    });
                }
            });

            function updateRowNumbers() {
                const numbers = table.querySelectorAll('tbody tr td.sticky-col-left');
                numbers.forEach((cell, index) => cell.textContent = index + 1);
            }

            // Modal Edit
            function openEditModal(data) {
                document.getElementById('edit-lembur-id').value = data.id;
                document.getElementById('edit-employee_id').value = data.employee_id;
                document.getElementById('edit-tanggal_lembur').value = data.tanggal_lembur || '';
                const [jamMulai, jamSelesai] = (data.waktu || ' - ').split(' - ');
                document.getElementById('edit-jam_mulai').value = jamMulai || '';
                document.getElementById('edit-jam_selesai').value = jamSelesai || '';
                document.getElementById('edit-durasi_lembur').value = data.durasi_lembur || '';
                document.getElementById('edit-keterangan_lembur').value = data.keterangan_lembur || '';
                document.getElementById('edit-makan_lembur').value = (data.makan_lembur && data.makan_lembur
                    .toLowerCase() === 'ya') ? 'Ya' : 'X';
                document.getElementById('editLemburModal').classList.replace('hidden', 'flex');
            }

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const data = {
                        id: btn.dataset.id,
                        employee_id: btn.dataset.employee,
                        tanggal_lembur: btn.dataset.tanggal,
                        waktu: btn.dataset.waktu,
                        durasi_lembur: btn.dataset.durasi,
                        keterangan_lembur: btn.dataset.keterangan,
                        makan_lembur: btn.dataset.makan
                    };
                    openEditModal(data);
                });
            });

            // Submit Edit
            document.getElementById('editLemburForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('edit-lembur-id').value;

                const data = {
                    employee_id: document.getElementById('edit-employee_id').value,
                    tanggal_lembur: document.getElementById('edit-tanggal_lembur').value,
                    jam_mulai: document.getElementById('edit-jam_mulai').value,
                    jam_selesai: document.getElementById('edit-jam_selesai').value,
                    durasi_lembur: document.getElementById('edit-durasi_lembur').value,
                    keterangan_lembur: document.getElementById('edit-keterangan_lembur').value,
                    makan_lembur: document.getElementById('edit-makan_lembur').value,
                };

                fetch(`/iseki_rifa/public/lembur/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                }).then(res => {
                    if (res.ok) location.reload();
                    else alert('Gagal menyimpan perubahan');
                });
            });

        });

        function closeEditModal() {
            const modal = document.getElementById('editLemburModal');
            if (modal) modal.classList.replace('flex', 'hidden');
            window.location.href = "{{ route('lemburs.index') }}";
        }
    </script>
@endsection
