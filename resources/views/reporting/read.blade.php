@extends('layouts.app')

@section('content')
<style>
    .badge-container {
        margin: 1em 0;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        margin: 4px 4px 8px 0;
        font-size: 14px;
        font-weight: bold;
        border-radius: 12px;
        text-transform: capitalize;
    }

    .badge-small {
        display: inline-block;
        padding: 3px 6px;
        margin: 2px 2px 4px 0;
        font-size: 10px;
        font-weight: bold;
        border-radius: 12px;
        text-transform: capitalize;
    }

    .badge-submitted {
        background-color: #ec057d;
        color: white;
    }

    .badge-pending {
        background-color: #ccc;
        color: #333;
    }

    .notif-success {
        background-color: #16a34a;
    }

    .notif-error {
        background-color: #dc2626;
    }

    .notif-warning {
        background-color: #f59e0b;
        color: #000;
    }

    .table-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 640px) {
        .table-scroll-wrapper {
            width: max-content;
        }
    }

    #cuti-table {
        width: max-content;
        min-width: 100%;
        border-collapse: collapse;
    }
    #cuti-table thead tr:first-child th.sticky-col-left {
        position: sticky;
        left: 0;
        z-index: 4;
    }
    #cuti-table thead tr:nth-child(2) th.sticky-col-left {
        position: sticky;
        left: 0;
        z-index: 4;
    }
    div#cuti-table_wrapper .dt-layout-row {
        align-items: center;
    }
    div#cuti-table_wrapper .dt-info {
        padding-top: 6px;
    }
