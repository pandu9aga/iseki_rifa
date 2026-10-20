@extends('layouts.app')

@section('content')
<style>
    .badge-container {
        margin: 1em 0;
    }

    div#cuti-table_wrapper .dt-layout-row {
        align-items: center;
    }

    div#cuti-table_wrapper .dt-info {
        padding-top: 6px;
    }

    /* Styling Tabel Reporting - Ultra Compact & Controlled Width like lembur */
    .table-scroll-wrapper {
        width: 100%;
        max-height: 72vh;
        overflow-x: auto;
    }

    #cuti-table {
        width: 100% !important;
        min-width: 1560px !important;
        table-layout: fixed !important;
        border-collapse: collapse;
    }

    #cuti-table th,
    #cuti-table td {
        padding: 0.35rem 0.5rem !important;
        font-size: 0.85rem !important;
        white-space: nowrap !important;
        vertical-align: middle;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        box-sizing: border-box;
    }

    #cuti-table thead th {
        padding: 0.4rem 0.4rem !important;
        font-weight: 600;
        text-align: center;
        background: var(--primary-50, #fff0f6);
        font-size: 0.84rem !important;
    }

    #cuti-table thead tr:first-child th {
        padding: 0.3rem 0.3rem !important;
        white-space: normal !important;
        line-height: 1.2;
        vertical-align: middle;
        word-break: normal;
        overflow-wrap: break-word;
    }

    #cuti-table thead tr:last-child th {
        padding: 0.18rem 0.3rem !important;
        position: relative;
    }

    /* Lebar kolom lebih leluasa & lega */
    #cuti-table .col-no {
        width: 40px !important;
        max-width: 40px !important;
    }

    #cuti-table .col-nama {
        width: 155px !important;
        max-width: 155px !important;
    }

    #cuti-table .col-jenis {
        width: 130px !important;
        max-width: 130px !important;
    }

    #cuti-table .col-tanggal {
        width: 120px !important;
        max-width: 120px !important;
    }

    #cuti-table .col-keterangan {
        width: 140px !important;
        max-width: 140px !important;
    }

    #cuti-table .col-sisa-cuti {
        width: 65px !important;
        max-width: 65px !important;
    }

    #cuti-table .col-approval {
        width: 105px !important;
        max-width: 105px !important;
    }

    #cuti-table .col-status-super {
        width: 110px !important;
        max-width: 110px !important;
    }

    #cuti-table .col-dokumen {
        width: 80px !important;
        max-width: 80px !important;
    }

    #cuti-table .col-member {
        width: 110px !important;
        max-width: 110px !important;
    }

    #cuti-table .col-hr {
        width: 110px !important;
        max-width: 110px !important;
    }

    #cuti-table .col-pengganti {
        width: 85px !important;
        max-width: 85px !important;
    }

    #cuti-table .col-tim {
        width: 85px !important;
        max-width: 85px !important;
    }

    #cuti-table .col-status {
        width: 80px !important;
        max-width: 80px !important;
    }

    #cuti-table .col-divisi {
        width: 95px !important;
        max-width: 95px !important;
    }

    #cuti-table .col-aksi {
        width: 75px !important;
        max-width: 75px !important;
    }

    /* Filter input & Select2 */
    #cuti-table thead input,
    #cuti-table thead select:not(.select2-hidden-accessible) {
        padding: 0.18rem 0.3rem !important;
        font-size: 0.78rem !important;
        height: 26px !important;
        min-width: 0 !important;
        width: 100% !important;
        border-radius: 3px !important;
        border: 1px solid var(--border-input, #ccc) !important;
        box-sizing: border-box;
    }

    /* Mencegah hidden select dari select2 meluber dan merusak lebar scroll container */
    #cuti-table thead select.select2-hidden-accessible {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        border: 0 !important;
        min-width: 0 !important;
        max-width: 1px !important;
    }

    #cuti-table .select2-container {
        min-width: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    #cuti-table .select2-container .select2-selection--single {
        height: 26px !important;
        min-height: 26px !important;
        font-size: 0.78rem !important;
        padding: 0 !important;
    }

    #cuti-table .select2-container .select2-selection__rendered {
        line-height: 24px !important;
        padding-left: 3px !important;
        padding-right: 14px !important;
        font-size: 0.76rem !important;
    }

    #cuti-table .select2-container .select2-selection__arrow {
        height: 24px !important;
        width: 14px !important;
    }

    /* Sticky columns - solid backgrounds & separation shadows */
    #cuti-table th.sticky-col-left,
    #cuti-table td.sticky-col-left {
        position: sticky;
        left: 0;
        z-index: 2;
    }

    #cuti-table th.sticky-col-left:first-child,
    #cuti-table td.sticky-col-left:first-child {
        left: 0;
    }

    #cuti-table th.sticky-col-left.col-nama,
    #cuti-table td.sticky-col-left.col-nama {
        left: 40px;
        box-shadow: 2px 0 4px rgba(0, 0, 0, 0.06);
    }

    #cuti-table thead th.sticky-col-left {
        background: var(--primary-50, #fff0f6) !important;
        z-index: 1070;
    }

    #cuti-table tbody tr:nth-child(even) td.sticky-col-left {
        background: #fafafa;
    }

    #cuti-table tbody tr:nth-child(odd) td.sticky-col-left {
        background: #ffffff;
    }

    #cuti-table tbody tr:hover td.sticky-col-left {
        background: var(--primary-50, #fff0f6);
    }

    #cuti-table th.sticky-col-right,
    #cuti-table td.sticky-col-right {
        position: sticky;
        right: 0;
        z-index: 2;
        box-shadow: -2px 0 4px rgba(0, 0, 0, 0.06);
    }

    #cuti-table thead th.sticky-col-right {
        background: var(--primary-50, #fff0f6) !important;
        z-index: 1070;
    }

    #cuti-table tbody tr:nth-child(even) td.sticky-col-right {
        background: #fafafa;
    }

    #cuti-table tbody tr:nth-child(odd) td.sticky-col-right {
        background: #ffffff;
    }

    #cuti-table tbody tr:hover td.sticky-col-right {
        background: var(--primary-50, #fff0f6);
    }

    /* Tombol dalam tabel compact */
    #cuti-table .btn {
        padding: 0.15rem 0.35rem !important;
        font-size: 0.75rem !important;
        border-radius: 3px !important;
        min-width: auto !important;
        line-height: 1.1 !important;
    }

    #cuti-table .btn-group {
        gap: 0.15rem !important;
        justify-content: center;
        display: flex;
    }

    #cuti-table .btn-icon {
        padding: 0.1rem 0.2rem !important;
    }

    #cuti-table .btn-icon i {
        font-size: 0.95rem !important;
    }

    #cuti-table td.status-super,
    #cuti-table td.status-member,
    #cuti-table td.status-hr {
        padding: 0.15rem 0.25rem !important;
        font-weight: 600;
        text-align: center;
        font-size: 0.74rem !important;
    }

    /* Badge status compact */
    #cuti-table .badge,
    #cuti-table .status-badge {
        font-size: 0.72rem !important;
        padding: 0.1rem 0.35rem !important;
        line-height: 1.2;
    }

    /* Dokumen thumbnail & upload slot compact */
    #cuti-table .dokumen-thumb {
        width: 24px !important;
        height: 24px !important;
        object-fit: cover;
        border-radius: 3px;
        cursor: pointer;
    }
