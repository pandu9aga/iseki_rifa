@extends('layouts.app')

@section('content')
@php
    $isEmployee = !Auth::check() && session('employee_login');
@endphp
<main>

    <!-- Modal Edit -->
    @include('components.popupEditLembur')

    <!-- Modal Delete -->
    @include('components.popupDeleteLembur')

    <!-- Judul & tombol tambah -->
    <div class="mb-8" style="margin-bottom: 2rem;">
        <br>
        <h3 class="font-bold text-2xl">Data Lembur</h3>
    </div>

    @if(Auth::check())
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

    <section class="btn-group flex flex-wrap gap-8 mb-10 items-center justify-between"
        style="gap: 2rem; margin-bottom: 0.2rem;">
        <div class="flex gap-4" style="gap: 1rem;">
            @userType('leader')
            <a href="{{ route('lemburs.create') }}" class="btn btn-primary">
                <span>Tambah Data</span>
                <i class="material-symbols-rounded">add</i>
            </a>
            <a href="#" class="btn btn-primary" id="export-lembur-btn">
                <i class="fas fa-file-excel mr-2" style="margin-right: 0.5rem;"></i> Export Lembur
            </a>
            <a href="#" class="btn btn-primary export-bulanan-excel-btn" style="display:none;">
                <i class="fas fa-file-excel mr-2" style="margin-right: 0.5rem;"></i> Export Bulanan Excel
            </a>
            @enduserType

            @userType('admin')
            <a href="#" class="btn btn-primary" id="export-lembur-btn">
                <i class="fas fa-file-excel mr-2" style="margin-right: 0.5rem;"></i> Export Lembur
            </a>
            <a href="#" class="btn btn-primary export-bulanan-excel-btn" style="display:none;">
                <i class="fas fa-file-excel mr-2" style="margin-right: 0.5rem;"></i> Export Bulanan Excel
            </a>
            <a href="{{ route('laporan.lembur.index') }}" class="btn btn-primary">
                <i class="fas fa-chart-line mr-2" style="margin-right: 0.5rem;"></i> Laporan Lembur
            </a>
            <a href="{{ route('budget.lembur.index') }}" class="btn btn-primary">
                <i class="fas fa-wallet mr-2" style="margin-right: 0.5rem;"></i> Budget Lembur
            </a>
            @enduserType

            @userType('super')
            <a href="#" class="btn btn-primary" id="export-lembur-btn">
                <i class="fas fa-file-excel mr-2" style="margin-right: 0.5rem;"></i> Export Lembur
            </a>
            <a href="#" class="btn btn-primary export-bulanan-excel-btn" style="display:none;">
                <i class="fas fa-file-excel mr-2" style="margin-right: 0.5rem;"></i> Export Bulanan Excel
            </a>
            <a href="{{ route('laporan.lembur.index') }}" class="btn btn-primary">
                <i class="fas fa-chart-line mr-2" style="margin-right: 0.5rem;"></i> Laporan Lembur
            </a>
            <a href="{{ route('budget.lembur.index') }}" class="btn btn-primary">
                <i class="fas fa-wallet mr-2" style="margin-right: 0.5rem;"></i> Budget Lembur
            </a>
            @enduserType
        </div>

        <div class="flex gap-8" style="gap: 2rem;">
            <div class="bg-green-100 border border-green-300 px-6 py-3 rounded shadow text-sm">
                <div class="text-gray-600 text-xs mb-2" style="margin-bottom: 0.5rem;">Budget (<span
                        id="bulan-budget-info">{{ \Carbon\Carbon::parse($bulanReferensi)->format('M Y') }}</span>)</div>
                <div class="text-green-700 text-base font-semibold">
                    <span id="budget-display">{{ number_format($budgetValue, 1) }}</span> jam
                </div>
            </div>
            <div class="bg-blue-100 border border-blue-300 px-6 py-3 rounded shadow text-sm">
                <div class="text-gray-600 text-xs mb-2" style="margin-bottom: 0.5rem;">Total Durasi (<span
                        id="bulan-info">{{ \Carbon\Carbon::parse($bulanReferensi)->format('M Y') }}</span>)</div>
                <div class="text-blue-700 text-base font-semibold">
                    <span id="total-durasi-display">{{ number_format($totalDurasiBulanIni, 1) }}</span> jam
                </div>
            </div>

            <div id="selisih-box" class="border px-6 py-3 rounded shadow text-sm 
                {{ $selisih >= 0 ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                <div class="text-gray-600 text-xs mb-2" style="margin-bottom: 0.5rem;">Selisih</div>
                <div id="selisih-text"
                    class="text-base font-semibold {{ $selisih >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ $selisih >= 0 ? '+' : '' }}<span id="selisih-display">{{ number_format($selisih, 1) }}</span> jam
                </div>
            </div>
        </div>
    </section>

    <section class="flex flex-wrap gap-6 mb-10 items-center" style="gap: 1.5rem; margin-bottom: 0.2rem;">
        <div class="text-sm font-semibold text-gray-600 mr-2">Jam per Kategori:</div>
        <div class="bg-purple-100 border border-purple-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-purple-700 font-semibold">Produksi: <span id="jam-produksi">0</span> jam</span>
        </div>
        <div class="bg-orange-100 border border-orange-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-orange-700 font-semibold">Maintenance: <span id="jam-maintenance">0</span> jam</span>
        </div>
        <div class="bg-teal-100 border border-teal-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-teal-700 font-semibold">Kaizen: <span id="jam-kaizen">0</span> jam</span>
        </div>
        <div class="bg-yellow-100 border border-yellow-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-yellow-700 font-semibold">5S: <span id="jam-5s">0</span> jam</span>
        </div>
            <div class="bg-indigo-100 border border-indigo-300 px-4 py-2 rounded shadow text-sm">
                <span class="text-indigo-700 font-semibold">Leader/PIC: <span id="jam-leader">0</span> jam</span>
            </div>
        </section>
    @endif

    <section class="container-table table-scroll-wrapper">
        <table class="table-auto w-full border border-gray-300 mt-4" id="lembur-table">
            <thead>
                <tr>
                    <th class="sticky-col-left">No</th>
                    <th class="{{ $isEmployee ? '' : 'sticky-col-left' }}">Nama</th>
                    @if(!$isEmployee)
                    <th>Nilai</th>
                    <th>Divisi</th>
                    @endif
                    <th>Tanggal</th>
                    @userType('leader')
                    <th>Status Persetujuan</th>
                    <th>Jam</th>
                    <th>Durasi</th>
                    <th>Pekerjaan</th>
                    <th>Makan</th>
                    @enduserType
                    @userType('admin')
                    <th>Status Persetujuan</th>
                    <th>Jam</th>
                    <th>Durasi</th>
                    <th>Pekerjaan</th>
                    <th>Makan</th>
                    @enduserType
                    @userType('super')
                    <th>Approval</th>
                    <th>Status Persetujuan</th>
                    <th>Jam</th>
                    <th>Durasi</th>
                    <th>Pekerjaan</th>
                    <th>Makan</th>
                    @enduserType

                    @if($isEmployee)
                    <th>Jam</th>
                    <th>Durasi</th>
                    @endif
                    
                    @if(Auth::check())
                    <th class="sticky-col-right">Aksi</th>
                    @endif
                </tr>
                <tr class="dt-filter-row">
                    <th></th>
                    <th class="{{ $isEmployee ? '' : 'sticky-col-left' }}">
                        <input type="text" class="filter dt-filter" data-col="nama" placeholder="Cari Nama"
                            @if($isEmployee) value="{{ session('employee_user')->name }}" readonly style="background-color: #e9ecef; cursor: not-allowed;" @endif>
                    </th>
                    @if(!$isEmployee)
                    <th><input type="text" class="filter dt-filter" data-col="nilai" placeholder="Cari Nilai"></th>
                    <th><input type="text" class="filter dt-filter" data-col="divisi" placeholder="Cari Divisi"></th>
                    @endif
                    <th>
                        <input id="customDate" name="customDate" type="date" class="filter dt-filter" data-col="tanggal">
                        <button type="button" id="toggleType" class="btn btn-secondary btn-sm mt-1">Month</button>
                    </th>
                    @userType('leader')
                    <th><input type="text" class="filter dt-filter" data-col="status" placeholder="Approval"></th>
                    <th><input type="text" class="filter dt-filter" data-col="waktu" placeholder="Jam"></th>
                    <th><input type="text" class="filter dt-filter" data-col="durasi" placeholder="Durasi"></th>
                    <th>
                        <select class="filter dt-filter" data-col="pekerjaan" id="filter-pekerjaan-leader">
                            <option value="">Semua</option>
                            <option value="Produksi">Produksi</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Kaizen">Kaizen</option>
                            <option value="5S">5S</option>
                            <option value="Pekerjaan Leader/PIC Lembur">Leader/PIC</option>
                        </select>
                    </th>
                    <th><input type="text" class="filter dt-filter" data-col="makan" placeholder="Makan"></th>
                    @enduserType
                    @userType('admin')
                    <th><input type="text" class="filter dt-filter" data-col="status" placeholder="Approval"></th>
                    <th><input type="text" class="filter dt-filter" data-col="waktu" placeholder="Jam"></th>
                    <th><input type="text" class="filter dt-filter" data-col="durasi" placeholder="Durasi"></th>
                    <th>
                        <select class="filter dt-filter" data-col="pekerjaan" id="filter-pekerjaan-admin">
                            <option value="">Semua</option>
                            <option value="Produksi">Produksi</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Kaizen">Kaizen</option>
                            <option value="5S">5S</option>
                            <option value="Pekerjaan Leader/PIC Lembur">Leader/PIC</option>
                        </select>
                    </th>
                    <th><input type="text" class="filter dt-filter" data-col="makan" placeholder="Makan"></th>
                    @enduserType
                    @userType('super')
                    <th></th>
                    <th><input type="text" class="filter dt-filter" data-col="status" placeholder="Approval"></th>
                    <th><input type="text" class="filter dt-filter" data-col="waktu" placeholder="Jam"></th>
                    <th><input type="text" class="filter dt-filter" data-col="durasi" placeholder="Durasi"></th>
                    <th>
                        <select class="filter dt-filter" data-col="pekerjaan" id="filter-pekerjaan-super">
                            <option value="">Semua</option>
                            <option value="Produksi">Produksi</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Kaizen">Kaizen</option>
                            <option value="5S">5S</option>
                            <option value="Pekerjaan Leader/PIC Lembur">Leader/PIC</option>
                        </select>
                    </th>
                    <th><input type="text" class="filter dt-filter" data-col="makan" placeholder="Makan"></th>
                    @enduserType
                    @if($isEmployee)
                    <th><input type="text" class="filter dt-filter" data-col="waktu" placeholder="Jam"></th>
                    <th><input type="text" class="filter dt-filter" data-col="durasi" placeholder="Durasi"></th>
                    @endif
                    @if(Auth::check())
                    <th></th>
                    @endif
                </tr>
            </thead>
        </table>
    </section>
    <br><br>
</main>

<script>
    const budgetData = @json($budgetData ?? []);
    const isEmployee = @json($isEmployee);
    const userType = @json(Auth::check() ? Auth::user()->type : null);
    let table;
    let tableColumns = [];

    function buildColumns() {
        const cols = [
            { data: 'no', orderable: false, searchable: false },
            { data: 'nama' },
        ];
        if (!isEmployee) {
            cols.push({ data: 'nilai' });
            cols.push({ data: 'divisi_nama' });
        }
        cols.push({ data: 'tanggal' });

        if (userType === 'leader' || userType === 'admin') {
            cols.push({ data: 'status_label' });
            cols.push({ data: 'waktu' });
            cols.push({ data: 'durasi' });
            cols.push({ data: 'pekerjaan' });
            cols.push({ data: 'makan' });
        } else if (userType === 'super') {
            cols.push({ data: 'approval_buttons', orderable: false, searchable: false });
            cols.push({ data: 'status_label' });
            cols.push({ data: 'waktu' });
            cols.push({ data: 'durasi' });
            cols.push({ data: 'pekerjaan' });
            cols.push({ data: 'makan' });
        }

        if (isEmployee) {
            cols.push({ data: 'waktu' });
            cols.push({ data: 'durasi' });
        }

        if (userType) {
            cols.push({ data: 'action_buttons', orderable: false, searchable: false });
        }

        return cols;
    }

    function getColIndex(dataKey) {
        for (let i = 0; i < tableColumns.length; i++) {
            if (tableColumns[i].data === dataKey) return i;
        }
        return -1;
    }

    function getFilterColIndex(el) {
        const key = el.dataset.col;
        return getColIndex(key);
    }

    $(document).ready(function () {
        tableColumns = buildColumns();
        const dateFilter = document.getElementById('customDate');
        @if(!$isEmployee)
        if (dateFilter && !dateFilter.value) {
            dateFilter.value = new Date().toISOString().split('T')[0];
        }
        @endif

        table = $('#lembur-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: isEmployee ? '/iseki_rifa/public/employee/lembur/data' : '/iseki_rifa/public/lembur/data',
                type: 'GET',
                data: function (d) {
                    const tahunSelect = document.querySelector('select[name="tahun"]');
                    if (tahunSelect) d.tahun = tahunSelect.value;
                    const dateFilter = document.getElementById('customDate');
                    if (dateFilter && dateFilter.value) {
                        d.tanggal = dateFilter.value;
                    }
                }
            },
            columns: tableColumns,
            order: [],
            orderCellsTop: true,
            paging: false,
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: 'info',
                bottomEnd: null
            },
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                processing: 'Memuat...',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: '→',
                    previous: '←'
                }
            },
            drawCallback: function () {
                attachApprovalListeners(document);
                updateBudgetFromServer();
                initEditButtons();
            },
            createdRow: function (row, data, dataIndex) {
                $(row).attr('data-id', data.id_lembur);
                const noIdx = getColIndex('no');
                if (noIdx >= 0) $(row).find('td').eq(noIdx).text(dataIndex + 1).addClass('sticky-col-left');
                const namaIdx = getColIndex('nama');
                if (namaIdx >= 0 && !isEmployee) {
                    $(row).find('td').eq(namaIdx).addClass('sticky-col-left');
                }
                const statusIdx = getColIndex('status_label');
                if (statusIdx >= 0) {
                    $(row).find('td').eq(statusIdx).addClass('status text-center h-full text-sm ' + (data.status_class || ''));
                }
                const aksiIdx = getColIndex('action_buttons');
                if (aksiIdx >= 0) {
                    $(row).find('td').eq(aksiIdx).addClass('sticky-col-right');
                }
            }
        });

        function clearDateFilter() {
            if (!dateFilter || !dateFilter.value) return;
            dateFilter.value = '';
        }

        // Connect filters
        function connectFilters() {
            document.querySelectorAll('.dt-filter').forEach(el => {
                const colIdx = getFilterColIndex(el);
                if (colIdx < 0) return;
                if (el.dataset.col === 'tanggal') return;
                const eventType = el.tagName === 'SELECT' ? 'change' : 'input';
                el.addEventListener(eventType, function () {
                    clearDateFilter();
                    table.column(colIdx).search(this.value).draw();
                });
            });
        }
        connectFilters();

        // Toggle date/month
        const toggleBtn = document.getElementById('toggleType');

        @if($isEmployee)
        if (dateFilter) dateFilter.value = '';
        @endif

        function updateBulananExcelBtnVisibility() {
            document.querySelectorAll('.export-bulanan-excel-btn').forEach(btn => {
                btn.style.display = (dateFilter && dateFilter.type === 'month' && dateFilter.value) ? '' : 'none';
            });
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                if (dateFilter.type === "date") {
                    dateFilter.type = "month";
                    this.textContent = "Date";
                } else {
                    dateFilter.type = "date";
                    this.textContent = "Month";
                }
                if (table) table.ajax.reload();
                updateBulananExcelBtnVisibility();
            });
        }

        if (dateFilter) {
            dateFilter.addEventListener('change', function () {
                if (table) table.ajax.reload();
                updateBulananExcelBtnVisibility();
            });
        }

        // Export
        document.getElementById('export-lembur-btn')?.addEventListener('click', function (e) {
            e.preventDefault();
            const tanggal = dateFilter ? dateFilter.value : '';
            let url = "{{ route('export.lembur') }}";
            if (tanggal) url += '?tanggal=' + encodeURIComponent(tanggal);
            window.location.href = url;
        });

        document.querySelectorAll('.export-bulanan-excel-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (!dateFilter || !dateFilter.value || dateFilter.type !== 'month') {
                    alert('Pilih bulan terlebih dahulu (klik tombol Month pada filter tanggal).');
                    return;
                }
                const [year, month] = dateFilter.value.split('-');
                let url = "{{ route('export.lembur.bulanan.excel') }}";
                url += '?tahun=' + encodeURIComponent(year) + '&bulan=' + encodeURIComponent(parseInt(month));
                window.location.href = url;
            });
        });

        updateBulananExcelBtnVisibility();
    });

    function updateBudgetFromServer() {
        const dateFilter = document.getElementById('customDate');
        const tahunSelect = document.querySelector('select[name="tahun"]');
        let params = new URLSearchParams();
        if (dateFilter && dateFilter.value) {
            if (dateFilter.type === 'month') {
                const [year, month] = dateFilter.value.split('-');
                params.set('bulan', parseInt(month));
                params.set('tahun', year);
            } else {
                params.set('tanggal', dateFilter.value);
            }
        } else if (tahunSelect) {
            params.set('tahun', tahunSelect.value);
        }
        const url = (isEmployee ? '/iseki_rifa/public/employee/lembur/summary' : '/iseki_rifa/public/lembur/summary') + '?' + params.toString();

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.totalDurasi !== undefined) {
                    document.getElementById('total-durasi-display').textContent = data.totalDurasi.toFixed(1);
                }
                if (data.budgetValue !== undefined) {
                    document.getElementById('budget-display').textContent = data.budgetValue.toFixed(1);
                }
                if (data.selisih !== undefined) {
                    const selisih = data.selisih;
                    const selisihBox = document.getElementById('selisih-box');
                    const selisihText = document.getElementById('selisih-text');
                    document.getElementById('selisih-display').textContent = Math.abs(selisih).toFixed(1);
                    if (selisih >= 0) {
                        selisihBox.className = 'border px-4 py-2 rounded shadow text-sm bg-green-50 border-green-300';
                        selisihText.className = 'text-base font-semibold text-green-700';
                        selisihText.innerHTML = '+<span id="selisih-display">' + selisih.toFixed(1) + '</span> jam';
                    } else {
                        selisihBox.className = 'border px-4 py-2 rounded shadow text-sm bg-red-50 border-red-300';
                        selisihText.className = 'text-base font-semibold text-red-700';
                        selisihText.innerHTML = '-<span id="selisih-display">' + Math.abs(selisih).toFixed(1) + '</span> jam';
                    }
                }
                if (data.jamProduksi !== undefined) {
                    document.getElementById('jam-produksi').textContent = data.jamProduksi.toFixed(1);
                    document.getElementById('jam-maintenance').textContent = data.jamMaintenance.toFixed(1);
                    document.getElementById('jam-kaizen').textContent = data.jamKaizen.toFixed(1);
                    document.getElementById('jam-5s').textContent = data.jam5s.toFixed(1);
                    document.getElementById('jam-leader').textContent = data.jamLeader.toFixed(1);
                }
                if (data.bulanReferensi) {
                    const [year, month] = data.bulanReferensi.split('-');
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const monthName = monthNames[parseInt(month) - 1];
                    const bulanInfo = document.getElementById('bulan-info');
                    const bulanBudgetInfo = document.getElementById('bulan-budget-info');
                    if (bulanInfo) bulanInfo.textContent = monthName + ' ' + year;
                    if (bulanBudgetInfo) bulanBudgetInfo.textContent = monthName + ' ' + year;
                }
            })
            .catch(err => console.error('Failed to load summary', err));
    }

    // Modal Delete
    let rowToDelete = null;
    window.showDeletePopup = function (row) {
        rowToDelete = row;
        document.getElementById('popupDelete').classList.replace('hidden', 'flex');
    };
    window.hideDeletePopup = function () {
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
                    if (table) table.ajax.reload(null, false);
                    hideDeletePopup();
                } else alert('Gagal menghapus data');
            });
        }
    });

    // Edit modal
    function initEditButtons() {
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
    }

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

    const editForm = document.getElementById('editLemburForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
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
                if (res.ok) {
                    if (table) table.ajax.reload(null, false);
                    closeEditModal();
                } else alert('Gagal menyimpan perubahan');
            });
        });
    }

    function closeEditModal() {
        const modal = document.getElementById('editLemburModal');
        if (modal) modal.classList.replace('flex', 'hidden');
    }
</script>

<script>
    const csrfTokenLembur = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function attachApprovalListeners(context = document) {
        context.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function () {
                const approval = this.dataset.value;
                const form = this.closest('.approval-form');
                const id = form.dataset.id;

                fetch(`/iseki_rifa/public/lembur/${id}/approve`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenLembur,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ approval: approval })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (table) table.ajax.reload(null, false);
                    })
                    .catch(() => alert('Gagal memperbarui status'));
            });
        });
    }
</script>

@endsection