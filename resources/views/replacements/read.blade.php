@extends('layouts.app')

@section('content')
<main>

    <section class="title-button d-flex flex-row">
        <h1 class="font-bold">List Perizinan</h1>
    </section>

    <p id="jumlah-data" class="flex justify-end mb-2 text-sm">
        Jumlah Data: {{ count($absensis) }}
    </p>

    @csrf
    <section class="container-table">
        <table id="pengganti-table">
            <thead>
                <tr>
                    <th rowspan="2" class="sticky-col-left">No</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th rowspan="2">Pengganti</th>
                    <th rowspan="2" class="sticky-col-right">Aksi</th>
                </tr>
                <tr>
                    <th><input class="filter" id="filter-nama" type="text" placeholder="Cari Nama" /></th>
                    {{-- <th><input class="filter" id="filter-tanggal" type="month" value="{{ request('bulan', now()->format('Y-m')) }}" /></th> --}}
                    <th><input class="filter" id="filter-tanggal" type="date" value="{{ request('tanggal', now()->format('Y-m-d')) }}" /></th>
                    {{-- <th><input type="text" class="filter" id="filter-pengganti" placeholder="Cari Pengganti" /></th> --}}
                </tr>
            </thead>

            <tbody>
                @foreach($absensis as $index => $absen)
                <tr data-id="{{ $absen->id }}">
                    {{-- <td class="sticky-col-left number">{{ $index + 1 }}</td> --}}
                    <td class="sticky-col-left number">{{ $loop->iteration }}</td>
                    <td>{{ $absen->employee->nama ?? '-' }}</td>
                    <td>{{ $absen->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <div style="display: inline-flex; align-items: center; gap: 5px;">
                            <span>{{ \App\Models\Replacement::where('absensi_id', $absen->id)->count() }} ; </span>
                            <button type="button" class="btn btn-icon btn-view-replacement" data-id="{{ $absen->id }}">
                                <i class="material-symbols-rounded delete-row btn-primary">
                                    visibility
                                </i>
                            </button>
                        </div>
                    </td>
                    <td class="sticky-col-right">
                        <div class="btn-group">
                            <a href="{{ route('replacements.create', $absen->id) }}" class="btn btn-icon">
                                <button type="button" class="btn btn-icon edit-row">
                                    <i class="material-symbols-rounded delete-row btn-primary">
                                        edit_square
                                    </i>
                                </button>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach

                <tr id="no-data-row" class="hidden text-center">
                    <td colspan="5" class="text-gray-500 py-4">Data tidak ditemukan</td>
                </tr>
            </tbody>
        </table>
    </section>
</main>
@include('components.popupReplacement')

<script>
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
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data</td></tr>`;
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