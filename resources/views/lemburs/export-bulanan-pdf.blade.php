<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Lembur Bulanan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 10mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #000;
        }

        .page {
            page-break-after: always;
            position: relative;
            width: 100%;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        /* ===== HEADER LOGOS ===== */
        .header-logos {
            width: 100%;
            margin-bottom: 5px;
        }

        .header-logos td {
            vertical-align: middle;
        }

        .logo-left {
            width: 60px;
            height: auto;
        }

        .logo-right {
            width: 90px;
            height: auto;
        }

        /* ===== TITLE ===== */
        .title-section {
            width: 100%;
            text-align: center;
            background-color: #DDDDDD;
            padding: 6px 0;
            margin-bottom: 8px;
            border: 1px solid #999;
        }

        .title-section .title-jp {
            font-size: 12pt;
            text-decoration: underline;
            font-weight: bold;
        }

        .title-section .title-id {
            font-size: 10pt;
            text-decoration: underline;
        }

        /* ===== INFO SECTION ===== */
        .info-section {
            width: 100%;
            margin-bottom: 8px;
        }

        .info-left {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 8pt;
            vertical-align: top;
        }

        .info-right {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 8pt;
            vertical-align: top;
        }

        .info-colon {
            width: 10px;
            text-align: center;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 2px;
        }

        .info-value {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
            padding: 3px 6px;
        }

        /* ===== DATA TABLE ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
            table-layout: fixed;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            word-wrap: break-word;
            overflow: hidden;
        }

        .data-table thead th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .data-table .col-no { width: 4%; }
        .data-table .col-nama { width: 20%; }
        .data-table .col-dept { width: 10%; }
        .data-table .col-tanggal { width: 11%; }
        .data-table .col-waktu { width: 14%; }
        .data-table .col-durasi { width: 5%; }
        .data-table .col-pekerjaan { width: 14%; }
        .data-table .col-makan { width: 5%; }
        .data-table .col-approval { width: 17%; }

        .data-table td.nama {
            text-align: left;
            padding-left: 4px;
        }

        .data-table .empty-row td {
            height: 22px;
        }

        /* ===== TOTAL ROW ===== */
        .total-row td {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        /* ===== FOOTER NOTES ===== */
        .footer-notes {
            width: 100%;
            font-size: 7pt;
            margin-top: 4px;
            border-collapse: collapse;
        }

        .footer-notes td {
            padding: 1px 3px;
            vertical-align: top;
        }

        .footer-notes .note-title {
            font-weight: bold;
            font-size: 7.5pt;
            padding-bottom: 2px;
        }

        .footer-notes .note-no {
            width: 12px;
            text-align: center;
        }

        /* ===== PAGE INFO ===== */
        .page-info {
            text-align: right;
            font-size: 7pt;
            color: #666;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    @foreach ($pages as $pageData)
    <div class="page">
        {{-- Page Info --}}
        <div class="page-info">
            Halaman {{ $loop->iteration }} dari {{ count($pages) }} &mdash; {{ $bulanNama }} {{ $tahun }}
        </div>

        {{-- Header Logos --}}
        <table class="header-logos">
            <tr>
                <td style="width: 70px;">
                    <img src="{{ $logo1Path }}" class="logo-left" alt="Logo1">
                </td>
                <td style="text-align: center;">&nbsp;</td>
                <td style="width: 100px; text-align: right;">
                    <img src="{{ $logo2Path }}" class="logo-right" alt="Logo2">
                </td>
            </tr>
        </table>

        {{-- Title --}}
        <div class="title-section">
            <div class="title-jp">時間外、祝日出勤申請書</div>
            <div class="title-id">Surat Permohonan Kerja Lembur, Kerja pada Hari Libur</div>
        </div>

        {{-- Info Section --}}
        <table class="info-section">
            <tr>
                {{-- Left info --}}
                <td style="width: 45%; vertical-align: top; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="info-left" style="width: 45%; border-right: none;">
                                管理部署<br>Dept. Pengendali<br>管理番号<br>No. Manajemen
                            </td>
                            <td class="info-colon" style="border-left: 1px solid #000; border-right: 1px solid #000;">:</td>
                            <td class="info-value" style="width: 45%;">
                                総務、人事<br>GA, HR<br>&nbsp;<br>&nbsp;
                            </td>
                        </tr>
                    </table>
                </td>

                <td style="width: 10%;">&nbsp;</td>

                {{-- Right info --}}
                <td style="width: 45%; vertical-align: top; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="info-right" style="width: 50%; border-right: none;">
                                申請日<br>Tgl Permohonan
                            </td>
                            <td class="info-colon" style="border-left: 1px solid #000; border-right: 1px solid #000;">:</td>
                            <td class="info-value">
                                {{ $pageData['tanggal_formatted'] }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Data Table --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-no" rowspan="2">No</th>
                    <th class="col-nama">氏名</th>
                    <th class="col-dept">部署</th>
                    <th class="col-tanggal">実施日</th>
                    <th class="col-waktu" colspan="2">時間帯</th>
                    <th class="col-pekerjaan">業務、仕事内容</th>
                    <th class="col-makan">飯</th>
                    <th class="col-approval">上司の承認</th>
                </tr>
                <tr>
                    <th class="col-nama">Nama</th>
                    <th class="col-dept">Dept.</th>
                    <th class="col-tanggal">Hari Pelaksanaan</th>
                    <th style="width: 10%;">Dari jam sampai</th>
                    <th style="width: 4%;">Jam</th>
                    <th class="col-pekerjaan">Isi Pekerjaan</th>
                    <th class="col-makan">Makan</th>
                    <th class="col-approval">Persetujuan Atasan</th>
                </tr>
            </thead>
            <tbody>
                @php $itemIndex = $pageData['start_index']; @endphp
                @foreach ($pageData['items'] as $item)
                <tr>
                    <td>{{ $itemIndex }}</td>
                    <td class="nama">{{ $item->employee->nama ?? '' }}</td>
                    <td>{{ $item->employee->division->nama ?? '' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_lembur)->format('d-m-Y') }}</td>
                    <td>{{ $item->waktu_lembur ?? '' }}</td>
                    <td>{{ $item->durasi_lembur }}</td>
                    <td>{{ $item->keterangan_lembur ?? '' }}</td>
                    <td>{{ $item->makan_lembur ?? '' }}</td>
                    <td></td>
                </tr>
                @php $itemIndex++; @endphp
                @endforeach

                {{-- Empty rows to fill up to 24 --}}
                @for ($e = count($pageData['items']); $e < 24; $e++)
                <tr class="empty-row">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" style="text-align: center;">TOTAL JAM</td>
                    <td colspan="2">{{ $pageData['total_jam'] }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        {{-- Footer Notes --}}
        <table class="footer-notes">
            <tr>
                <td colspan="2" class="note-title">Perhatian</td>
            </tr>
            <tr>
                <td class="note-no">1</td>
                <td>薄枠は申請者（従業員）が記入する。Yang di dalam kotak tipis adalah diisi oleh pemohon (karyawan).</td>
            </tr>
            <tr>
                <td class="note-no">2</td>
                <td>太枠は上司が記入する。Yang di dalam kotak tebal adalah diisi oleh Atasan.</td>
            </tr>
            <tr>
                <td class="note-no">3</td>
                <td>二重線枠は総務、人事の方で記入する。Yang di dalam kotak dengan 2 garis diisi oleh dept. GA HR.</td>
            </tr>
            <tr>
                <td class="note-no">4</td>
                <td>
                    時間外、祝日出勤3時間以上の場合は会社が飯を用意する義務がある為丸して下さい、3時間以内はXにして下さい。<br>
                    Untuk kerja lembur atau hari libur masuk kerja selama dan atau lebih dari 3 jam, maka perusahaan mempunyai kewajiban menyediakan makan, untuk itu beri tanda O, jika kurang dari 3 jam beri tanda X.
                </td>
            </tr>
            <tr>
                <td class="note-no">5</td>
                <td>
                    本届けを上司に承認を得た後に総務、人事部の方へ提出する事。<br>
                    Setelah mendapatkan persetujuan dari atasan, serahkan surat ini ke bagian GA, HRD.
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
