@extends('layouts.app')

@section('content')
    <main>
        <div class="mb-4">
            <br>
            <h3 class="font-bold text-lg">Laporan Lembur Tahun {{ $tahun }}</h3>
        </div>

<section class="flex items-center mb-4">
    {{-- Kiri: Dropdown tahun + Export --}}
    <div class="flex gap-2">
        {{-- Form pilih tahun --}}
        <form action="{{ route('laporan.lembur.index') }}" method="GET" class="flex items-center gap-2">
            <select name="tahun" onchange="this.form.submit()" class="form-control">
                @foreach ($tahunList as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>

        {{-- Export sesuai tahun --}}
        <a href="{{ route('laporan.lembur.export', ['tahun' => $tahun]) }}" class="btn btn-primary">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
    </div>

    {{-- Kanan: Tombol Kembali --}}
    <a href="{{ route('lemburs.index') }}" class="btn btn-secondary ml-auto">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</section>

        <section class="container-table">
            <table class="table-auto w-full border border-gray-300 mt-4" id="laporan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>
                            Nama Karyawan<br>
                            <input type="text" class="filter" data-column="1" placeholder="Cari Nama">
                        </th>
                        <th>
                            Divisi<br>
                            <input type="text" class="filter" data-column="2" placeholder="Cari Divisi">
                        </th>
                        <th>Total Lembur (Jam)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $emp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $emp->nama }}</td>
                            <td>{{ $emp->division->nama ?? '-' }}</td>
                            <td>
                                @if (fmod($emp->total_lembur ?? 0, 1) == 0)
                                    {{ (int) $emp->total_lembur }}
                                @else
                                    {{ number_format($emp->total_lembur, 1, '.', '') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const table = document.getElementById('laporan-table');
            const filters = table.querySelectorAll('.filter');

            filters.forEach(input => {
                input.addEventListener('input', filterTable);
            });

            function filterTable() {
                const rows = table.tBodies[0].rows;
                let counter = 1;

                for (let row of rows) {
                    let show = true;

                    filters.forEach(filter => {
                        const colIndex = filter.dataset.column;
                        const filterValue = filter.value.toLowerCase();
                        const cell = row.cells[colIndex];

                        if (filterValue && cell) {
                            let cellText = cell.textContent.toLowerCase();
                            if (!cellText.includes(filterValue)) show = false;
                        }
                    });

                    if (show) {
                        row.style.display = '';
                        row.cells[0].textContent = counter++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        });
    </script>
@endsection
