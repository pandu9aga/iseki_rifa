@php
    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Lembur Bulanan – {{ $bulanList[$bulan] }} {{ $tahun }}</title>
    <style>
        /* Reset & Base */
        body { font-family: "Times New Roman", Times, serif; font-size: 10pt; color: #000; background: #fff; margin: 0; padding: 0; }
        .page { width: 210mm; min-height: 297mm; padding: 10mm 10mm 10mm 10mm; page-break-after: always; }
        .page:last-child { page-break-after: avoid; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6mm; }
        .header img { height: 35px; }
        .header .logo-right { height: 45px; }
        .title-block { text-align: center; background: #ddd; border: 1px solid #000; padding: 4px 0; margin-bottom: 4mm; }
        .title-block p { font-size: 12pt; font-weight: bold; text-decoration: underline; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 4mm; gap: 10mm; margin-top: 2mm; }
        .info-box { border: 1.5px solid #000; padding: 3px 6px; font-size: 9pt; min-width: 120px; }
        .info-box table { border-collapse: collapse; }
        .info-box td { padding: 1px 3px; }
        .info-box td.sep { text-align: center; padding: 0 4px; }
        /* Data Table */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 2mm; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 3px 4px; text-align: center; vertical-align: middle; font-size: 8.5pt; }
        table.data-table th { background: #ececec; font-weight: bold; }
        table.data-table td.nama { text-align: left; }
        table.data-table td.pekerjaan { text-align: left; }
        .row-empty { height: 22px; }
        .total-row td { font-weight: bold; background: #f5f5f5; }
        /* Perhatian */
        .perhatian { margin-top: 4mm; font-size: 8pt; }
        .perhatian p { margin-bottom: 2px; }
        .perhatian .title { font-weight: bold; }
        /* Print */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 10mm 12mm; }
        }
        @page { size: A4 portrait; margin: 0; }
    </style>
</head>
<body>
@foreach($groupedData as $tanggal => $chunks)
    @foreach($chunks as $chunkIndex => $chunk)
    <div class="page">

        {{-- HEADER --}}
        <div class="header">
            <img src="{{ public_path('images/LOGO1.png') }}" alt="Logo">
            <img class="logo-right" src="{{ public_path('images/LOGO5.png') }}" alt="Logo ISEKI">
        </div>

        {{-- JUDUL --}}
        <div class="title-block">
            <p>時間外、祝日出勤申請書</p>
            <p>Surat Permohonan Kerja Lembur, Kerja pada Hari Libur</p>
        </div>

        {{-- INFO ROW --}}
        <div class="info-row">
            <div class="info-box">
                <table>
                    <tr>
                        <td>管理部署</td>
                        <td class="sep">:</td>
                        <td>総務、人事</td>
                    </tr>
                    <tr>
                        <td>Dept. Pengendali</td>
                        <td class="sep"></td>
                        <td>GA, HR</td>
                    </tr>
                    <tr>
                        <td>管理番号</td>
                        <td class="sep">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>No. Manajemen</td>
                        <td class="sep"></td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="info-box">
                <table>
                    <tr>
                        <td>申請日 / Tgl Permohonan</td>
                        <td class="sep">:</td>
                        <td><strong>{{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM Y') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:24px">No</th>
                    <th rowspan="2" style="min-width:90px">氏名<br><small>Nama</small></th>
                    <th rowspan="2" style="min-width:70px">部署<br><small>Dept.</small></th>
                    <th rowspan="2" style="min-width:65px">実施日<br><small>Hari Pelaksanaan</small></th>
                    <th colspan="2" style="min-width:90px">時間帯<br><small>Dari jam sampai</small></th>
                    <th rowspan="2" style="min-width:65px">業務、仕事内容<br><small>Isi Pekerjaan</small></th>
                    <th rowspan="2" style="width:30px">飯<br><small>Makan</small></th>
                    <th rowspan="2" style="min-width:70px">上司の承認<br><small>Persetujuan Atasan</small></th>
                </tr>
                <tr>
                    <th style="width:45px"><small>Jam</small></th>
                    <th style="width:35px"><small>Durasi</small></th>
                </tr>
            </thead>
            <tbody>
                @php $globalStart = $chunkIndex * 24; @endphp
                @foreach($chunk->values() as $i => $item)
                <tr>
                    <td>{{ $globalStart + $i + 1 }}</td>
                    <td class="nama">{{ $item->employee->nama ?? '-' }}</td>
                    <td>{{ $item->employee->division->nama ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_lembur)->format('d-m-Y') }}</td>
                    <td>{{ $item->waktu_lembur ?? '-' }}</td>
                    <td>{{ number_format((float)$item->durasi_lembur, 1) }}</td>
                    <td class="pekerjaan">{{ $item->keterangan_lembur ?? '-' }}</td>
                    <td>{{ $item->makan_lembur ?? '-' }}</td>
                    <td></td>
                </tr>
                @endforeach
                {{-- Baris kosong sampai minimal 24 baris --}}
                @for($e = $chunk->count(); $e < 24; $e++)
                <tr class="row-empty">
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
                @endfor
                {{-- TOTAL --}}
                <tr class="total-row">
                    <td colspan="5" style="text-align:right">TOTAL JAM</td>
                    <td>{{ number_format($chunk->sum(fn($i) => (float)$i->durasi_lembur), 1) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

        {{-- PERHATIAN --}}
        <div class="perhatian">
            <p class="title">Perhatian :</p>
            <p>1. 薄枠は申請者（従業員）が記入する。Yang di dalam kotak tipis adalah diisi oleh pemohon (karyawan).</p>
            <p>2. 太枠は上司が記入する。Yang di dalam kotak tebal adalah diisi oleh Atasan.</p>
            <p>3. 二重線枠は総務、人事の方で記入する。Yang di dalam kotak dengan 2 garis diisi oleh dept. GA HR.</p>
            <p>4. 時間外、祝日出勤3時間以上の場合は会社が飯を用意する義務がある為丸して下さい、3時間以内はXにして下さい。<br>
               &nbsp;&nbsp;&nbsp;Untuk kerja lembur atau hari libur masuk kerja selama dan atau lebih dari 3 jam, maka perusahaan mempunyai kewajiban menyediakan makan, untuk itu beri tanda O, jika kurang dari 3 jam beri tanda X.</p>
            <p>5. 本届けを上司に承認を得た後に総務、人事部の方へ提出する事。<br>
               &nbsp;&nbsp;&nbsp;Setelah mendapatkan persetujuan dari atasan, serahkan surat ini ke bagian GA, HRD.</p>
        </div>

    </div>
    @endforeach
@endforeach
</body>
</html>