</style>
<main>
    @include('components.popupEdit')
    @include('components.popupDailyReport')
    @include('components.popupPreviewDokumen')

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

        <span class="badge {{ $isSubmitted ? 'badge-pink' : 'badge-warning' }}">
            {{ ucwords($team) }}
        </span>
        @endforeach
    </div>

    <div class="card">
        <h5 style="margin-bottom:0.75rem;font-weight:600;">Leader Yang Telah Submit :</h5>
        <div class="table-scroll-wrapper" style="border:1px solid var(--border);border-radius:var(--radius-sm);">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Team</th>
                        <th>Submit Laporan</th>
                        <th>Update Laporan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportToday as $i => $report)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $report->user?->name ?? '-' }}</td>
                        <td>{{ $report->user?->division ?? 'Tanpa Divisi' }}</td>
                        <td>{{ is_array($report->user?->team) ? implode(', ', $report->user->team) : $report->user?->team ?? 'Tanpa Team' }}</td>
                        <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $report->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br>

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

        <span class="badge {{ $isSubmitted ? 'badge-pink' : 'badge-warning' }}">
            {{ ucwords($team) }}
        </span>
        @endforeach
    </div>

    <div class="card">
        <h5 style="margin-bottom:0.75rem;font-weight:600;">Leader Yang Telah Submit :</h5>
        <div class="table-scroll-wrapper" style="border:1px solid var(--border);border-radius:var(--radius-sm);">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Team</th>
                        <th>Submit Laporan</th>
                        <th>Update Laporan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportToday as $i => $report)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $report->user?->name ?? '-' }}</td>
                        <td>{{ $report->user?->division ?? 'Tanpa Divisi' }}</td>
                        <td>{{ is_array($report->user?->team) ? implode(', ', $report->user->team) : $report->user?->team ?? 'Tanpa Team' }}</td>
                        <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $report->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br>

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

        <span class="badge {{ $isSubmitted ? 'badge-pink' : 'badge-warning' }}">
            {{ ucwords($team) }}
        </span>
        @endforeach
    </div>

    <div class="card">
        <h5 style="margin-bottom:0.75rem;font-weight:600;">Leader Yang Telah Submit :</h5>
        <div class="table-scroll-wrapper" style="border:1px solid var(--border);border-radius:var(--radius-sm);">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Team</th>
                        <th>Submit Laporan</th>
                        <th>Update Laporan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportToday as $i => $report)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $report->user?->name ?? '-' }}</td>
                        <td>{{ $report->user?->division ?? 'Tanpa Divisi' }}</td>
                        <td>{{ is_array($report->user?->team) ? implode(', ', $report->user->team) : $report->user?->team ?? 'Tanpa Team' }}</td>
                        <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $report->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br>

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
                    <label for="dropdown-bulan" class="text-sm font-medium w-full flex gap-sm-1">Pilih Bulan</label>
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
                    <label for="dropdown-bulan" class="text-sm font-medium w-full flex gap-sm-1">Pilih Bulan</label>
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

    <div id="approvalNotif" class="notif-toast hidden">
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
                    <th rowspan="2" class="sticky-col-left col-no">No</th>
                    <th class="sticky-col-left col-nama">Nama</th>
                    <th class="col-jenis">Jenis Izin</th>
                    <th class="col-tanggal">Tanggal</th>
                    <th class="col-keterangan">Keterangan</th>
                    <th rowspan="2" class="col-sisa-cuti">Sisa Cuti</th>
                    @userType('super')
                    <th rowspan="2" class="col-approval">Approvement</th>
                    @enduserType
                    <th class="col-status-super">Status Persetujuan</th>
                    <th rowspan="2" class="col-dokumen">Dokumen</th>
                    <th class="col-member">Persetujuan Member</th>
                    <th class="col-hr">Persetujuan HR</th>
                    @if(!$isEmployeeReporting)
                    <th rowspan="2" class="col-pengganti">Pengganti</th>
                    @endif
                    <th class="col-tim">Tim</th>
                    <th class="col-status">Status</th>
                    <th class="col-divisi">Divisi</th>
                    <th rowspan="2" class="sticky-col-right col-aksi">Aksi</th>
                </tr>
                <tr>
                    <th class="sticky-col-left col-nama">
                        <input class="filter dt-filter" id="filter-nama" type="text" placeholder="Nama"
                            value="{{ $isEmployeeReporting ? session('employee_user')->name : '' }}"
                            {{ $isEmployeeReporting ? 'readonly' : '' }} />
                    </th>
                    <th class="col-jenis">
                        <select name="jenis_cuti" class="select2 dt-filter" data-placeholder="Jenis"
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
                    <th class="col-tanggal">
                        <div style="display:flex; align-items:center; gap:2px;">
                            <button type="button" class="btn btn-sm btn-secondary" id="date-prev" style="padding:1px 4px; line-height:1; font-size:0.75rem;">&#8249;</button>
                            <input class="filter dt-filter" id="filter-tanggal" type="date"
                                value="{{ $isEmployeeReporting ? '' : date('Y-m-d') }}" style="flex:1; min-width:0; padding:1px 3px !important; font-size:0.75rem !important; height:26px !important;" />
                            <button type="button" class="btn btn-sm btn-secondary" id="date-next" style="padding:1px 4px; line-height:1; font-size:0.75rem;">&#8250;</button>
                        </div>
                    </th>
                    <th class="col-keterangan"><input type="text" class="filter dt-filter" id="filter-keterangan" placeholder="Ket" /></th>
                    <th class="col-status-super">
                        <select name="approval_status" class="select2 dt-filter"
                            data-placeholder="Status" data-allow-clear="true" style="width: 100%"
                            id="filter-approval-status">
                            <option></option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </th>
                    <th class="col-member">
                        <select name="approval_member_status" class="select2 dt-filter"
                            data-placeholder="Member" data-allow-clear="true" style="width: 100%"
                            id="filter-approval-member-status" {{ $isEmployeeReporting ? 'disabled' : '' }}>
                            <option></option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </th>
                    <th class="col-hr">
                        <select name="approval_hr_status" class="select2 dt-filter"
                            data-placeholder="HR" data-allow-clear="true" style="width: 100%"
                            id="filter-approval-hr-status">
                            <option></option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </th>
                    <th class="col-tim"><input class="filter dt-filter" id="filter-team" type="text" placeholder="Tim" /></th>
                    <th class="col-status">
                        <select name="status" class="select2 dt-filter" data-placeholder="Status"
                            data-allow-clear="true" style="width: 100%" id="filter-status">
                            <option></option>
                            <option value="Direct">Direct</option>
                            <option value="Non Direct">Non Direct</option>
                        </select>
                    </th>
                    <th class="col-divisi">
                        <select name="divisi" class="select2 dt-filter" data-placeholder="Divisi"
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

    const columns = [{
            data: 'no',
            orderable: false,
            searchable: false,
            width: '40px',
            className: 'text-center col-no'
        },
        {
            data: 'nama',
            width: '155px',
            className: 'col-nama'
        },
        {
            data: 'jenis_izin',
            width: '130px',
            className: 'col-jenis'
        },
        {
            data: 'tanggal_formatted',
            width: '120px',
            className: 'text-center col-tanggal'
        },
        {
            data: 'keterangan_html',
            width: '140px',
            className: 'col-keterangan'
        },
        {
            data: 'sisa_cuti',
            width: '65px',
            className: 'text-center col-sisa-cuti'
        },
    ];
    if (userType === 'super') {
        columns.push({
            data: 'approval_buttons',
            orderable: false,
            searchable: false,
            width: '105px',
            className: 'text-center col-approval'
        });
    }
    columns.push({
        data: 'status_super_label',
        width: '110px',
        className: 'text-center col-status-super'
    }, {
        data: 'dokumen_upload_html',
        orderable: false,
        searchable: false,
        width: '80px',
        className: 'text-center col-dokumen'
    }, {
        data: 'member_approval_html',
        width: '110px',
        className: 'text-center col-member'
    }, {
        data: 'hr_approval_label',
        width: '110px',
        className: 'text-center col-hr'
    }, );
    if (!isEmployee) {
        columns.push({
            data: 'replacement_info',
            orderable: false,
            searchable: false,
            width: '85px',
            className: 'text-center col-pengganti'
        });
    }
    columns.push({
        data: 'tim',
        width: '85px',
        className: 'text-center col-tim'
    }, {
        data: 'status_pegawai',
        width: '80px',
        className: 'text-center col-status'
    }, {
        data: 'divisi',
        width: '95px',
        className: 'text-center col-divisi'
    }, {
        data: 'action_buttons',
        orderable: false,
        searchable: false,
        width: '75px',
        className: 'text-center col-aksi'
    }, );

    const columnMap = {};
    columns.forEach((col, idx) => {
        columnMap[col.data] = idx;
    });

    let table;
    const initialTanggal = document.getElementById('filter-tanggal')?.value || '';
    const initialSearchCols = columns.map(col => {
        if (col.data === 'tanggal_formatted' && initialTanggal) {
            return {
                search: initialTanggal
            };
        }
        return null;
    });

    $(document).ready(function() {
        table = $('#cuti-table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            searchCols: initialSearchCols,
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
            drawCallback: function() {
                updateJumlahData();
                attachApprovalListeners(document);
                attachMemberApprovalListeners(document);
                initEditButtons();
            },
            createdRow: function(row, data, dataIndex) {
                $(row).attr('data-id', data.id);
                const statusSuper = data.status_super;
                if (statusSuper) {
                    const statusCell = $(row).find('td').eq(columnMap['status_super_label']);
                    statusCell.addClass('status-super text-center h-full text-sm ' + statusSuper.class);

                    const memberIdx = columnMap['member_approval_html'];
                    if (memberIdx !== undefined) {
                        $(row).find('td').eq(memberIdx).addClass('status-member text-center h-full text-sm ' + (data.member_approval_class || 'bg-yellow'));
                    }

                    const hrIdx = columnMap['hr_approval_label'];
                    if (hrIdx !== undefined) {
                        $(row).find('td').eq(hrIdx).addClass('status-hr text-center h-full text-sm ' + (data.hr_approval_class || 'bg-yellow'));
                    }
                }
                const noIdx = columnMap['no'];
                if (noIdx !== undefined) {
                    $(row).find('td').eq(noIdx).text(dataIndex + 1).addClass('sticky-col-left number col-no text-center');
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
                    $(row).find('td').eq(tglIdx).addClass('col-tanggal text-center');
                }
                const ketIdx = columnMap['keterangan_html'];
                if (ketIdx !== undefined) {
                    $(row).find('td').eq(ketIdx).addClass('col-keterangan');
                }
                const timIdx = columnMap['tim'];
                if (timIdx !== undefined) {
                    $(row).find('td').eq(timIdx).addClass('col-tim text-center');
                }
                const statusPegIdx = columnMap['status_pegawai'];
                if (statusPegIdx !== undefined) {
                    $(row).find('td').eq(statusPegIdx).addClass('col-status text-center');
                }
                const divIdx = columnMap['divisi'];
                if (divIdx !== undefined) {
                    $(row).find('td').eq(divIdx).addClass('col-divisi text-center');
                }
                const aksiIdx = columnMap['action_buttons'];
                if (aksiIdx !== undefined) {
                    $(row).find('td').eq(aksiIdx).addClass('sticky-col-right col-aksi text-center');
                }
            }
        });

        function syncMobileDate(val) {
            const mDate = document.getElementById('mobile-absensi-filter-tanggal');
            if (mDate) mDate.value = val || '';
        }

        function clearDateFilter() {
            const dateInput = document.getElementById('filter-tanggal');
            if (!dateInput || !dateInput.value) return;
            const dateColIdx = columnMap['tanggal_formatted'];
            if (dateColIdx === undefined) return;
            dateInput.value = '';
            syncMobileDate('');
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
            syncMobileDate(input.value);
            table.column(colIdx).search(input.value).draw();
        }

        document.getElementById('date-prev')?.addEventListener('click', function() {
            dateNavigate(-1);
        });
        document.getElementById('date-next')?.addEventListener('click', function() {
            dateNavigate(1);
        });

        function connectFilter(filterId, colIndex) {
            const el = document.getElementById(filterId);
            if (!el) return;
            // Skip Select2 elements (handled separately to avoid double-draw)
            if (el.classList.contains('select2')) return;
            const eventType = el.tagName === 'SELECT' ? 'change' : 'input';
            el.addEventListener(eventType, function() {
                if (filterId !== 'filter-tanggal') clearDateFilter();
                if (table && table.column(colIndex)) {
                    table.column(colIndex).search(this.value).draw();
                }
            });
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
        $('.select2.dt-filter').on('change', function() {
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

    document.addEventListener("click", function(e) {
        if (toggleButton && dropdownForm && !toggleButton.contains(e.target) && !dropdownForm.contains(e.target)) {
            dropdownForm.classList.add("hidden");
        }
    });

    let rowToDelete = null;

    window.showDeletePopup = function(row) {
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

    document.getElementById('dr-prev')?.addEventListener('click', function() {
        drNavigate(-1);
    });
    document.getElementById('dr-next')?.addEventListener('click', function() {
        drNavigate(1);
    });

    document.getElementById('dailyReportBtn')?.addEventListener('click', function() {
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

    document.getElementById('confirmDelete').addEventListener('click', function() {
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
                if ($('#edit-jenis').hasClass('select2-hidden-accessible')) {
                    $('#edit-jenis').select2('destroy');
                }
                $('#edit-jenis').select2({
                    dropdownParent: $('#editModal .modal-content')
                });
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
            button.addEventListener('click', function() {
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
                        body: JSON.stringify({
                            approval: approval
                        })
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

    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = editId.value;
        if (!id) {
            alert('ID data tidak ditemukan');
            return;
        }

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
        // Attach preview listeners untuk file input dokumen
        context.querySelectorAll('.dokumen-input').forEach(input => {
            input.addEventListener('change', function() {
                const previewId = this.dataset.target;
                const placeholderId = this.dataset.placeholder;
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                const label = this.closest('.dokumen-slot').querySelector('.dokumen-label');

                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (preview) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                        }
                        if (placeholder) {
                            placeholder.classList.add('hidden');
                        }
                        if (label) {
                            label.classList.add('has-file');
                        }
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    if (preview) {
                        preview.src = '';
                        preview.classList.add('hidden');
                    }
                    if (placeholder) {
                        placeholder.classList.remove('hidden');
                    }
                    if (label) {
                        label.classList.remove('has-file');
                    }
                }
            });
        });

        context.querySelectorAll('.member-approve-btn').forEach(button => {
            button.addEventListener('click', function() {
                const approval = this.dataset.value;
                const form = this.closest('.member-approval-form');
                const id = form.dataset.id;
                const row = form.closest('tr');

                // Cari dokumen cell di row yang sama
                const dokumenCell = row ? row.querySelector('.dokumen-cell') : null;
                const butuhDokumen = dokumenCell && dokumenCell.dataset.butuhDokumen === '1';

                // Jika approve dan butuh dokumen, validasi file di kolom dokumen
                if (approval === '1' && butuhDokumen) {
                    const dok1Input = dokumenCell.querySelector('input[name="dokumen_1"]');
                    if (!dok1Input || !dok1Input.files || dok1Input.files.length === 0) {
                        showApprovalNotif('Dokumen 1 wajib diunggah sebelum menyetujui!', 'error', 3500);
                        return;
                    }
                }

                // Gunakan FormData agar bisa kirim file
                const formData = new FormData();
                formData.append('approval', approval);
                formData.append('_method', 'PUT');

                // Ambil file dari kolom dokumen
                if (butuhDokumen && approval === '1' && dokumenCell) {
                    const dok1 = dokumenCell.querySelector('input[name="dokumen_1"]');
                    const dok2 = dokumenCell.querySelector('input[name="dokumen_2"]');
                    if (dok1 && dok1.files[0]) formData.append('dokumen_1', dok1.files[0]);
                    if (dok2 && dok2.files[0]) formData.append('dokumen_2', dok2.files[0]);
                }

                // Loading state
                form.style.position = 'relative';
                form.classList.add('uploading');

                fetch(`/iseki_rifa/public/reporting/${id}/member-approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        form.classList.remove('uploading');
                        if (table) table.ajax.reload(null, false);
                        if (approval === '1') {
                            showApprovalNotif('Persetujuan member berhasil disimpan', 'success');
                        } else if (approval === '0') {
                            showApprovalNotif('Perizinan ditolak oleh member', 'error');
                        } else {
                            showApprovalNotif('Persetujuan member dibatalkan', 'warning');
                        }
                    })
                    .catch(err => {
                        form.classList.remove('uploading');
                        const msg = err.message || err.errors?.dokumen_1?.[0] || 'Gagal menyimpan persetujuan member';
                        showApprovalNotif(msg, 'error', 4000);
                    });
            });
        });
    }

    document.addEventListener('click', function(e) {
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

    // ===== DOKUMEN PREVIEW MODAL =====
    let currentDokumenAbsenId = null;

    function openDokumenPreviewModal(el) {
        const absenId = el.dataset.absenId;
        const nama = el.dataset.nama || '-';
        const dok1 = el.dataset.dok1 || '';
        const dok2 = el.dataset.dok2 || '';
        const canEdit = el.dataset.canEdit === '1';
        const memberApproved = el.dataset.memberApproved;

        currentDokumenAbsenId = absenId;

        // Set nama
        const namaEl = document.getElementById('preview-dokumen-nama');
        if (namaEl) namaEl.textContent = nama;

        // Dokumen 1
        const dok1Img = document.getElementById('preview-dok1-img');
        const dok1Placeholder = document.getElementById('preview-dok1-placeholder');
        if (dok1) {
            dok1Img.src = dok1;
            dok1Img.style.display = 'block';
            dok1Placeholder.style.display = 'none';
        } else {
            dok1Img.src = '';
            dok1Img.style.display = 'none';
            dok1Placeholder.style.display = 'flex';
        }

        // Dokumen 2
        const dok2Img = document.getElementById('preview-dok2-img');
        const dok2Placeholder = document.getElementById('preview-dok2-placeholder');
        const dok2Section = document.getElementById('preview-dok2-section');
        if (dok2) {
            dok2Img.src = dok2;
            dok2Img.style.display = 'block';
            dok2Placeholder.style.display = 'none';
            if (dok2Section) dok2Section.style.display = 'block';
        } else {
            dok2Img.src = '';
            dok2Img.style.display = 'none';
            dok2Placeholder.style.display = 'flex';
            // Tampilkan placeholder "Tidak ada dokumen 2"
            if (dok2Section) dok2Section.style.display = 'block';
        }

        // Tombol Edit (hanya member yang belum di-approve)
        const showEdit = canEdit && memberApproved === 'null';
        document.querySelectorAll('.dokumen-edit-btn').forEach(btn => {
            btn.style.display = showEdit ? 'inline-flex' : 'none';
            if (showEdit) btn.classList.remove('hidden');
        });
        const saveBtn = document.getElementById('save-dokumen-btn');
        if (saveBtn) {
            saveBtn.style.display = showEdit ? 'inline-flex' : 'none';
            if (showEdit) saveBtn.classList.remove('hidden');
        }

        // Show modal
        const modal = document.getElementById('previewDokumenModal');
        if (modal) modal.classList.replace('hidden', 'flex');
    }

    function closeDokumenPreviewModal() {
        const modal = document.getElementById('previewDokumenModal');
        if (modal) modal.classList.replace('flex', 'hidden');
        currentDokumenAbsenId = null;
    }

    function zoomDokumenImage(img) {
        if (!img || !img.src) return;
        const zoomModal = document.getElementById('zoomDokumenModal');
        const zoomImg = document.getElementById('zoom-dokumen-img');
        if (zoomModal && zoomImg) {
            zoomImg.src = img.src;
            zoomModal.classList.replace('hidden', 'flex');
        }
    }

    function closeZoomDokumen() {
        const zoomModal = document.getElementById('zoomDokumenModal');
        if (zoomModal) zoomModal.classList.replace('flex', 'hidden');
    }

    function saveDokumenEdit() {
        if (!currentDokumenAbsenId) return;

        const dok1Input = document.getElementById('edit-dokumen-1-input');
        const dok2Input = document.getElementById('edit-dokumen-2-input');

        if ((!dok1Input || !dok1Input.files.length) && (!dok2Input || !dok2Input.files.length)) {
            alert('Pilih minimal satu dokumen untuk diganti.');
            return;
        }

        const formData = new FormData();
        formData.append('_method', 'PUT');
        if (dok1Input && dok1Input.files[0]) formData.append('dokumen_1', dok1Input.files[0]);
        if (dok2Input && dok2Input.files[0]) formData.append('dokumen_2', dok2Input.files[0]);

        const saveBtn = document.getElementById('save-dokumen-btn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="material-symbols-rounded" style="font-size:18px;">hourglass_empty</i> Menyimpan...';
        }

        fetch(`/iseki_rifa/public/reporting/${currentDokumenAbsenId}/update-dokumen`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                closeDokumenPreviewModal();
                if (table) table.ajax.reload(null, false);
                showApprovalNotif('Dokumen berhasil diperbarui', 'success');
            })
            .catch(err => {
                const msg = err.message || 'Gagal menyimpan dokumen';
                showApprovalNotif(msg, 'error', 4000);
            })
            .finally(() => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="material-symbols-rounded" style="font-size:18px;">save</i> Simpan Perubahan';
                }
                // Reset file inputs
                if (dok1Input) dok1Input.value = '';
                if (dok2Input) dok2Input.value = '';
            });
    }

    // File input preview untuk edit modal dokumen
    document.getElementById('edit-dokumen-1-input')?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview-dok1-img');
                const placeholder = document.getElementById('preview-dok1-placeholder');
                if (img) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    document.getElementById('edit-dokumen-2-input')?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview-dok2-img');
                const placeholder = document.getElementById('preview-dok2-placeholder');
                if (img) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Event delegation: handle klik thumbnail dokumen dari DataTables (dynamic rows)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.dokumen-preview-btn');
        if (btn) {
            e.stopPropagation();
            openDokumenPreviewModal(btn);
        }
    });

    // Keyboard shortcuts untuk modal dokumen
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('previewDokumenModal');
        const zoomModal = document.getElementById('zoomDokumenModal');

        if (zoomModal && !zoomModal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeZoomDokumen();
            return;
        }
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeDokumenPreviewModal();
        }
    });
</script>
@endsection