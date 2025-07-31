<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        @page {
            margin: 10px 10px 10px 10px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            word-wrap: break-word;
            font-size: 8px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
    </style>
</head>
<body>
    {{ $date }}
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">Nama</th>
                <th style="width: 10%;">Jenis Izin</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 20%;">Keterangan</th>
                <th style="width: 10%;">Persetujuan</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 12%;">Divisi</th>
                <th style="width: 12%;">Tim</th>
            </tr>
        </thead>

        <tbody>
            @forelse($absensis as $index => $absen)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $absen->employee->nama ?? '-' }}</td>
                <td>{{ $absen->kategori_label }}</td>
                <td>{{ $absen->tanggal->format('d/m/Y') }}</td>
                <td>{!! $absen->keterangan ? nl2br(e($absen->keterangan)) : '-' !!}</td>
                @php
                    if (is_null($absen->is_approved)) {
                        $status = 'Menunggu Persetujuan';
                    } elseif ($absen->is_approved) {
                        $status = 'Disetujui';
                    } else {
                        $status = 'Ditolak';
                    }
                @endphp
                <td><span>{{ $status }}</span></td>
                <td>{{ $absen->employee->status ?? 'Contract' }}</td>
                <td>{{ $absen->employee->division->nama ?? '-' }}</td>
                <td>{{ $absen->employee->team ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; font-style: italic;">---- Nihil ----</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                $size = 8;
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $x = 520;
                $y = 820;
                $pdf->text($x, $y, $pageText, $font, $size);
            ');
        }
    </script>
</body>
</html>
