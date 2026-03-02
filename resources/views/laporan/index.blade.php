@extends('layouts.app')

@section('content')
<main class="w-full">
    <div class="mb-4">
        <br>
        <h3 class="font-bold text-lg">
            Laporan Lembur
            @if(!empty($bulan) && isset($bulanList[$bulan]))
            {{ $bulanList[$bulan] }} {{ $tahun }}
            @else
            Tahun {{ $tahun }}
            @endif
        </h3>
    </div>

    {{-- Kontainer aksi: filter + export + kembali --}}
    <section class="flex flex-col sm:flex-row gap-3 mb-4 items-start sm:items-center">
        {{-- Kiri: Filter Tahun & Bulan --}}
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="tahun" class="form-select" onchange="this.form.submit()">
                @foreach ($tahunList as $opt)
                <option value="{{ $opt }}" @selected($opt==$tahun)>{{ $opt }}</option>
                @endforeach
            </select>

            <select name="bulan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Bulan --</option>
                @foreach ($bulanList as $num => $nama)
                <option value="{{ $num }}" @selected(!empty($bulan) && (int)$bulan===$num)>{{ $nama }}</option>
                @endforeach
            </select>

            <select name="pekerjaan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Pekerjaan --</option>
                <option value="Produksi" @selected($pekerjaan=='Produksi' )>Produksi</option>
                <option value="Maintenance" @selected($pekerjaan=='Maintenance' )>Maintenance</option>
                <option value="Kaizen" @selected($pekerjaan=='Kaizen' )>Kaizen</option>
                <option value="5S" @selected($pekerjaan=='5S' )>5S</option>
                <option value="Pekerjaan Leader/PIC Lembur" @selected($pekerjaan=='Pekerjaan Leader/PIC Lembur' )>Leader/PIC</option>
            </select>
        </form>

        {{-- Tengah/Kanan: Tombol Export & Kembali --}}
        <div class="flex flex-wrap gap-2 ml-auto">
            {{-- Export Tahunan --}}
            <a href="{{ route('laporan.lembur.export', ['tahun' => $tahun]) }}"
                class="btn btn-primary whitespace-nowrap">
                <i class="fas fa-file-excel"></i> Export Tahunan
            </a>

            {{-- Export Bulanan (jika bulan dipilih) --}}
            @if(!empty($bulan) && isset($bulanList[$bulan]))
            <a href="{{ route('laporan.lembur.export', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
                class="btn btn-primary">
                <i class="fas fa-file-excel"></i> Export {{ $bulanList[$bulan] }}
            </a>
            @endif

            {{-- Kembali --}}
            <a href="{{ route('lemburs.index') }}"
                class="btn btn-secondary whitespace-nowrap">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>

    {{-- Ringkasan Jam per Kategori — sama persis seperti index lembur --}}
    @if(isset($breakdownKategori))
    <section class="flex flex-wrap items-center" style="gap: 1.5rem; margin-bottom: 0.5rem;">
        <div class="text-sm font-semibold text-gray-600">Jam per Kategori:</div>
        <div class="bg-purple-100 border border-purple-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-purple-700 font-semibold">Produksi: {{ number_format($breakdownKategori['Produksi'] ?? 0, 1) }} jam</span>
        </div>
        <div class="bg-orange-100 border border-orange-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-orange-700 font-semibold">Maintenance: {{ number_format($breakdownKategori['Maintenance'] ?? 0, 1) }} jam</span>
        </div>
        <div class="bg-teal-100 border border-teal-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-teal-700 font-semibold">Kaizen: {{ number_format($breakdownKategori['Kaizen'] ?? 0, 1) }} jam</span>
        </div>
        <div class="bg-yellow-100 border border-yellow-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-yellow-700 font-semibold">5S: {{ number_format($breakdownKategori['5S'] ?? 0, 1) }} jam</span>
        </div>
        <div class="bg-indigo-100 border border-indigo-300 px-4 py-2 rounded shadow text-sm">
            <span class="text-indigo-700 font-semibold">Leader/PIC: {{ number_format($breakdownKategori['Pekerjaan Leader/PIC Lembur'] ?? 0, 1) }} jam</span>
        </div>
    </section>
    @endif

    {{-- Kotak Informasi Budget & Durasi (hanya tampil jika bulan dipilih) --}}
    @if(!empty($bulan) && isset($bulanList[$bulan]))
    <section class="flex flex-wrap items-center" style="gap: 2rem; margin-bottom: 1rem; margin-top: 0.75rem;">
        <!-- Budget Bulanan -->
        <div class="bg-green-100 border border-green-300 px-6 py-3 rounded shadow text-sm">
            <div class="text-gray-600 text-xs" style="margin-bottom: 0.5rem;">Budget ({{ $bulanList[$bulan] }} {{ $tahun }})</div>
            <div class="text-green-700 text-base font-semibold">
                {{ number_format($budgetBulanan, 1) }} jam
            </div>
        </div>
        <!-- Total Durasi Bulanan -->
        <div class="bg-blue-100 border border-blue-300 px-6 py-3 rounded shadow text-sm">
            <div class="text-gray-600 text-xs" style="margin-bottom: 0.5rem;">Total Durasi ({{ $bulanList[$bulan] }} {{ $tahun }})</div>
            <div class="text-blue-700 text-base font-semibold">
                {{ number_format($totalDurasiBulanan, 1) }} jam
            </div>
        </div>
        <!-- Selisih (Budget - Durasi) -->
        <div class="border px-6 py-3 rounded shadow text-sm
            {{ $selisihBudget >= 0 ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
            <div class="text-gray-600 text-xs" style="margin-bottom: 0.5rem;">Selisih</div>
            <div class="text-base font-semibold {{ $selisihBudget >= 0 ? 'text-green-700' : 'text-red-700' }}">
                {{ $selisihBudget >= 0 ? '+' : '' }}{{ number_format($selisihBudget, 1) }} jam
            </div>
        </div>
    </section>
    @endif

    {{-- Tabel dengan Nested Scroll --}}
    <div class="w-full bg-white rounded-lg shadow">
        <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
            <table class="w-full border-collapse border border-gray-300" id="laporan-table">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 border border-gray-300 text-left bg-gray-100">No</th>
                        <th class="px-4 py-3 border border-gray-300 text-left bg-gray-100">
                            <div class="mb-1 font-semibold">Nama Karyawan</div>
                            <input type="text"
                                class="filter w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                data-column="1"
                                placeholder="Cari Nama">
                        </th>
                        <th class="px-4 py-3 border border-gray-300 text-center bg-gray-100">
                            <div class="mb-1 font-semibold">Divisi</div>
                            <input type="text"
                                class="filter w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                data-column="2"
                                placeholder="Cari Divisi">
                        </th>
                        <th class="px-4 py-3 border border-gray-300 text-center bg-gray-100">
                            <div class="mb-1 font-semibold">Nilai</div>
                            <input type="text"
                                class="filter w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                data-column="3"
                                placeholder="Cari Nilai">
                        </th>
                        <th class="px-4 py-3 border border-gray-300 text-center font-semibold bg-gray-100">Total Lembur (Jam)</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($employees as $emp)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 border border-gray-300 text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 border border-gray-300">{{ $emp->nama }}</td>
                        <td class="px-4 py-3 border border-gray-300 text-center">{{ $emp->division->nama ?? '-' }}</td>
                        <td class="px-4 py-3 border border-gray-300 text-center">
                            {{ $emp->nilaiTahunan->firstWhere('tanggal_penilaian', 'like', $tahun . '-12-31')?->nilai ?? '-' }}
                        </td>
                        <td class="px-4 py-3 border border-gray-300 text-center font-semibold">
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
        </div>
    </div>

    <br><br>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('laporan-table');
        if (!table) return;

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
                    const colIndex = parseInt(filter.dataset.column);
                    const filterValue = filter.value.toLowerCase().trim();
                    const cell = row.cells[colIndex];
                    if (filterValue && cell) {
                        const cellText = cell.textContent.toLowerCase().trim();
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