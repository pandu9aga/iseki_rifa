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
                <option value="{{ $opt }}" @selected($opt==$tahun)>{{ $opt }}</option>
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
            <a href="{{ route('budget.lembur.index') }}" class="btn btn-primary">
                <i class="fas fa-wallet"></i> Budget Lembur
            </a>
            @enduserType

            @userType('super')
            <a href="#" class="btn btn-primary" id="export-lembur-btn">
                <i class="fas fa-file-excel"></i> Export Lembur
            </a>
            <a href="{{ route('laporan.lembur.index') }}" class="btn btn-primary">
                <i class="fas fa-chart-line"></i> Laporan Lembur
            </a>
            <a href="{{ route('budget.lembur.index') }}" class="btn btn-primary">
                <i class="fas fa-wallet"></i> Budget Lembur
            </a>
            @enduserType
        </div>

        <!-- Kotak Informasi Budget & Durasi -->
        <div class="flex gap-3">

            <!-- Budget Bulanan -->
            <div class="bg-green-100 border border-green-300 px-4 py-2 rounded shadow text-sm">
                <div class="text-gray-600 text-xs mb-1">Budget (<span id="bulan-budget-info">{{ \Carbon\Carbon::parse($bulanReferensi)->format('M Y') }}</span>)</div>
                <div class="text-green-700 text-base font-semibold">
                    <span id="budget-display">{{ number_format($budgetValue, 1) }}</span> jam
                </div>
            </div>
            <!-- Total Durasi -->
            <div class="bg-blue-100 border border-blue-300 px-4 py-2 rounded shadow text-sm">
                <div class="text-gray-600 text-xs mb-1">Total Durasi (<span id="bulan-info">{{ \Carbon\Carbon::parse($bulanReferensi)->format('M Y') }}</span>)</div>
                <div class="text-blue-700 text-base font-semibold">
                    <span id="total-durasi-display">{{ number_format($totalDurasiBulanIni, 1) }}</span> jam
                </div>
            </div>

            <!-- Selisih -->
            <div id="selisih-box" class="border px-4 py-2 rounded shadow text-sm 
                {{ $selisih >= 0 ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                <div class="text-gray-600 text-xs mb-1">Selisih</div>
                <div id="selisih-text" class="text-base font-semibold {{ $selisih >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ $selisih >= 0 ? '+' : '' }}<span id="selisih-display">{{ number_format($selisih, 1) }}</span> jam
                </div>
            </div>
        </div>
    </section>

    <!-- Ringkasan Jam per Kategori Pekerjaan -->
    <section class="flex flex-wrap gap-2 mb-4 items-center">
        <div class="text-sm font-semibold text-gray-600 mr-2">Jam per Kategori:</div>
        <div class="bg-purple-100 border border-purple-300 px-3 py-1 rounded shadow text-sm">
            <span class="text-purple-700 font-semibold">Produksi: <span id="jam-produksi">0</span> jam</span>
        </div>
        <div class="bg-orange-100 border border-orange-300 px-3 py-1 rounded shadow text-sm">
            <span class="text-orange-700 font-semibold">Maintenance: <span id="jam-maintenance">0</span> jam</span>
        </div>
        <div class="bg-teal-100 border border-teal-300 px-3 py-1 rounded shadow text-sm">
            <span class="text-teal-700 font-semibold">Kaizen: <span id="jam-kaizen">0</span> jam</span>
        </div>
        <div class="bg-yellow-100 border border-yellow-300 px-3 py-1 rounded shadow text-sm">
            <span class="text-yellow-700 font-semibold">5S: <span id="jam-5s">0</span> jam</span>
        </div>
        <div class="bg-indigo-100 border border-indigo-300 px-3 py-1 rounded shadow text-sm">
            <span class="text-indigo-700 font-semibold">Leader/PIC: <span id="jam-leader">0</span> jam</span>
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
                    <th>Status Persetujuan<br><input type="text" class="filter" data-column="5" placeholder="Approval"></th>
                    <th>Jam<br><input type="text" class="filter" data-column="6" placeholder="Jam"></th>
                    <th>Durasi<br><input type="text" class="filter" data-column="7" placeholder="Durasi"></th>
                    <th>Pekerjaan<br>
                        <select class="filter" data-column="8" id="filter-pekerjaan-leader">
                            <option value="">Semua</option>
                            <option value="Produksi">Produksi</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Kaizen">Kaizen</option>
                            <option value="5S">5S</option>
                            <option value="Pekerjaan Leader/PIC Lembur">Leader/PIC</option>
                        </select>
                    </th>
                    <th>Makan<br><input type="text" class="filter" data-column="9" placeholder="Makan"></th>
                    @enduserType
                    @userType('admin')
                    <th>Status Persetujuan<br><input type="text" class="filter" data-column="5" placeholder="Approval"></th>
                    <th>Jam<br><input type="text" class="filter" data-column="6" placeholder="Jam"></th>
                    <th>Durasi<br><input type="text" class="filter" data-column="7" placeholder="Durasi"></th>
                    <th>Pekerjaan<br>
                        <select class="filter" data-column="8" id="filter-pekerjaan-admin">
                            <option value="">Semua</option>
                            <option value="Produksi">Produksi</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Kaizen">Kaizen</option>
                            <option value="5S">5S</option>
                            <option value="Pekerjaan Leader/PIC Lembur">Leader/PIC</option>
                        </select>
                    </th>
                    <th>Makan<br><input type="text" class="filter" data-column="9" placeholder="Makan"></th>
                    @enduserType
                    @userType('super')
                    <th>Approval</th>
                    <th>Status Persetujuan<br><input type="text" class="filter" data-column="6" placeholder="Approval"></th>
                    <th>Jam<br><input type="text" class="filter" data-column="7" placeholder="Jam"></th>
                    <th>Durasi<br><input type="text" class="filter" data-column="8" placeholder="Durasi"></th>
                    <th>Pekerjaan<br>
                        <select class="filter" data-column="9" id="filter-pekerjaan-super">
                            <option value="">Semua</option>
                            <option value="Produksi">Produksi</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Kaizen">Kaizen</option>
                            <option value="5S">5S</option>
                            <option value="Pekerjaan Leader/PIC Lembur">Leader/PIC</option>
                        </select>
                    </th>
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

                    <!-- Approval (hanya super) -->
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

                    <!-- Status Persetujuan (semua role) -->
                    <td class="status text-center h-full text-sm {{ $row->approval_lembur === null ? 'bg-yellow' : ($row->approval_lembur ? 'bg-success' : 'bg-red') }}">
                        {{ is_null($row->approval_lembur) ? 'Menunggu Persetujuan' : ($row->approval_lembur ? 'Disetujui' : 'Ditolak') }}
                    </td>
                    <td>{{ $row->waktu_lembur }}</td>
                    <td>{{ $row->durasi_lembur }}</td>
                    <td>{{ $row->keterangan_lembur }}</td>
                    <td>{{ $row->makan_lembur }}</td>

                    <!-- Aksi -->
                    <td class="sticky-col-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-icon edit-btn"
                                data-id="{{ $row->id_lembur }}"
                                data-employee_name="{{ $row->employee->nama ?? '-' }}"
                                data-employee_id="{{ $row->employee->id }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($row->tanggal_lembur)->format('Y-m-d') }}"
                                data-waktu="{{ $row->waktu_lembur }}"
                                data-durasi="{{ $row->durasi_lembur }}"
                                data-keterangan="{{ $row->keterangan_lembur }}"
                                data-makan="{{ $row->makan_lembur }}">
                                <i class="material-symbols-rounded btn-primary">edit_square</i>
                            </button>

                            <button type="button" class="btn btn-icon danger delete-row"
                                onclick="showDeletePopup(this.closest('tr'))" title="Hapus">
                                <i class="material-symbols-rounded delete-row btn-danger">delete</i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    <br><br>
</main>

<script>
    // Data budget dari server
    const budgetData = @json($budgetData ?? []);

    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('lembur-table');
        if (!table) return;

        const filters = table.querySelectorAll('.filter');
        const dateFilter = document.getElementById('customDate');

        // Set default tanggal hari ini
        const today = new Date().toISOString().split('T')[0];
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

        // Filter per kolom
        filters.forEach(input => {
            input.addEventListener('input', filterTable);
            input.addEventListener('change', filterTable);
        });

        function filterTable() {
            const rows = table.tBodies[0].rows;
            let counter = 1;
            let totalDurasi = 0;
            let currentBulan = null;

            // Inisialisasi total jam per kategori
            let jamProduksi = 0;
            let jamMaintenance = 0;
            let jamKaizen = 0;
            let jam5S = 0;
            let jamLeader = 0;

            for (let row of rows) {
                let show = true;
                filters.forEach(filter => {
                    const colIndex = filter.dataset.column;
                    const filterValue = filter.value.toLowerCase();
                    const cell = row.cells[colIndex];

                    if (filterValue && cell) {
                        let cellText = cell.textContent.toLowerCase();

                        // Normalisasi format tanggal d-m-Y ke Y-m-d untuk pencarian
                        if (colIndex == "4") {
                            const parts = cellText.split("-");
                            if (parts.length === 3) {
                                cellText = `${parts[2]}-${parts[1]}-${parts[0]}`;
                            }
                        }

                        // Exact match untuk dropdown pekerjaan
                        if (filter.tagName === 'SELECT' && colIndex >= 8) { // Asumsi kolom pekerjaan ada di index 8 atau 9
                            if (cellText.trim() !== filterValue) show = false;
                        } else {
                            if (!cellText.includes(filterValue)) show = false;
                        }
                    }
                });

                // Filter fleksibel berdasarkan input tanggal/bulan
                if (dateFilter && dateFilter.value) {
                    const [day, month, year] = row.cells[4].textContent.trim().split("-");
                    if (dateFilter.type === "date") {
                        const filterDate = new Date(dateFilter.value);
                        const rowDate = new Date(`${year}-${month}-${day}`);
                        if (rowDate.toDateString() !== filterDate.toDateString()) show = false;
                        else currentBulan = `${year}-${month}`;
                    } else if (dateFilter.type === "month") {
                        const [fYear, fMonth] = dateFilter.value.split("-");
                        if (month !== fMonth || year !== fYear) show = false;
                        else currentBulan = `${fYear}-${fMonth}`;
                    }
                }

                if (show) {
                    row.style.display = '';
                    row.querySelector('td.sticky-col-left').textContent = counter++;

                    // Ambil kolom durasi (selalu di index length-4)
                    const durasiCell = row.cells[row.cells.length - 4];
                    let durasiText = durasiCell ? durasiCell.textContent.trim() : '0';
                    let durasiNum = parseFloat(durasiText.replace(',', '.')) || 0;
                    totalDurasi += durasiNum;

                    // Ambil kolom pekerjaan (selalu di index length-3)
                    const pekerjaanCell = row.cells[row.cells.length - 3];
                    const pekerjaanText = pekerjaanCell ? pekerjaanCell.textContent.trim().toLowerCase() : '';

                    if (pekerjaanText === 'produksi') jamProduksi += durasiNum;
                    else if (pekerjaanText === 'maintenance') jamMaintenance += durasiNum;
                    else if (pekerjaanText === 'kaizen') jamKaizen += durasiNum;
                    else if (pekerjaanText === '5s') jam5S += durasiNum;
                    else if (pekerjaanText.includes('leader') || pekerjaanText.includes('pic')) jamLeader += durasiNum;

                } else {
                    row.style.display = 'none';
                }
            }

            // Update display total durasi
            updateBudgetInfo(totalDurasi, currentBulan);

            // Update display ringkasan kategori
            document.getElementById('jam-produksi').textContent = jamProduksi.toFixed(1);
            document.getElementById('jam-maintenance').textContent = jamMaintenance.toFixed(1);
            document.getElementById('jam-kaizen').textContent = jamKaizen.toFixed(1);
            document.getElementById('jam-5s').textContent = jam5S.toFixed(1);
            document.getElementById('jam-leader').textContent = jamLeader.toFixed(1);
        }

        function updateBudgetInfo(totalDurasi, bulan) {
            // Update total durasi
            document.getElementById('total-durasi-display').textContent = totalDurasi.toFixed(1);

            // Cari budget berdasarkan bulan
            let budget = 0;
            if (bulan && budgetData[bulan]) {
                budget = parseFloat(budgetData[bulan]);
            }

            // Update budget display
            document.getElementById('budget-display').textContent = budget.toFixed(1);

            // Hitung selisih
            const selisih = budget - totalDurasi;
            document.getElementById('selisih-display').textContent = Math.abs(selisih).toFixed(1);

            // Update warna dan tanda selisih
            const selisihBox = document.getElementById('selisih-box');
            const selisihText = document.getElementById('selisih-text');

            if (selisih >= 0) {
                selisihBox.className = 'border px-4 py-2 rounded shadow text-sm bg-green-50 border-green-300';
                selisihText.className = 'text-base font-semibold text-green-700';
                selisihText.innerHTML = '+<span id="selisih-display">' + selisih.toFixed(1) + '</span> jam';
            } else {
                selisihBox.className = 'border px-4 py-2 rounded shadow text-sm bg-red-50 border-red-300';
                selisihText.className = 'text-base font-semibold text-red-700';
                selisihText.innerHTML = '-<span id="selisih-display">' + Math.abs(selisih).toFixed(1) + '</span> jam';
            }

            // Update bulan info
            if (bulan) {
                const [year, month] = bulan.split('-');
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const monthName = monthNames[parseInt(month) - 1];
                document.getElementById('bulan-info').textContent = `${monthName} ${year}`;
                document.getElementById('bulan-budget-info').textContent = `${monthName} ${year}`;
            }
        }

        // Export Lembur
        const exportBtn = document.getElementById('export-lembur-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const tanggal = dateFilter ? dateFilter.value : '';
                let url = "{{ route('export.lembur') }}";
                if (tanggal) url += '?tanggal=' + encodeURIComponent(tanggal);
                window.location.href = url;
            });
        }

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
        document.getElementById('cancelDelete')?.addEventListener('click', hideDeletePopup);
        document.getElementById('confirmDelete')?.addEventListener('click', () => {
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
                        filterTable(); // Re-calculate budget after delete
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
                        firstCell.textContent = visibleCounter++;
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
        const editForm = document.getElementById('editLemburForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
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
        }
    });

    function closeEditModal() {
        const modal = document.getElementById('editLemburModal');
        if (modal) modal.classList.replace('flex', 'hidden');
    }
</script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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

                        statusCell.textContent = data.status_label;
                        statusCell.className = 'status text-center h-full text-sm ' + data.status_class;

                        form.innerHTML = data.button_html;
                        attachApprovalListeners(form);
                    })
                    .catch(() => alert('Gagal memperbarui status'));
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => attachApprovalListeners());
</script>

@endsection