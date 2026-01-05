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

        {{-- Filter Tahun Penilaian --}}
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
                @userType('super')
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
        <section class="container-table table-scroll-wrapper">
            <table class="table-auto w-full border border-gray-300 mt-4" id="lembur-table">
                <thead>
                    <tr>
                        <th class="sticky-col-left">No</th>
                        <th class="sticky-col-left">Nama<br><input type="text" class="filter" data-column="1" placeholder="Cari Nama"></th>
                        <th>Nilai<br><input type="text" class="filter" data-column="2" placeholder="Cari Nilai"></th>
                        <th>Divisi<br><input type="text" class="filter" data-column="3" placeholder="Cari Divisi"></th>
                        <th>
                            Tanggal<br>
                            <input id="customDate" name="customDate" type="date" class="filter" data-column="4">
                            <button type="button" id="toggleType" class="btn btn-secondary btn-sm mt-1">Month</button>
                        </th>
                        @userType('leader')
                            <th>Status Persetujuan<br><input type="text" class="filter" data-column="5"
                                    placeholder="Approval"></th>
                            <th>Jam<br><input type="text" class="filter" data-column="6" placeholder="Jam"></th>
                            <th>Durasi<br><input type="text" class="filter" data-column="7" placeholder="Durasi"></th>
                            <th>Pekerjaan<br><input type="text" class="filter" data-column="8" placeholder="Pekerjaan"></th>
                            <th>Makan<br><input type="text" class="filter" data-column="9" placeholder="Makan"></th>
                        @enduserType
                        @userType('admin')
                            <th>Status Persetujuan<br><input type="text" class="filter" data-column="5"
                                    placeholder="Approval"></th>
                            <th>Jam<br><input type="text" class="filter" data-column="6" placeholder="Jam"></th>
                            <th>Durasi<br><input type="text" class="filter" data-column="7" placeholder="Durasi"></th>
                            <th>Pekerjaan<br><input type="text" class="filter" data-column="8" placeholder="Pekerjaan"></th>
                            <th>Makan<br><input type="text" class="filter" data-column="9" placeholder="Makan"></th>
                        @enduserType
                        @userType('super')
                            <th>Approval</th>
                            <th>Status Persetujuan<br><input type="text" class="filter" data-column="6"
                                placeholder="Approval"></th>
                            <th>Jam<br><input type="text" class="filter" data-column="7" placeholder="Jam"></th>
                            <th>Durasi<br><input type="text" class="filter" data-column="8" placeholder="Durasi"></th>
                            <th>Pekerjaan<br><input type="text" class="filter" data-column="9" placeholder="Pekerjaan"></th>
                            <th>Makan<br><input type="text" class="filter" data-column="10" placeholder="Makan"></th>
                        @enduserType
                        <th class="sticky-col-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lemburs as $row)
                        <tr data-id="{{ $row->id_lembur }}">
                            <td class="sticky-col-left">{{ $loop->iteration }}</td>
                            <td class="sticky-col-left">{{ $row->employee->nama ?? '-' }}</td>
                            <td>{{ $row->employee->nilaiTahunan->firstWhere('tanggal_penilaian', 'like', $tahun . '-12-31')?->nilai ?? '-' }}</td>
                            <td>{{ $row->employee->division->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal_lembur)->format('d-m-Y') }}</td>
                            <!-- Approval -->
                            @userType('super')
                                <td>
                                    <form class="approval-form" data-id="{{ $row->id_lembur }}">
                                        @csrf
                                        @method('PUT')
                                        @if (is_null($row->approval_lembur))
                                            <div class="flex flex-col btn-group">
                                                <button type="button" data-value="1"
                                                    class="btn bg-success text-sm rounded approve-btn">Setujui</button>
                                                <button type="button" data-value="0"
                                                    class="btn bg-red text-sm rounded approve-btn">Tolak</button>
                                            </div>
                                        @else
                                            <button type="button" data-value="null"
                                                class="btn bg-yellow text-sm rounded approve-btn">Batalkan</button>
                                        @endif
                                    </form>
                                </td>
                            @enduserType
                            <!-- Status Persetujuan -->
                            <td
                                class="status text-center h-full text-sm {{ $row->approval_lembur === null ? 'bg-yellow' : ($row->approval_lembur ? 'bg-success' : 'bg-red') }}">
                                {{ is_null($row->approval_lembur) ? 'Menunggu Persetujuan' : ($row->approval_lembur ? 'Disetujui' : 'Ditolak') }}
                            </td>
                            <td>{{ $row->waktu_lembur }}</td>
                            <td>{{ $row->durasi_lembur }}</td>
                            <td>{{ $row->keterangan_lembur }}</td>
                            <td>{{ $row->makan_lembur }}</td>

                            <!-- Aksi -->
                            <td class="sticky-col-right">
                                @userType('leader')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-icon edit-btn" data-id="{{ $row->id_lembur }}"
                                            data-employee_name="{{ $row->employee->nama ?? '-' }}"
                                            data-employee_id="{{ $row->employee->id }}"
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
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-icon edit-btn" data-id="{{ $row->id_lembur }}"
                                            data-employee_name="{{ $row->employee->nama ?? '-' }}"
                                            data-employee_id="{{ $row->employee->id }}"
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
                                @userType('super')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-icon edit-btn" data-id="{{ $row->id_lembur }}"
                                            data-employee_name="{{ $row->employee->nama ?? '-' }}"
                                            data-employee_id="{{ $row->employee->id }}"
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
                                {{-- @userType('admin')
                                    <span class="text-gray-500 italic">Export Only</span>
                                @enduserType --}}
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

                            if (colIndex == "4") { // Kolom Tanggal sekarang indeks 4
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
                        const [day, month, year] = row.cells[4].textContent.trim().split("-"); // Kolom Tanggal sekarang indeks 4
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

                        // Ambil kolom durasi: 4 kolom dari belakang (Aksi = -1, Makan = -2, Pekerjaan = -3, Durasi = -4)
                        const durasiCell = row.cells[row.cells.length - 4];
                        let durasiText = durasiCell ? durasiCell.textContent.trim() : '0';
                        let durasiNum = parseFloat(durasiText.replace(',', '.')) || 0;
                        totalDurasi += durasiNum;
                    } else {
                        row.style.display = 'none';
                    }
                }

                // update kotak total durasi
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
                const tbody = table.tBodies[0];
                const rows = tbody.rows;
                let visibleCounter = 1;

                for (let i = 0; i < rows.length; i++) {
                    const currentRow = rows[i];
                    if (currentRow.style.display !== 'none') {
                        const firstCell = currentRow.cells[0];
                        if (firstCell) {
                            firstCell.textContent = visibleCounter;
                            visibleCounter++;
                        }
                    }
                }
            }

            // Modal Edit
            function openEditModal(data) {
                document.getElementById('edit-lembur-id').value = data.id;
                document.getElementById('edit-employee_name').value = data.employee_name;
                document.getElementById('edit-employee_id').value = data.employee_id;
                document.getElementById('edit-tanggal_lembur').value = data.tanggal_lembur || '';
                const [jamMulai, jamSelesai] = (data.waktu || ' - ').split(' - ');
                document.getElementById('edit-jam_mulai').value = jamMulai || '';
                document.getElementById('edit-jam_selesai').value = jamSelesai || '';
                document.getElementById('edit-durasi_lembur').value = data.durasi_lembur || '';
                document.getElementById('edit-keterangan_lembur').value = data.keterangan_lembur || '';
                let makan = (data.makan_lembur || '').toString().toLowerCase().trim();
                document.getElementById('edit-makan_lembur').value = (makan === 'ya') ? 'ya' : 'tidak';
                document.getElementById('editLemburModal').classList.replace('hidden', 'flex');
            }

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const data = {
                        id: btn.dataset.id,
                        employee_name: btn.dataset.employee_name,
                        employee_id: btn.dataset.employee_id,
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
                    employee_name: document.getElementById('edit-employee_name').value,
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
            // window.location.href = "{{ route('lemburs.index') }}";
        }

        // Approve / Reject / Cancel
        document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', () => updateApproval(btn.dataset.id, 'Approved'));
        });
        document.querySelectorAll('.btn-reject').forEach(btn => {
            btn.addEventListener('click', () => updateApproval(btn.dataset.id, 'Rejected'));
        });
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => updateApproval(btn.dataset.id, 'Pending'));
        });
    </script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function attachApprovalListeners(context = document) {
            context.querySelectorAll('.approve-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const approval = this.dataset.value;
                    const form = this.closest('.approval-form');
                    const id = form.dataset.id;

                    fetch(`/iseki_rifa/public/lembur/${id}/approve`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                approval: approval
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            const statusCell = row.querySelector('.status');

                            // Update kolom Status Persetujuan
                            statusCell.textContent = data.status_label;
                            statusCell.classList.remove('bg-yellow', 'bg-success', 'bg-red');
                            statusCell.classList.add(data.status_class);

                            // Update tombol Approval
                            form.innerHTML = data.button_html;
                            attachApprovalListeners(form); // attach ulang hanya di form ini

                        })
                        .catch(() => alert('Gagal memperbarui status'));
                });
            });
        }

        // Jalankan listener saat page load
        document.addEventListener('DOMContentLoaded', () => attachApprovalListeners());
    </script>

@endsection