</style>
<main>
    @include('components.popupEdit')
    @include('components.popupDailyReport')

    @userType('leader')
    <h4 class="font-bold mt-4">
        Laporan hari ini :
        <span style="color: #ec057d;">{{ now()->format('d M Y') }}</span>
    </h4>

    <div class="badge-container">
        <h5 style="margin-bottom: 5pt;">Status Tim yang Sudah Submit:</h5>
        @foreach ($allTeams as $team)
        @php
        $flatTeams = array_map('strtolower', array_merge(...$teamsWithReport));
        $isSubmitted = in_array(strtolower($team), $flatTeams);
        @endphp

        <span class="badge {{ $isSubmitted ? 'badge-submitted' : 'badge-pending' }}">
            {{ ucwords($team) }}
        </span>
        @endforeach
    </div>

    <h5>
        Leader Yang Telah Submit :
    </h5>

    <table class="table-auto w-full border border-gray-300 mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2 text-center">No</th>
                <th class="border px-4 py-2">Nama</th>
                <th class="border px-4 py-2">Divisi</th>
                <th class="border px-4 py-2">Team</th>
                <th class="border px-4 py-2">Submit Laporan</th>
                <th class="border px-4 py-2">Update Laporan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportToday as $i => $report)
            <tr>
                <td class="border px-4 py-2 text-center">{{ $i + 1 }}</td>
                <td class="border px-4 py-2">{{ $report->user?->name ?? '-' }}</td>
                <td class="border px-4 py-2">{{ $report->user?->division ?? 'Tanpa Divisi' }}</td>
                <td class="border px-4 py-2">
                    {{ is_array($report->user?->team) 
                                ? implode(', ', $report->user->team) 
                                : $report->user?->team ?? 'Tanpa Team' }}
                </td>
                <td class="border px-4 py-2">{{ $report->created_at->format('Y-m-d H:i') }}</td>
                <td class="border px-4 py-2">{{ $report->updated_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    @enduserType

    @userType('admin')
    <h4 class="font-bold mt-4">
        Laporan hari ini :
        <span style="color: #ec057d;">{{ now()->format('d M Y') }}</span>
    </h4>

    <div class="badge-container">
        <h5 style="margin-bottom: 5pt;">Status Tim yang Sudah Submit:</h5>
        @foreach ($allTeams as $team)
        @php
        $flatTeams = array_map('strtolower', array_merge(...$teamsWithReport));
        $isSubmitted = in_array(strtolower($team), $flatTeams);
        @endphp

        <span class="badge {{ $isSubmitted ? 'badge-submitted' : 'badge-pending' }}">
            {{ ucwords($team) }}
        </span>
        @endforeach
    </div>

    <h5>
        Leader Yang Telah Submit :
    </h5>

    <table class="table-auto w-full border border-gray-300 mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2 text-center">No</th>
                <th class="border px-4 py-2">Nama</th>
                <th class="border px-4 py-2">Divisi</th>
                <th class="border px-4 py-2">Team</th>
                <th class="border px-4 py-2">Submit Laporan</th>
                <th class="border px-4 py-2">Update Laporan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportToday as $i => $report)
            <tr>
                <td class="border px-4 py-2 text-center">{{ $i + 1 }}</td>
                <td class="border px-4 py-2">{{ $report->user?->name ?? '-' }}</td>
                <td class="border px-4 py-2">{{ $report->user?->division ?? 'Tanpa Divisi' }}</td>
                <td class="border px-4 py-2">
                    {{ is_array($report->user?->team) 
                                ? implode(', ', $report->user->team) 
                                : $report->user?->team ?? 'Tanpa Team' }}
                </td>
                <td class="border px-4 py-2">{{ $report->created_at->format('Y-m-d H:i') }}</td>
                <td class="border px-4 py-2">{{ $report->updated_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    @enduserType

    @userType('super')
    <h4 class="font-bold mt-4">
        Laporan hari ini :
        <span style="color: #ec057d;">{{ now()->format('d M Y') }}</span>
    </h4>

    <div class="badge-container">
        <h5 style="margin-bottom: 5pt;">Status Tim yang Sudah Submit:</h5>
        @foreach ($allTeams as $team)
        @php
        $flatTeams = array_map('strtolower', array_merge(...$teamsWithReport));
        $isSubmitted = in_array(strtolower($team), $flatTeams);
        @endphp

        <span class="badge {{ $isSubmitted ? 'badge-submitted' : 'badge-pending' }}">
            {{ ucwords($team) }}
        </span>
        @endforeach
    </div>

    <h5>
        Leader Yang Telah Submit :
    </h5>

    <table class="table-auto w-full border border-gray-300 mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2 text-center">No</th>
                <th class="border px-4 py-2">Nama</th>
                <th class="border px-4 py-2">Divisi</th>
                <th class="border px-4 py-2">Team</th>
                <th class="border px-4 py-2">Submit Laporan</th>
                <th class="border px-4 py-2">Update Laporan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportToday as $i => $report)
            <tr>
                <td class="border px-4 py-2 text-center">{{ $i + 1 }}</td>
                <td class="border px-4 py-2">{{ $report->user?->name ?? '-' }}</td>
                <td class="border px-4 py-2">{{ $report->user?->division ?? 'Tanpa Divisi' }}</td>
                <td class="border px-4 py-2">
                    {{ is_array($report->user?->team) 
                                ? implode(', ', $report->user->team) 
                                : $report->user?->team ?? 'Tanpa Team' }}
                </td>
                <td class="border px-4 py-2">{{ $report->created_at->format('Y-m-d H:i') }}</td>
                <td class="border px-4 py-2">{{ $report->updated_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    @enduserType

    <section class="title-button d-flex flex-row">
        <h1 class="font-bold">List Perizinan</h1>
        <section class="btn-group d-flex flex-row">
            @userType('leader')
            <a href="{{ url('/reporting/create') }}" class="btn btn-primary">
                Tambah Data
                <i class="material-symbols-rounded">
                    add
                </i>
            </a>
            @enduserType
            @userType('admin')
            <div style="display:flex; align-items:center; gap:2px;">
                <button type="button" class="btn btn-sm btn-secondary" id="dr-prev" style="padding:2px 6px; line-height:1;">&#8249;</button>
                <input type="date" id="dailyReportInput" class="form-control" value="{{ now()->format('Y-m-d') }}" />
                <button type="button" class="btn btn-sm btn-secondary" id="dr-next" style="padding:2px 6px; line-height:1;">&#8250;</button>
            </div>
            <button class="btn btn-secondary" id="dailyReportBtn">
                Laporan Harian
                <i class="material-symbols-rounded">
                    assignment
                </i>
            </button>
            <div class="relative">
                <button class="btn w-fit btn-export text-white" id="toggle-dropdown">
                    Unduh Laporan
                    <span class="material-symbols-rounded">
                        stat_minus_1
                    </span>
                </button>

                <form id="dropdown-form" method="GET" action="{{ url('/reporting/export') }}"
                    class="absolute right-0 mt-2 p-4 bg-white rounded-md items-end shadow-sm hidden w-fit z-10 border border-black">
                    <label for="bulan" class="text-sm font-medium w-full flex gap-sm-1">Pilih Bulan</label>
                    <input type="month" name="bulan" id="dropdown-bulan"
                        value="{{ request('bulan', now()->format('Y-m')) }}"
                        class="border rounded px-2 py-1 block w-fit text-sm" required>
                    <button type="submit" class="btn w-fit btn-primary">
                        Unduh
                        <i class="material-symbols-rounded">
                            download
                        </i>
                    </button>
                </form>
            </div>
            @enduserType
            @userType('super')
            <div style="display:flex; align-items:center; gap:2px;">
                <button type="button" class="btn btn-sm btn-secondary" id="dr-prev" style="padding:2px 6px; line-height:1;">&#8249;</button>
                <input type="date" id="dailyReportInput" class="form-control" value="{{ now()->format('Y-m-d') }}" />
                <button type="button" class="btn btn-sm btn-secondary" id="dr-next" style="padding:2px 6px; line-height:1;">&#8250;</button>
            </div>
            <button class="btn btn-secondary" id="dailyReportBtn">
                Laporan Harian
                <i class="material-symbols-rounded">
                    assignment
                </i>
            </button>
            <div class="relative">
                <button class="btn w-fit btn-export text-white" id="toggle-dropdown">
                    Unduh Laporan
                    <span class="material-symbols-rounded">
                        stat_minus_1
                    </span>
                </button>

                <form id="dropdown-form" method="GET" action="{{ url('/reporting/export') }}"
                    class="absolute right-0 mt-2 p-4 bg-white rounded-md items-end shadow-sm hidden w-fit z-10 border border-black">
                    <label for="bulan" class="text-sm font-medium w-full flex gap-sm-1">Pilih Bulan</label>
                    <input type="month" name="bulan" id="dropdown-bulan"
                        value="{{ request('bulan', now()->format('Y-m')) }}"
                        class="border rounded px-2 py-1 block w-fit text-sm" required>
                    <button type="submit" class="btn w-fit btn-primary">
                        Unduh
                        <i class="material-symbols-rounded">
                            download
                        </i>
                    </button>
                </form>
            </div>
            @enduserType
        </section>
    </section>

    @userType('leader', 'admin', 'super')
    <a href="{{ route('reporting.excel') }}" class="btn btn-primary">Data Perizinan <i
            class="material-symbols-rounded">download</i></a>
    @enduserType

    <p id="jumlah-data" class="flex justify-end mb-2 text-sm">
        Jumlah Data: 0
    </p>

    <div id="approvalNotif"
        style="
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                padding: 20px;
                z-index: 100;
                color: white;
                width: 50%;
                max-width: 400px;
                text-align: center;
                border-radius: 8px;
            ">
        <span id="approvalNotifText"></span>
    </div>

    @csrf
    @php
        $isEmployeeReporting = session()->has('employee_login') && session('employee_login');
    @endphp
    <section class="container-table table-scroll-wrapper">
        <table id="cuti-table">
            <thead>
                <tr>
                    <th rowspan="2" class="sticky-col-left">No</th>
                    <th class="sticky-col-left">Nama</th>
                    <th>Jenis Izin</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th rowspan="2">Sisa Cuti</th>
                    @userType('super')
                    <th rowspan="2">Approvement</th>
                    @enduserType
                    <th>Status Persetujuan</th>
                    <th>Persetujuan Member</th>
                    <th>Persetujuan HR</th>
                    @if(!$isEmployeeReporting)
                    <th rowspan="2">Pengganti</th>
                    @endif
                    <th>Tim</th>
                    <th>Status</th>
                    <th>Divisi</th>
                    <th rowspan="2" class="sticky-col-right">Aksi</th>
                </tr>
                <tr>
                    <th class="sticky-col-left">
                        <input class="filter dt-filter" id="filter-nama" type="text" placeholder="Cari Nama"
                            value="{{ $isEmployeeReporting ? session('employee_user')->name : '' }}"
                            {{ $isEmployeeReporting ? 'readonly' : '' }} />
                    </th>
                    <th>
                        <select name="jenis_cuti" class="select2 dt-filter" data-placeholder="Pilih jenis izin"
                            data-allow-clear="true" style="width: 100%;" id="filter-jenis">
                            <option></option>
                            <option value="Cuti">Cuti</option>
                            <option value="Cuti Setengah Hari Pagi">Cuti Setengah Hari Pagi</option>
                            <option value="Cuti Setengah Hari Siang">Cuti Setengah Hari Siang</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Izin Keluar">Izin Keluar</option>
                            <option value="Pulang Cepat">Pulang Cepat</option>
                            <option value="Pulang Cepat Dengan Surat">Pulang Cepat Dengan Surat</option>
                            <option value="Absen">Absen</option>
                            <option value="Absen Setengah Hari Pagi">Absen Setengah Hari Pagi</option>
                            <option value="Absen Setengah Hari Siang">Absen Setengah Hari Siang</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Cuti Khusus">Cuti Khusus</option>
                            <option value="Serikat">Serikat</option>
                            <option value="Salah Fingerprint">Salah Fingerprint</option>
                        </select>
                    </th>
                    <th>
                        <div style="display:flex; align-items:center; gap:2px;">
                            <button type="button" class="btn btn-sm btn-secondary" id="date-prev" style="padding:2px 6px; line-height:1;">&#8249;</button>
                            <input class="filter dt-filter" id="filter-tanggal" type="date"
                                value="{{ $isEmployeeReporting ? '' : date('Y-m-d') }}" style="flex:1; min-width:0;" />
                            <button type="button" class="btn btn-sm btn-secondary" id="date-next" style="padding:2px 6px; line-height:1;">&#8250;</button>
                        </div>
                    </th>
                    <th><input type="text" class="filter dt-filter" id="filter-keterangan" placeholder="Cari Keterangan" /></th>
                    <th>
                        <select name="approval_status" class="select2 dt-filter"
                            data-placeholder="Pilih status persetujuan" data-allow-clear="true" style="width: 100%"
                            id="filter-approval-status">
                            <option></option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </th>
                    <th>
                        <select name="approval_member_status" class="select2 dt-filter"
                            data-placeholder="Pilih status persetujuan" data-allow-clear="true" style="width: 100%"
                            id="filter-approval-member-status" {{ $isEmployeeReporting ? 'disabled' : '' }}>
                            <option></option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </th>
                    <th>
                        <select name="approval_hr_status" class="select2 dt-filter"
                            data-placeholder="Pilih status persetujuan" data-allow-clear="true" style="width: 100%"
                            id="filter-approval-hr-status">
                            <option></option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </th>
                    <th><input class="filter dt-filter" id="filter-team" type="text" placeholder="Cari Tim" /></th>
                    <th>
                        <select name="status" class="select2 dt-filter" data-placeholder="Pilih status"
                            data-allow-clear="true" style="width: 100%" id="filter-status">
                            <option></option>
                            <option value="Direct">Direct</option>
                            <option value="Non Direct">Non Direct</option>
                        </select>
                    </th>
                    <th>
                        <select name="divisi" class="select2 dt-filter" data-placeholder="Pilih divisi"
                            data-allow-clear="true" style="width: 100%" id="filter-divisi">
                            <option></option>
                            @foreach ($divisions as $division)
                            <option value="{{ $division->nama }}">{{ $division->nama }}</option>
                            @endforeach
                        </select>
                    </th>
                </tr>
            </thead>
        </table>
    </section>
    @include('components.popupDelete')
</main>

@include('components.popupReplacement')

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const isEmployee = @json($isEmployeeReporting);
    const userType = @json(Auth::check() ? Auth::user()->type : null);

    const columns = [
        { data: 'no', orderable: false, searchable: false },
        { data: 'nama' },
        { data: 'jenis_izin' },
        { data: 'tanggal_formatted' },
        { data: 'keterangan_html' },
        { data: 'sisa_cuti' },
    ];
    if (userType === 'super') {
        columns.push({ data: 'approval_buttons', orderable: false, searchable: false });
    }
    columns.push(
        { data: 'status_super_label' },
        { data: 'member_approval_html' },
        { data: 'hr_approval_label' },
    );
    if (!isEmployee) {
        columns.push({ data: 'replacement_info', orderable: false, searchable: false });
    }
    columns.push(
        { data: 'tim' },
        { data: 'status_pegawai' },
        { data: 'divisi' },
        { data: 'action_buttons', orderable: false, searchable: false },
    );

    const columnMap = {};
    columns.forEach((col, idx) => {
        columnMap[col.data] = idx;
    });

    let table;

    $(document).ready(function () {
            table = $('#cuti-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: isEmployee ? '/iseki_rifa/public/employee/reporting/data' : '/iseki_rifa/public/reporting/data',
                type: 'GET',
            },
            columns: columns,
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
                updateJumlahData();
                attachApprovalListeners(document);
                attachMemberApprovalListeners(document);
                initEditButtons();
            },
            createdRow: function (row, data, dataIndex) {
                $(row).attr('data-id', data.id);
                const statusSuper = data.status_super;
                if (statusSuper) {
                    const statusCell = $(row).find('td').eq(columnMap['status_super_label']);
                    statusCell.addClass('status-super text-center h-full text-sm ' + statusSuper.class);

                    const memberIdx = columnMap['member_approval_html'];
                    if (memberIdx !== undefined) {
                        $(row).find('td').eq(memberIdx).addClass('status-member');
                    }

                    const hrIdx = columnMap['hr_approval_label'];
                    if (hrIdx !== undefined) {
                        $(row).find('td').eq(hrIdx).addClass('status-hr text-center h-full text-sm ' + (data.hr_approval_class || 'bg-yellow'));
                    }
                }
                const noIdx = columnMap['no'];
                if (noIdx !== undefined) {
                    $(row).find('td').eq(noIdx).text(dataIndex + 1).addClass('sticky-col-left number');
                }
                const namaIdx = columnMap['nama'];
                if (namaIdx !== undefined) {
                    $(row).find('td').eq(namaIdx).addClass('sticky-col-left col-nama');
                }
                const jenisIdx = columnMap['jenis_izin'];
                if (jenisIdx !== undefined) {
                    $(row).find('td').eq(jenisIdx).addClass('col-jenis');
                }
                const tglIdx = columnMap['tanggal_formatted'];
                if (tglIdx !== undefined) {
                    $(row).find('td').eq(tglIdx).addClass('col-tanggal');
                }
                const ketIdx = columnMap['keterangan_html'];
                if (ketIdx !== undefined) {
                    $(row).find('td').eq(ketIdx).addClass('col-keterangan');
                }
                const timIdx = columnMap['tim'];
                if (timIdx !== undefined) {
                    $(row).find('td').eq(timIdx).addClass('col-tim');
                }
                const statusPegIdx = columnMap['status_pegawai'];
                if (statusPegIdx !== undefined) {
                    $(row).find('td').eq(statusPegIdx).addClass('col-status');
                }
                const divIdx = columnMap['divisi'];
                if (divIdx !== undefined) {
                    $(row).find('td').eq(divIdx).addClass('col-divisi');
                }
                const aksiIdx = columnMap['action_buttons'];
                if (aksiIdx !== undefined) {
                    $(row).find('td').eq(aksiIdx).addClass('sticky-col-right');
                }
            }
        });

        function clearDateFilter() {
            const dateInput = document.getElementById('filter-tanggal');
            if (!dateInput || !dateInput.value) return;
            const dateColIdx = columnMap['tanggal_formatted'];
            if (dateColIdx === undefined) return;
            dateInput.value = '';
            if (table && table.column(dateColIdx)) {
                table.column(dateColIdx).search('');
            }
        }

        function dateNavigate(step) {
            const input = document.getElementById('filter-tanggal');
            const colIdx = columnMap['tanggal_formatted'];
            if (colIdx === undefined || !table) return;
            if (!input.value) {
                const t = new Date();
                input.value = t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0');
            }
            const p = input.value.split('-');
            const d = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
            d.setDate(d.getDate() + step);
            input.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            table.column(colIdx).search(input.value).draw();
        }

        document.getElementById('date-prev')?.addEventListener('click', function () { dateNavigate(-1); });
        document.getElementById('date-next')?.addEventListener('click', function () { dateNavigate(1); });

        function connectFilter(filterId, colIndex) {
            const el = document.getElementById(filterId);
            if (!el) return;
            // Skip Select2 elements (handled separately to avoid double-draw)
            if (el.classList.contains('select2')) return;
            const eventType = el.tagName === 'SELECT' ? 'change' : 'input';
            el.addEventListener(eventType, function () {
                if (filterId !== 'filter-tanggal') clearDateFilter();
                if (table && table.column(colIndex)) {
                    table.column(colIndex).search(this.value).draw();
                }
            });
            if (el.value) {
                setTimeout(() => {
                    if (table && table.column(colIndex)) {
                        table.column(colIndex).search(el.value).draw();
                    }
                }, 100);
            }
        }

        // Map column indices to filter IDs
        const filterMap = {};
        filterMap[columnMap['nama']] = 'filter-nama';
        filterMap[columnMap['jenis_izin']] = 'filter-jenis';
        filterMap[columnMap['tanggal_formatted']] = 'filter-tanggal';
        filterMap[columnMap['keterangan_html']] = 'filter-keterangan';
        filterMap[columnMap['status_super_label']] = 'filter-approval-status';
        filterMap[columnMap['member_approval_html']] = 'filter-approval-member-status';
        filterMap[columnMap['hr_approval_label']] = 'filter-approval-hr-status';
        filterMap[columnMap['tim']] = 'filter-team';
        filterMap[columnMap['status_pegawai']] = 'filter-status';
        filterMap[columnMap['divisi']] = 'filter-divisi';

        Object.keys(filterMap).forEach(colIdx => {
            connectFilter(filterMap[colIdx], parseInt(colIdx));
        });

        // Select2 change -> trigger filter
        $('.select2.dt-filter').on('change', function () {
            const id = this.id;
            if (id !== 'filter-tanggal') clearDateFilter();
            let colIdx = null;
            Object.keys(filterMap).forEach(idx => {
                if (filterMap[idx] === id) colIdx = parseInt(idx);
            });
            if (colIdx !== null && table) {
                table.column(colIdx).search(this.value).draw();
            }
        });
    });

    function updateJumlahData() {
        if (!table) return;
        const info = table.page.info();
        document.getElementById('jumlah-data').textContent = 'Jumlah Data: ' + info.recordsDisplay;
    }

    const toggleButton = document.getElementById("toggle-dropdown");
    const dropdownForm = document.getElementById("dropdown-form");

    if (toggleButton) {
        toggleButton.addEventListener("click", (e) => {
            e.preventDefault();
            dropdownForm.classList.toggle("hidden");
        });
    }

    document.addEventListener("click", function (e) {
        if (!toggleButton.contains(e.target) && !dropdownForm.contains(e.target)) {
            dropdownForm.classList.add("hidden");
        }
    });

    let rowToDelete = null;

    window.showDeletePopup = function (row) {
        rowToDelete = row;
        document.getElementById('popupDelete').classList.replace('hidden', 'flex');
    };

    function hideDeletePopup() {
        rowToDelete = null;
        document.getElementById('popupDelete').classList.replace('flex', 'hidden');
    }

    document.getElementById('cancelDelete').addEventListener('click', hideDeletePopup);

    function drNavigate(step) {
        const input = document.getElementById('dailyReportInput');
        if (!input) return;
        const p = input.value ? input.value.split('-') : null;
        if (!p) {
            const t = new Date();
            input.value = t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0');
        } else {
            const d = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
            d.setDate(d.getDate() + step);
            input.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }
    }

    document.getElementById('dr-prev')?.addEventListener('click', function () { drNavigate(-1); });
    document.getElementById('dr-next')?.addEventListener('click', function () { drNavigate(1); });

    document.getElementById('dailyReportBtn')?.addEventListener('click', function () {
        const selectedDate = document.getElementById('dailyReportInput')?.value;
        if (!selectedDate) {
            alert('Silakan pilih tanggal terlebih dahulu');
            return;
        }
        fetch(`/iseki_rifa/public/reporting/daily-report?tanggal=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                const contentEl = document.getElementById('dailyReportContent');
                contentEl.textContent = data.text;
                showModal(document.getElementById('dailyReportText'));
            })
            .catch(err => {
                alert('Gagal memuat laporan harian');
                console.error(err);
            });
    });

    document.getElementById('confirmDelete').addEventListener('click', function () {
        if (rowToDelete) {
            const id = rowToDelete.getAttribute('data-id');
            fetch(`/iseki_rifa/public/reporting/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (response.ok) {
                        if (table) table.ajax.reload(null, false);
                        hideDeletePopup();
                    } else {
                        alert('Gagal menghapus data');
                    }
                })
                .catch(() => alert('Error saat menghapus data'));
        }
    });

    // Edit modal
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const editTanggal = document.getElementById('edit-tanggal');
    const editJenis = document.getElementById('edit-jenis');
    const editKeterangan = document.getElementById('edit-keterangan');
    const editJamMasuk = document.getElementById('edit-jam-masuk');
    const editJamKeluar = document.getElementById('edit-jam-keluar');
    const editId = document.getElementById('edit-id');

    function initEditButtons() {
        document.querySelectorAll('.edit-row').forEach(button => {
            button.addEventListener('click', () => {
                const row = button.closest('tr');
                const id = row.dataset.id;
                const cells = row.querySelectorAll('td');
                const tanggalStr = cells[columnMap['tanggal_formatted']]?.textContent.trim();
                const tanggal = tanggalStr ? tanggalStr.split('/').reverse().join('-') : '';
                const jenis = cells[columnMap['jenis_izin']]?.textContent.trim() || '';
                const keteranganEl = cells[columnMap['keterangan_html']];
                const keterangan = keteranganEl?.dataset?.keterangan || keteranganEl?.textContent?.trim() || '';

                editId.value = id;
                editTanggal.value = tanggal;
                editJenis.value = jenis;
                editKeterangan.value = keterangan;
                editJamMasuk.value = '';
                editJamKeluar.value = '';

                showModal(editModal);
                $('#edit-jenis').val(jenis).trigger('change');
            });
        });
    }

    function showApprovalNotif(message, type = 'success', duration = 2500) {
        const notif = document.getElementById('approvalNotif');
        const text = document.getElementById('approvalNotifText');
        notif.classList.remove('hidden', 'notif-success', 'notif-error', 'notif-warning');
        notif.classList.add(`notif-${type}`);
        text.textContent = message;
        setTimeout(() => {
            notif.classList.add('hidden');
        }, duration);
    }

    function attachApprovalListeners(context = document) {
        context.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function () {
                const approval = this.dataset.value;
                const form = this.closest('.approval-form');
                const id = form.dataset.id;

                fetch(`/iseki_rifa/public/reporting/${id}/approve`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ approval: approval })
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        if (table) table.ajax.reload(null, false);
                        if (data.status_label === 'Disetujui') {
                            showApprovalNotif('Perizinan berhasil disetujui', 'success');
                        } else if (data.status_label === 'Ditolak') {
                            showApprovalNotif('Perizinan ditolak', 'error');
                        } else {
                            showApprovalNotif('Status persetujuan dibatalkan', 'warning');
                        }
                    })
                    .catch(err => {
                        showApprovalNotif(err.message || 'Gagal sinkron ke sistem MIRAI', 'error');
                    });
            });
        });
    }

    editForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const id = editId.value;
        if (!id) { alert('ID data tidak ditemukan'); return; }

        const data = {
            tanggal: editTanggal.value,
            kategori: editJenis.value.trim(),
            keterangan: editKeterangan.value,
            jam_masuk: editJamMasuk.value || null,
            jam_keluar: editJamKeluar.value || null,
        };

        fetch(`/iseki_rifa/public/reporting/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(res => {
                if (!res.ok) throw new Error('Update gagal');
                if (table) table.ajax.reload(null, false);
                closeModal('editModal');
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menyimpan perubahan');
            });
    });

    function attachMemberApprovalListeners(context = document) {
        context.querySelectorAll('.member-approve-btn').forEach(button => {
            button.addEventListener('click', function () {
                const approval = this.dataset.value;
                const form = this.closest('.member-approval-form');
                const id = form.dataset.id;

                fetch(`/iseki_rifa/public/reporting/${id}/member-approve`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ approval })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (table) table.ajax.reload(null, false);
                    })
                    .catch(() => {
                        showApprovalNotif('Gagal menyimpan persetujuan member', 'error');
                    });
            });
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-view-replacement')) {
            const btn = e.target.closest('.btn-view-replacement');
            const absensiId = btn.getAttribute('data-id');

            fetch(`/iseki_rifa/public/replacements/by-absensi/${absensiId}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('replacementTableBody');
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data</td></tr>`;
                    } else {
                        data.forEach((item, index) => {
                            tbody.innerHTML += `
                                <tr>
                                    <td class="border px-4 py-2">${index + 1}</td>
                                    <td class="border px-4 py-2">${item.replacer_nik}</td>
                                    <td class="border px-4 py-2">${item.nama_pengganti ?? '-'}</td>
                                    <td class="border px-4 py-2">${item.production_number}</td>
                                    <td class="border px-4 py-2">${item.created_at}</td>
                                </tr>
                            `;
                        });
                    }
                    showModal(document.getElementById('popupReplacement'));
                });
        }
    });

    function showModal(modal) {
        modal.classList.replace('hidden', 'flex');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.replace('flex', 'hidden');
    }
</script>
@endsection