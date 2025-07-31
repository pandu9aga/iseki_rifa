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

    .badge-submitted {
        background-color: #ec057d;
        color: white;
    }

    .badge-pending {
        background-color: #ccc;
        color: #333;
    }
</style>
<main>
    @include('components.popupEdit')
    @include('components.popupDailyReport')

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
                    <td class="border px-4 py-2">{{ $report->user->name }}</td>
                    <td class="border px-4 py-2">{{ $report->user->division ?? 'Tanpa Divisi' }}</td>
                    <td class="border px-4 py-2">
                        {{ is_array($report->user->team) ? implode(', ', $report->user->team) : ($report->user->team ?? 'Tanpa Team') }}
                    </td>
                    <td class="border px-4 py-2">{{ $report->created_at->format('Y-m-d H:i') }}</td>
                    <td class="border px-4 py-2">{{ $report->updated_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
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
            <input type="date" id="dailyReportInput" class="form-control" value="{{ now()->format('Y-m-d'); }}" />
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

    {{-- <a href="{{ route('reporting.pdf') }}" class="btn btn-primary">Download PDF</a> --}}
    <a href="{{ route('reporting.excel') }}" class="btn btn-primary">Data Perizinan <i class="material-symbols-rounded">download</i></a>

    <p id="jumlah-data" class="flex justify-end mb-2 text-sm">
        Jumlah Data: {{ count($absensis) }}
    </p>

    @csrf
    <section class="container-table">
        <table id="cuti-table">
            <thead>
                <tr>
                    <th rowspan="2" class="sticky-col-left">No</th>
                    <th class="sticky-col-left">Nama</th>
                    <th>Jenis Izin</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Status Persetujuan</th>
                    @userType('admin')
                    <th rowspan="2">Approvement</th>
                    @enduserType
                    <th>Status</th>
                    <th>Divisi</th>
                    <th>Tim</th>
                    <th rowspan="2" class="sticky-col-right">Aksi</th>
                </tr>
                <tr>
                    <th class="sticky-col-left"><input class="filter" id="filter-nama" type="text" placeholder="Cari Nama" /></th>
                    <th>
                        <select name="jenis_cuti" class="select2 filter" data-placeholder="Pilih jenis izin" data-allow-clear="true" style="width: 100%;" id="filter-jenis">
                            <option></option>
                            <option value="Cuti">Cuti</option>
                            <option value="Cuti Setengah Hari Pagi">Cuti Setengah Hari Pagi</option>
                            <option value="Cuti Setengah Hari Siang">Cuti Setengah Hari Siang</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Izin Keluar">Izin Keluar</option>
                            <option value="Pulang Cepat">Pulang Cepat</option>
                            <option value="Absen">Absen</option>
                            <option value="Absen Setengah Hari Pagi">Absen Setengah Hari Pagi</option>
                            <option value="Absen Setengah Hari Siang">Absen Setengah Hari Siang</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Cuti Khusus">Cuti Khusus</option>
                            <option value="Serikat">Serikat</option>
                        </select>
                    </th>
                    {{-- <th><input class="filter" id="filter-tanggal" type="month" value="{{ request('bulan', now()->format('Y-m')) }}" /></th> --}}
                    <th><input class="filter" id="filter-tanggal" type="date" value="{{ request('tanggal', now()->format('Y-m-d')) }}" /></th>
                    <th><input type="text" class="filter" id="filter-keterangan" placeholder="Cari Keterangan" /></th>
                    <th>
                        <select name="approval_status" class="select2 filter" data-placeholder="Pilih status persetujuan" data-allow-clear="true" style="width: 100%" id="filter-approval-status">
                            <option></option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </th>
                    <th>
                        <select name="status" class="select2 filter" data-placeholder="Pilih status" data-allow-clear="true" style="width: 100%" id="filter-status">
                            <option></option>
                            <option value="Permanent">Permanent</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </th>
                    <th>
                        <select name="divisi" class="select2 filter" data-placeholder="Pilih divisi" data-allow-clear="true" style="width: 100%" id="filter-divisi">
                            <option></option>
                            @foreach($divisions as $division)
                            <option value="{{ $division->nama }}">{{ $division-> nama }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th><input class="filter" id="filter-team" type="text" placeholder="Cari Tim" /></th>
                </tr>
            </thead>

            <tbody>
                @foreach($absensis as $index => $absen)
                <tr data-id="{{ $absen->id }}">
                    {{-- <td class="sticky-col-left number">{{ $index + 1 }}</td> --}}
                    <td class="sticky-col-left number">{{ $loop->iteration }}</td>
                    <td class="sticky-col-left">{{ $absen->employee->nama ?? '-' }}</td>
                    <td>{{ $absen->kategori_label }}</td>
                    <td>{{ $absen->tanggal->format('d/m/Y') }}</td>
                    <td>{!! $absen->keterangan ? nl2br(e($absen->keterangan)) : '-' !!}</td>
                    @php
                    if (is_null($absen->is_approved)) {
                    $status = 'Menunggu Persetujuan';
                    $stylingClass = 'bg-yellow';
                    } elseif ($absen->is_approved) {
                    $status = 'Disetujui';
                    $stylingClass = 'bg-success';
                    } else {
                    $status = 'Ditolak';
                    $stylingClass = 'bg-red';
                    }
                    @endphp

                    <td class="status text-center h-full text-sm {{ $stylingClass }}">
                        <span>
                            {{ $status }}
                        </span>
                    </td>
                    @userType('admin')
                    <td>
                        <form class="approval-form" data-id="{{ $absen->id }}">
                            @csrf
                            @method('PUT')
                            @if (is_null($absen->is_approved))
                            <div class="flex flex-col btn-group">
                                <button type="button" data-value="1" class="btn bg-success text-sm rounded approve-btn">Setujui</button>
                                <button type="button" data-value="0" class="btn bg-red text-sm rounded approve-btn">Tolak</button>
                            </div>
                            @else
                            <button type="button" data-value="null" class="btn bg-red text-sm rounded approve-btn">Batalkan</button>
                            @endif
                        </form>

                    </td>
                    @enduserType
                    <td>{{ $absen->employee->status ?? 'Contract' }}</td>
                    <td>{{ $absen->employee->division->nama ?? '-' }}</td>
                    <td>{{ $absen->employee->team ?? '-' }}</td>
                    <td class="sticky-col-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-icon edit-row">
                                <i class="material-symbols-rounded delete-row btn-primary">
                                    edit_square
                                </i>
                            </button>
                            <button type="button" class="btn btn-icon danger delete-row"
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
                    @userType('leader')
                    <td colspan="10" class="text-gray-500 py-4">Data tidak ditemukan</td>
                    @enduserType
                    @userType('admin')
                    <td colspan="11" class="text-gray-500 py-4">Data tidak ditemukan</td>
                    @enduserType
                </tr>
            </tbody>
        </table>
    </section>
    @include('components.popupDelete')
</main>

<script>
    // Update jumlah data (setelah filter, oninput, setelah delete)
    document.addEventListener('DOMContentLoaded', () => {
        () => updateJumlahData('cuti-table', 'jumlah-data');
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        setTimeout(() => updateJumlahData('cuti-table', 'jumlah-data'), 300);
    });

    const toggleButton = document.getElementById("toggle-dropdown");
    const dropdownForm = document.getElementById("dropdown-form");

    if (toggleButton) {
        toggleButton.addEventListener("click", (e) => {
            e.preventDefault();
            dropdownForm.classList.toggle("hidden");
        });
    }

    // Tutup dropdown kalau klik di luar
    document.addEventListener("click", function(e) {
        if (!toggleButton.contains(e.target) && !dropdownForm.contains(e.target)) {
            dropdownForm.classList.add("hidden");
        }
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
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const editTanggal = document.getElementById('edit-tanggal');
    const editJenis = document.getElementById('edit-jenis');
    const editKeterangan = document.getElementById('edit-keterangan');
    const editId = document.getElementById('edit-id');

    // Show Edit Modal
    document.querySelectorAll('.edit-row').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const tanggal = row.children[3].textContent.split('/').reverse().join('-');
            const jenis = row.children[2].textContent;
            const keterangan = row.children[4].textContent;

            editId.value = id;
            editTanggal.value = tanggal;
            editJenis.value = jenis;
            editKeterangan.value = keterangan;

            showModal(editModal);
            $('#edit-jenis').val(jenis).trigger('change');
        });
    });

    // Function untuk approve perizinan
    attachApprovalListeners();

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
                    .then(response => response.json())
                    .then(data => {
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        const statusCell = row.querySelector('.status');

                        statusCell.textContent = data.status_label;
                        statusCell.className = `status text-center h-full text-sm ${data.status_class}`;

                        form.innerHTML = data.button_html;
                        attachApprovalListeners(form);
                    })
                    .catch(() => alert('Gagal memperbarui status'));
            });
        });
    }

    // Show modal
    function showModal(modal) {
        modal.classList.replace('hidden', 'flex');
    }

    // Close modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.replace('flex', 'hidden');
    }

    // Handle form submit
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const id = editId.value;
        const data = {
            tanggal: editTanggal.value,
            kategori: editJenis.value,
            keterangan: editKeterangan.value,
        };

        fetch(`/iseki_rifa/public/reporting/${id}`, {
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
        const numbers = document.querySelectorAll('#cuti-table tbody tr .number');
        numbers.forEach((cell, index) => {
            cell.textContent = index + 1;
        });
    }
</script>
@endsection