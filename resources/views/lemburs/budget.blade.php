@extends('layouts.app')

@section('content')
<main>
    <div class="mb-4">
        <br>
        <h3 class="font-bold text-lg">Budget Lembur (per Bulan)</h3>
        <p class="text-sm text-gray-600">Satuan: jam | Bisa dikosongkan</p>
    </div>

    {{-- Filter Tahun --}}
    <form method="GET" class="mb-6 bg-gray-50 p-4 rounded">
        <div class="flex items-end gap-3 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tahun</label>
                <select name="tahun" class="form-select mt-1" onchange="this.form.submit()">
                    @for ($y = now()->year - 2; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" @selected($tahun==$y)>{{ $y }}</option>
                        @endfor
                </select>
            </div>
        </div>
    </form>
    {{-- Kembali --}}
    <a href="{{ route('lemburs.index') }}"
        class="btn btn-secondary whitespace-nowrap">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    {{-- Form Bulk Input --}}
    <form method="POST" action="{{ route('budget.lembur.bulk-update') }}">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <div class="overflow-x-auto">
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Bulan</th>
                        <th class="px-4 py-2">Jumlah Budget (Jam)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $bulanList = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                    @endphp

                    @foreach($bulanList as $num => $nama)
                    @php
                    $key = "$tahun-$num";
                    $value = $budgetData[$key] ?? null;
                    @endphp
                    <tr>
                        <td class="border px-4 py-2">{{ $nama }} {{ $tahun }}</td>
                        <td class="border px-4 py-2">
                            <input
                                type="number"
                                name="budgets[{{ $key }}]"
                                value="{{ old("budgets.$key", $value) }}"
                                min="0"
                                class="form-input w-full"
                                placeholder="Boleh kosong">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Semua Budget
            </button>
        </div>
    </form>

    @if(session('success'))
    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif
</main>
@endsection