<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Employee;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\PageMargins;
use Carbon\Carbon;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $tahunOptions = range(2025, now()->year + 1);

        // === TENTUKAN BULAN REFERENSI DEFAULT ===
        $bulanReferensi = now()->format('Y-m');

        // === AMBIL SEMUA DATA BUDGET UNTUK JAVASCRIPT ===
        $allBudgets = Budget::all();
        $budgetData = [];
        foreach ($allBudgets as $budget) {
            $budgetData[$budget->Tanggal_Budget] = $budget->Jumlah_Budget;
        }

        // === HITUNG TOTAL DURASI BULAN INI (DEFAULT) ===
        $startOfMonth = Carbon::parse($bulanReferensi)->startOfMonth();
        $endOfMonth = Carbon::parse($bulanReferensi)->endOfMonth();

        $totalDurasiBulanIni = Lembur::whereBetween('tanggal_lembur', [$startOfMonth, $endOfMonth])
            ->sum('durasi_lembur');

        // === AMBIL BUDGET BULAN INI ===
        $budgetRecord = Budget::where('Tanggal_Budget', $bulanReferensi)->first();
        $budgetValue = $budgetRecord?->Jumlah_Budget ?? 0;
        $selisih = $budgetValue - $totalDurasiBulanIni;

        // === QUERY UNTUK TABEL ===
        $employees = Employee::with('division', 'nilaiTahunan')->get();

        $query = Lembur::with([
            'employee',
            'employee.division',
            'employee.nilaiTahunan' => fn($q) => $q->whereYear('tanggal_penilaian', $tahun)
        ])->whereHas('employee');

        // Filter tampilan tabel (opsional, untuk URL params)
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_lembur', $request->tanggal);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal_lembur', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_lembur', '<=', $request->sampai);
        }

        $lemburs = $query->orderBy('tanggal_lembur', 'asc')->get();

        return view('lemburs.index', compact(
            'lemburs',
            'employees',
            'tahun',
            'tahunOptions',
            'totalDurasiBulanIni',
            'budgetValue',
            'selisih',
            'bulanReferensi',
            'budgetData' // ← Kirim semua data budget ke view
        ));
    }

    // ... sisanya tetap sama seperti sebelumnya ...

    public function create()
    {
        $employees = Employee::with('division')->whereNull('deleted_at')->get();
        return view('lemburs.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id.*' => 'required|exists:employees,id',
            'tanggal_lembur.*' => 'required|date',
            'jam_mulai.*' => 'nullable|date_format:H:i',
            'jam_selesai.*' => 'nullable|date_format:H:i',
            'durasi_lembur.*' => 'nullable|numeric',
            'keterangan_lembur.*' => 'nullable|string',
            'makan_lembur.*' => 'nullable|string',
        ]);

        foreach ($request->employee_id as $index => $emp_id) {
            $tanggal     = $request->tanggal_lembur[$index];
            $jamMulai    = $request->jam_mulai[$index] ?? null;
            $jamSelesai  = $request->jam_selesai[$index] ?? null;

            if (!$jamMulai || !$jamSelesai) {
                $this->insertLembur($emp_id, $index, $request);
                continue;
            }

            $newStart = Carbon::createFromFormat('H:i', $jamMulai);
            $newEnd   = Carbon::createFromFormat('H:i', $jamSelesai);

            $existingLembur = Lembur::where('employee_id', $emp_id)
                ->whereDate('tanggal_lembur', $tanggal)
                ->get();

            $isBentrok = false;

            foreach ($existingLembur as $lembur) {
                if (!$lembur->waktu_lembur) continue;

                [$oldStart, $oldEnd] = array_map('trim', explode('-', $lembur->waktu_lembur));
                $oldStart = Carbon::createFromFormat('H:i', $oldStart);
                $oldEnd   = Carbon::createFromFormat('H:i', $oldEnd);

                if ($newStart < $oldEnd && $newEnd > $oldStart) {
                    $isBentrok = true;
                    break;
                }
            }

            if ($isBentrok) {
                continue;
            }

            $this->insertLembur($emp_id, $index, $request);
        }

        return redirect()
            ->route('lemburs.index')
            ->with('success', 'Data lembur berhasil disimpan (data bentrok otomatis dilewati)');
    }

    private function insertLembur($emp_id, $index, Request $request)
    {
        $employee = Employee::with('division')->findOrFail($emp_id);

        Lembur::create([
            'employee_id'       => $emp_id,
            'division'          => $employee->division->nama ?? '-',
            'tanggal_lembur'    => $request->tanggal_lembur[$index] ?? null,
            'waktu_lembur'      => ($request->jam_mulai[$index] ?? '') . ' - ' . ($request->jam_selesai[$index] ?? ''),
            'durasi_lembur'     => $request->durasi_lembur[$index] ?? null,
            'keterangan_lembur' => $request->keterangan_lembur[$index] ?? null,
            'makan_lembur'      => $request->makan_lembur[$index] ?? null,
            'approval_lembur'   => null,
        ]);
    }

    public function edit($id)
    {
        $lembur = Lembur::findOrFail($id);
        $employees = Employee::with('division')->get();

        if ($lembur->waktu_lembur) {
            [$jam_mulai, $jam_selesai] = explode(' - ', $lembur->waktu_lembur);
        } else {
            $jam_mulai = '';
            $jam_selesai = '';
        }

        return view('components.popupEditLembur', compact('lembur', 'employees', 'jam_mulai', 'jam_selesai'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'durasi_lembur' => 'nullable|numeric',
            'keterangan_lembur' => 'nullable|string',
            'makan_lembur' => 'nullable|string',
        ]);

        $lembur = Lembur::findOrFail($id);
        $employee = Employee::with('division')->findOrFail($request->employee_id);

        $lembur->update([
            'tanggal_lembur' => $request->tanggal_lembur,
            'waktu_lembur' => $request->jam_mulai . ' - ' . $request->jam_selesai,
            'durasi_lembur' => $request->durasi_lembur !== null ? (float) $request->durasi_lembur : null,
            'keterangan_lembur' => $request->keterangan_lembur,
            'makan_lembur' => $request->makan_lembur,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data lembur berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data lembur berhasil dihapus'
        ]);
    }

    public function approve(Request $request, $id)
    {
        $lembur = Lembur::findOrFail($id);
        $approval = $request->approval;

        if ($approval === '1') {
            $lembur->approval_lembur = true;
        } elseif ($approval === '0') {
            $lembur->approval_lembur = false;
        } else {
            $lembur->approval_lembur = null;
        }
        $lembur->save();

        if (is_null($lembur->approval_lembur)) {
            $status_label = 'Menunggu Persetujuan';
            $status_class = 'bg-yellow';
            $button_html = '
            <div class="flex flex-col btn-group">
                <button type="button" data-value="1" class="approve-btn btn bg-success text-sm rounded">Setujui</button>
                <button type="button" data-value="0" class="approve-btn btn bg-red text-sm rounded">Tolak</button>
            </div>
        ';
        } elseif ($lembur->approval_lembur) {
            $status_label = 'Disetujui';
            $status_class = 'bg-success';
            $button_html = '<button type="button" data-value="null" class="approve-btn btn bg-yellow text-sm rounded">Batalkan</button>';
        } else {
            $status_label = 'Ditolak';
            $status_class = 'bg-red';
            $button_html = '<button type="button" data-value="null" class="approve-btn btn bg-yellow text-sm rounded">Batalkan</button>';
        }

        return response()->json([
            'status_label' => $status_label,
            'status_class' => $status_class,
            'button_html' => $button_html,
        ]);
    }

    public function exportLembur(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $dari    = $request->get('dari');
        $sampai  = $request->get('sampai');

        $query = Lembur::with(['employee', 'employee.division'])->whereHas('employee');

        if ($tanggal) {
            $query->whereDate('tanggal_lembur', $tanggal);
        }
        if ($dari) {
            $query->whereDate('tanggal_lembur', '>=', $dari);
        }
        if ($sampai) {
            $query->whereDate('tanggal_lembur', '<=', $sampai);
        }

        // Urutkan berdasarkan id untuk memastikan urutan tetap
        $lemburs = $query->orderBy('id_lembur')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Footlight MT Light')
            ->setSize(11);

        // Chunk data per 24 baris
        $chunks = $lemburs->chunk(24);

        $sheetIndex = 0;
        $globalIndex = 1; // Inisialisasi indeks global

        foreach ($chunks as $chunk) {
            if ($sheetIndex > 0) {
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Sheet ' . ($sheetIndex + 1));
                $spreadsheet->addSheet($sheet);
            }

            $sheet->setTitle('Sheet ' . ($sheetIndex + 1));

            // Reset default row height agar tidak terbawa dari sheet sebelumnya
            $sheet->getDefaultRowDimension()->setRowHeight(15);

            // ========== LOGO ==========
            $drawing1 = new Drawing();
            $drawing1->setName('Logo1');
            $drawing1->setPath(public_path('images/LOGO1.png'));
            $drawing1->setCoordinates('A1');
            $drawing1->setHeight(35);
            $drawing1->setWorksheet($sheet);

            $drawing2 = new Drawing();
            $drawing2->setName('Logo2');
            $drawing2->setPath(public_path('images/LOGO5.png'));
            $drawing2->setCoordinates('K1');
            $drawing2->setHeight(55);
            $drawing2->setOffsetX(-85);
            $drawing2->setWorksheet($sheet);

            $sheet->mergeCells('H1:K1');
            $sheet->getRowDimension(1)->setRowHeight(20);

            // ========== JUDUL ==========
            $sheet->mergeCells('A4:K4');
            $sheet->mergeCells('A5:K5');
            $sheet->setCellValue('A4', '時間外、祝日出勤申請書');
            $sheet->setCellValue('A5', 'Surat Permohonan Kerja Lembur, Kerja pada Hari Libur');

            $sheet->getStyle('A4:K5')->getFont()->setSize(14)->setUnderline(true);
            $sheet->getStyle('A4:K5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A4:K5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');

            // ========== BAGIAN DEPT & NOMOR ==========
            $sheet->setCellValue('B7', '管理部署');
            $sheet->setCellValue('B8', 'Dept. Pengendali');
            $sheet->setCellValue('B9', '管理番号');
            $sheet->setCellValue('B10', 'No. Manajemen');

            $sheet->mergeCells('C7:C10');
            $sheet->setCellValue('C7', ':');
            $sheet->getStyle('C7')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getColumnDimension('C')->setWidth(2);

            $sheet->setCellValue('D7', '総務、人事');
            $sheet->setCellValue('D8', 'GA, HR');
            $sheet->setCellValue('D9', '');
            $sheet->setCellValue('D10', '');
            $sheet->getStyle('D7:D10')->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('B7:D10')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('C7:C10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // ========== TANGGAL PERMOHONAN ==========
            $sheet->mergeCells('H7:I7')->setCellValue('H7', '申請日');
            $sheet->mergeCells('H8:I8')->setCellValue('H8', 'Tgl Permohonan');

            $sheet->mergeCells('J7:J8')->setCellValue('J7', ':');
            $sheet->getStyle('J7')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getColumnDimension('J')->setWidth(3);

            if ($tanggal) {
                $tglPermohonan = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
            } elseif ($dari && $sampai) {
                $tglPermohonan = \Carbon\Carbon::parse($dari)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($sampai)->format('d-m-Y');
            } elseif ($dari) {
                $tglPermohonan = 'Mulai ' . \Carbon\Carbon::parse($dari)->format('d-m-Y');
            } elseif ($sampai) {
                $tglPermohonan = 'Sampai ' . \Carbon\Carbon::parse($sampai)->format('d-m-Y');
            } else {
                $tglPermohonan = \Carbon\Carbon::now()->format('d-m-Y');
            }

            $sheet->setCellValue('K8', $tglPermohonan);

            $sheet->getStyle('H7:K8')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('J7:J8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // ========== HEADER TABEL ==========
            $sheet->mergeCells('A12:A13')->setCellValue('A12', 'No');
            $sheet->setCellValue('B12', '氏名');
            $sheet->setCellValue('B13', 'Nama');
            $sheet->mergeCells('C12:D12')->setCellValue('C12', '部署');
            $sheet->mergeCells('C13:D13')->setCellValue('C13', 'Dept.');
            $sheet->setCellValue('E12', '実施日');
            $sheet->setCellValue('E13', 'Hari Pelaksanaan');
            $sheet->mergeCells('F12:G12')->setCellValue('F12', '時間帯');
            $sheet->mergeCells('F13:G13')->setCellValue('F13', 'Dari jam sampai');
            $sheet->setCellValue('H12', '業務、仕事内容');
            $sheet->setCellValue('H13', 'Isi Pekerjaan');
            $sheet->setCellValue('I12', '飯');
            $sheet->setCellValue('I13', 'Makan');
            $sheet->mergeCells('J12:K12')->setCellValue('J12', '上司の承認');
            $sheet->mergeCells('J13:K13')->setCellValue('J13', 'Persetujuan Atasan');

            $sheet->getStyle('A12:K13')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText(true);

            $sheet->getRowDimension(12)->setRowHeight(15);
            $sheet->getRowDimension(13)->setRowHeight(30);

            $sheet->getColumnDimension('A')->setWidth(4);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(1);
            $sheet->getColumnDimension('D')->setWidth(11);
            $sheet->getColumnDimension('E')->setWidth(13);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(5);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(6);
            $sheet->getColumnDimension('J')->setWidth(1);
            $sheet->getColumnDimension('K')->setWidth(10);

            $sheet->getStyle('A12:K13')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // ========== DATA ==========
            $startRow = 14;
            $endRow   = max($startRow + $chunk->count() - 1, 37); // minimal sampai baris 37

            foreach ($chunk->values() as $i => $item) {
                $row = $startRow + $i;

                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("E{$row}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("H{$row}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("J{$row}:K{$row}")->getAlignment()->setWrapText(true);

                $sheet->setCellValue("A{$row}", $globalIndex); // Gunakan indeks global
                $sheet->setCellValue("B{$row}", $item->employee->nama ?? '');
                $sheet->setCellValue("C{$row}", $item->employee->division->nama ?? '');
                $sheet->setCellValue("E{$row}", \Carbon\Carbon::parse($item->tanggal_lembur)->format('d-m-Y'));
                $sheet->setCellValue("F{$row}", $item->waktu_lembur ?? '');
                $sheet->setCellValueExplicit("G{$row}", (float) $item->durasi_lembur, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $sheet->setCellValue("H{$row}", $item->keterangan_lembur ?? '');
                $sheet->setCellValue("I{$row}", $item->makan_lembur ?? '');
                $sheet->setCellValue("J{$row}", '');

                $globalIndex++; // Tambahkan indeks global setiap kali data ditambahkan
            }

            // ========== ATUR LEBAR ROW DATA (14-37) ==========
            for ($row = 14; $row <= 37; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(27);

                $sheet->getStyle("B{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $sheet->getStyle("A{$row}:A{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $sheet->getStyle("C{$row}:K{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
            }

            // ========== MERGE KOLOM C-D & J-K SAMPAI BARIS 37 ==========
            for ($row = $startRow; $row <= $endRow; $row++) {
                $sheet->mergeCells("C{$row}:D{$row}");
                $sheet->mergeCells("J{$row}:K{$row}");
            }

            // ========== BORDER SAMPAI BARIS 37 ==========
            $sheet->getStyle("A12:K{$endRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // ========== TOTAL JAM ==========
            $totalJam = $chunk->sum(function ($item) {
                return is_numeric($item->durasi_lembur) ? (float) $item->durasi_lembur : 0;
            });

            $sheet->mergeCells('A38:F38')->setCellValue('A38', 'TOTAL JAM');
            $sheet->mergeCells('G38:I38')->setCellValue('G38', $totalJam);
            $sheet->mergeCells('J38:K38')->setCellValue('J38', '');

            $sheet->getStyle('A38:K38')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A38:K38')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // ========== PERHATIAN ==========
            $sheet->mergeCells('A39:B39')->setCellValue('A39', 'Perhatian');
            for ($i = 40; $i <= 48; $i++) {
                $sheet->mergeCells("B{$i}:K{$i}");
            }
            $sheet->setCellValue('A40', '1');
            $sheet->setCellValue('A41', '2');
            $sheet->setCellValue('A42', '3');
            $sheet->setCellValue('A43', '4');
            $sheet->setCellValue('A47', '5');
            $sheet->setCellValue('B40', '薄枠は申請者（従業員）が記入する。Yang di dalam kotak tipis adalah diisi oleh pemohon (karyawan).');
            $sheet->setCellValue('B41', '太枠は上司が記入する。Yang di dalam kotak tebal adalah diisi oleh Atasan.');
            $sheet->setCellValue('B42', '二重線枠は総務、人事の方で記入する。Yang di dalam kotak dengan 2 garis diisi oleh dept. GA HR.');
            $sheet->setCellValue('B43', '時間外、祝日出勤3時間以上の場合は会社が飯を用意する義務がある為丸して下さい、3時間');
            $sheet->setCellValue('B44', '以内はXにして下さい。');
            $sheet->setCellValue('B45', 'Untuk kerja lembur atau hari libur masuk kerja selama dan atau lebih dari 3 jam, maka perusahaan mempunyai');
            $sheet->setCellValue('B46', 'kewajiban menyediakan makan, untuk itu beri tanda O, jika kurang dari 3 jam beri tanda X.');
            $sheet->setCellValue('B47', '本届けを上司に承認を得た後に総務、人事部の方へ提出する事。');
            $sheet->setCellValue('B48', 'Setelah mendapatkan persetujuan dari atasan, serahkan surat ini ke bagian GA, HRD.');
            $sheet->getStyle('B40:B48')->getAlignment()->setWrapText(true);

            // ========== PRINT SETUP ==========
            $sheet->getPageSetup()
                ->setPaperSize(PageSetup::PAPERSIZE_A4)
                ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                ->setFitToWidth(1)
                ->setFitToHeight(1);

            // Margin standar A4 (bisa disesuaikan)
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setBottom(0.5);
            $sheet->getPageMargins()->setLeft(0.5);
            $sheet->getPageMargins()->setRight(0.5);

            // Center halaman saat print
            $sheet->getPageSetup()->setHorizontalCentered(true);
            $sheet->getPageSetup()->setVerticalCentered(false);
            $sheet->getPageSetup()->setPrintArea("A1:K48");

            $sheetIndex++;
        }

        // Set active sheet ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        if ($tanggal) {
            $fileName = 'Laporan_Lembur_' . \Carbon\Carbon::parse($tanggal)->format('Y-m-d') . '.xlsx';
        } elseif ($dari && $sampai) {
            $fileName = 'Laporan_Lembur_' . \Carbon\Carbon::parse($dari)->format('Y-m-d') . '_sampai_' . \Carbon\Carbon::parse($sampai)->format('Y-m-d') . '.xlsx';
        } elseif ($dari) {
            $fileName = 'Laporan_Lembur_mulai_' . \Carbon\Carbon::parse($dari)->format('Y-m-d') . '.xlsx';
        } elseif ($sampai) {
            $fileName = 'Laporan_Lembur_sampai_' . \Carbon\Carbon::parse($sampai)->format('Y-m-d') . '.xlsx';
        } else {
            $fileName = 'Laporan_Lembur_' . \Carbon\Carbon::now()->format('Y-m-d') . '.xlsx';
        }

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}
