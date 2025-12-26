@extends('layouts.app')

@section('content')
    <main>
        <section class="title-button d-flex flex-row justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Penilaian Tahunan Karyawan</h1>

            <div class="flex gap-2">
                <form action="{{ route('penilaian.export') }}" method="GET" class="inline">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="material-symbols-rounded">download</i>
                        Export Excel
                    </button>
                </form>

                <a href="{{ route('employees.read') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </section>

        <!-- Filter -->
        <form method="GET" class="mb-4 flex gap-3 flex-wrap bg-gray-50 p-3 rounded">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tahun</label>
                <select name="tahun" class="form-select mt-1" onchange="this.form.submit()">
                    @foreach ($tahunOptions as $opt)
                        <option value="{{ $opt }}" @selected($opt == $tahun)>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Divisi</label>
                <select name="division_id" class="form-select mt-1" onchange="this.form.submit()">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisions as $d)
                        <option value="{{ $d->id }}" @selected($divisionId == $d->id)>
                            {{ $d->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="form-select mt-1" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="Direct" @selected($status == 'Direct')>Direct</option>
                    <option value="Non Direct" @selected($status == 'Non Direct')>Non Direct</option>
                </select>
            </div>
        </form>

        <!-- Form Penilaian -->
        <form method="POST" action="{{ route('penilaian.store') }}" class="mt-2">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            <div class="table-scroll-wrapper border rounded overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">#</th>
                            <th class="px-3 py-2 text-left">Nama</th>
                            <th class="px-3 py-2 text-left">NIK</th>
                            <th class="px-3 py-2 text-left">Divisi</th>
                            <th class="px-3 py-2 text-left">Tim</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left w-28">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $index => $emp)
                            @php
                                $nilaiSekarang = $emp->nilaiTahunan->first()?->nilai;
                            @endphp
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 font-medium">{{ $emp->nama }}</td>
                                <td class="px-3 py-2">{{ $emp->nik ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $emp->division?->nama ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $emp->team ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $emp->status ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    <select name="nilai[{{ $emp->id }}]" class="form-select w-full text-center">
                                        <option value="" @selected(empty($nilaiSekarang))>—</option>
                                        @foreach (['AA', 'A', 'B', 'C', 'D', 'E'] as $grade)
                                            <option value="{{ $grade }}" @selected($nilaiSekarang == $grade)>
                                                {{ $grade }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500">
                                    Tidak ada data karyawan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('employees.read') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    Simpan Semua Nilai
                </button>
            </div>
        </form>
    </main>
@endsection
