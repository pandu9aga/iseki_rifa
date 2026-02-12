<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Employee;
use App\Models\Lembur;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanLemburController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan');
        $pekerjaan = $request->get('pekerjaan'); // Ambil filter pekerjaan

        // Validasi bulan
        if ($bulan !== null && (!is_numeric($bulan) || $bulan < 1 || $bulan > 12)) {
            $bulan = null;
        }

        // Validasi tahun
        if (!is_numeric($tahun) || $tahun < 2000 || $tahun > now()->year + 1) {
            $tahun = now()->year;
        }

        $employees = Employee::with('division', 'nilaiTahunan')
            ->where(function ($query) use ($tahun, $bulan) {
                if ($bulan) {
                    $query->whereNull('deleted_at')
                        ->orWhere('deleted_at', '>=', "$tahun-$bulan-01");
                } else {
                    $query->whereNull('deleted_at')
                        ->orWhereYear('deleted_at', '<=', $tahun);
                }
            })
            ->withSum(['lemburs as total_lembur' => function ($query) use ($tahun, $bulan, $pekerjaan) {
                if ($bulan) {
                    $endDate = now()->setDate($tahun, $bulan, 1)->endOfMonth()->toDateString();
                    $query->whereBetween('tanggal_lembur', ["$tahun-$bulan-01", $endDate]);
                } else {
                    $query->whereYear('tanggal_lembur', $tahun);
                }

                if ($pekerjaan) {
                    $query->where('keterangan_lembur', $pekerjaan);
                }
            }], 'durasi_lembur')
            ->orderBy('nik')
            ->get();

        // Hitung Total Durasi, Budget, dan Selisih (jika bulan dipilih)
        $totalDurasiBulanan = 0;
        $budgetBulanan = 0;
        $selisihBudget = 0;

        // Hitung Breakdown Jam per Kategori
        $breakdownKategori = [
            'Produksi' => 0,
            'Maintenance' => 0,
            'Kaizen' => 0,
            '5S' => 0,
            'Pekerjaan Leader/PIC Lembur' => 0
        ];

        // Query dasar untuk statistik
        $statsQuery = Lembur::query();
        if ($bulan) {
            $statsQuery->whereYear('tanggal_lembur', $tahun)
                ->whereMonth('tanggal_lembur', $bulan);
        } else {
            $statsQuery->whereYear('tanggal_lembur', $tahun);
        }

        // Hitung breakdown tanpa filter pekerjaan (agar user melihat proporsi utuh)
        // Atau jika user ingin breakdown berubah sesuai filter? 
        // Biasanya breakdown menunjukkan total dari data yang tampil.
        // Jika ada filter pekerjaan, breakdown akan 0 semua kecuali yang dipilih. 
        // Tapi user minta "misal dalam bulan itu total lembur dengan ketegori pekerjaan perodusi berapa jam",
        // jadi lebih baik breakdown selalu menampilkan semua kategori untuk periode tersebut.

        $rawBreakdown = (clone $statsQuery)
            ->selectRaw('keterangan_lembur, sum(durasi_lembur) as total_jam')
            ->groupBy('keterangan_lembur')
            ->pluck('total_jam', 'keterangan_lembur');

        foreach ($breakdownKategori as $key => $val) {
            if (isset($rawBreakdown[$key])) {
                $breakdownKategori[$key] = $rawBreakdown[$key];
            }
        }

        // Handle keterangan lama/manual yang mungkin masuk ke 'Lainnya' jika perlu, tapi requirement hanya 5 opsi ini.

        if ($bulan) {
            $bulanReferensi = sprintf('%d-%02d', $tahun, $bulan);

            // Jika ada filter pekerjaan, total durasi bulanan harus ikut terfilter
            if ($pekerjaan) {
                $totalDurasiBulanan = $statsQuery->where('keterangan_lembur', $pekerjaan)->sum('durasi_lembur');
            } else {
                $totalDurasiBulanan = $statsQuery->sum('durasi_lembur');
            }

            $budgetRecord = Budget::where('Tanggal_Budget', $bulanReferensi)->first();
            $budgetBulanan = $budgetRecord?->Jumlah_Budget ?? 0;

            $selisihBudget = $budgetBulanan - $totalDurasiBulanan;
        }

        // Buat daftar tahun
        $currentYear = now()->year;
        $tahunList = [];
        for ($y = $currentYear; $y >= 2020; $y--) {
            $tahunList[] = $y;
        }

        // Daftar bulan
        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return view('laporan.index', compact(
            'employees',
            'tahun',
            'bulan',
            'pekerjaan',
            'tahunList',
            'bulanList',
            'totalDurasiBulanan',
            'budgetBulanan',
            'selisihBudget',
            'breakdownKategori'
        ));
    }

    public function export(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan');
        $pekerjaan = $request->get('pekerjaan'); // Ambil filter pekerjaan

        // Validasi bulan
        if ($bulan !== null && (!is_numeric($bulan) || $bulan < 1 || $bulan > 12)) {
            $bulan = null;
        }

        // Validasi tahun
        if (!is_numeric($tahun) || $tahun < 2000 || $tahun > now()->year + 1) {
            $tahun = now()->year;
        }

        $query = Employee::with('division', 'nilaiTahunan')
            ->where(function ($q) use ($tahun, $bulan) {
                if ($bulan) {
                    $q->whereNull('deleted_at')
                        ->orWhere('deleted_at', '>=', "$tahun-$bulan-01");
                } else {
                    $q->whereNull('deleted_at')
                        ->orWhereYear('deleted_at', '<=', $tahun);
                }
            });

        // Tambahkan relasi lembur dengan filter tahun/bulan
        if ($bulan) {
            $endDate = now()->setDate($tahun, $bulan, 1)->endOfMonth()->toDateString();
            $query->withSum(['lemburs as total_lembur' => function ($q) use ($tahun, $bulan, $endDate, $pekerjaan) {
                $q->whereBetween('tanggal_lembur', ["$tahun-$bulan-01", $endDate]);
                if ($pekerjaan) {
                    $q->where('keterangan_lembur', $pekerjaan);
                }
            }], 'durasi_lembur');
        } else {
            $query->withSum(['lemburs as total_lembur' => function ($q) use ($tahun, $pekerjaan) {
                $q->whereYear('tanggal_lembur', $tahun);
                if ($pekerjaan) {
                    $q->where('keterangan_lembur', $pekerjaan);
                }
            }], 'durasi_lembur');
        }

        $employees = $query->orderBy('nik')->get();

        // --- Mulai generate Excel ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set default font
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(11);

        // Judul
        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        if ($bulan) {
            $judul = "LAPORAN LEMBUR {$bulanList[$bulan]} {$tahun}";
            $fileName = "Laporan_Lembur_{$bulanList[$bulan]}_{$tahun}.xlsx";
        } else {
            $judul = "LAPORAN LEMBUR TAHUN {$tahun}";
            $fileName = "Laporan_Lembur_Tahun_{$tahun}.xlsx";
        }

        // Set judul
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', $judul);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Spasi
        $sheet->getRowDimension(2)->setRowHeight(10);

        // Header tabel
        $headerRow = 3;
        $sheet->setCellValue("A{$headerRow}", 'No');
        $sheet->setCellValue("B{$headerRow}", 'Nama Karyawan');
        $sheet->setCellValue("C{$headerRow}", 'Divisi');
        $sheet->setCellValue("D{$headerRow}", 'Nilai');
        $sheet->setCellValue("E{$headerRow}", 'Total Jam Lembur');

        // Style header
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")
            ->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E1F2');
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Isi data
        $row = $headerRow + 1;
        $totalLembur = 0;

        foreach ($employees as $i => $emp) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $emp->nama);
            $sheet->setCellValue("C{$row}", $emp->division->nama ?? '-');

            // Ambil nilai akhir tahun
            $nilaiAkhir = $emp->nilaiTahunan
                ->firstWhere('tanggal_penilaian', 'like', $tahun . '-12-31')
                ?->nilai ?? '-';
            $sheet->setCellValue("D{$row}", $nilaiAkhir);

            $lemburValue = $emp->total_lembur ?? 0;
            $sheet->setCellValue("E{$row}", $lemburValue);
            $totalLembur += $lemburValue;

            // Style data
            $sheet->getStyle("A{$row}:E{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}:E{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Alternating row colors
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$row}:E{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2F2F2');
            }

            $row++;
        }

        // Total row
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->setCellValue("E{$row}", $totalLembur);

        $sheet->getStyle("A{$row}:E{$row}")
            ->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:E{$row}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFC000');
        $sheet->getStyle("A{$row}:E{$row}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle("A{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);

        // Create writer and output
        $writer = new Xlsx($spreadsheet);

        // Set proper headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Save to output
        $writer->save('php://output');
        exit;
    }
}
