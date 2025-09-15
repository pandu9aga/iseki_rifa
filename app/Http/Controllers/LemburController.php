<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Style\Font as PhpFont;



class LemburController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data karyawan (untuk form tambah/edit)
        $employees = Employee::with('division')->get();

        // Ambil filter dari request
        $tanggal = $request->get('tanggal'); // filter perhari
        $dari    = $request->get('dari');    // filter range mulai
        $sampai  = $request->get('sampai');  // filter range sampai

        // Query dasar
        $query = Lembur::with(['employee', 'employee.division']);

        // Filter kalau ada tanggal / range
        if ($tanggal) {
            $query->whereDate('tanggal_lembur', $tanggal);
        }
        if ($dari) {
            $query->whereDate('tanggal_lembur', '>=', $dari);
        }
        if ($sampai) {
            $query->whereDate('tanggal_lembur', '<=', $sampai);
        }

        // Urutkan dari tanggal terlama → terbaru
        $lemburs = $query->orderBy('tanggal_lembur', 'asc')->get();

        return view('lemburs.index', compact('lemburs', 'employees'));
    }

    public function create()
    {
        $employees = Employee::with('division')->get();
        return view('lemburs.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // Validasi array
        $request->validate([
            'employee_id.*' => 'required|exists:employees,id',
            'tanggal_lembur.*' => 'required|date',
            'jam_mulai.*' => 'nullable|date_format:H:i',
            'jam_selesai.*' => 'nullable|date_format:H:i',
            'durasi_lembur.*' => 'nullable|string',
            'keterangan_lembur.*' => 'nullable|string',
            'makan_lembur.*' => 'nullable|string',
        ]);

        $employee_ids = $request->employee_id;
        $tanggal = $request->tanggal_lembur;
        $jam_mulai = $request->jam_mulai;
        $jam_selesai = $request->jam_selesai;
        $durasi = $request->durasi_lembur;
        $keterangan = $request->keterangan_lembur;
        $makan = $request->makan_lembur;

        foreach ($employee_ids as $index => $emp_id) {
            $employee = Employee::with('division')->findOrFail($emp_id);
            $division = $employee->division->nama ?? '-';

            Lembur::create([
                'employee_id' => $emp_id,
                'division' => $division,
                'tanggal_lembur' => $tanggal[$index] ?? null,
                'waktu_lembur' => ($jam_mulai[$index] ?? '') . ' - ' . ($jam_selesai[$index] ?? ''),
                'durasi_lembur' => $durasi[$index] ?? null,
                'keterangan_lembur' => $keterangan[$index] ?? null,
                'makan_lembur' => $makan[$index] ?? null,
            ]);
        }

        return redirect()->route('lemburs.index')->with('success', 'Data lembur berhasil disimpan');
    }

    public function edit($id)
    {
        $lembur = Lembur::findOrFail($id);
        $employees = Employee::with('division')->get();
        // Split waktu_lembur menjadi jam_mulai dan jam_selesai
        [$jam_mulai, $jam_selesai] = explode(' - ', $lembur->waktu_lembur . ' - ');
        return view('lemburs.edit', compact('lembur', 'employees', 'jam_mulai', 'jam_selesai'));
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
            'durasi_lembur' => $request->durasi_lembur,
            'keterangan_lembur' => $request->keterangan_lembur,
            'makan_lembur' => $request->makan_lembur,
        ]);

        // return redirect()->route('lemburs.index')->with('success', 'Data lembur berhasil diupdate');
        return response()->json([
            'success' => true,
            'message' => 'Data lembur berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->delete();

        // return redirect()->route('lemburs.index')->with('success', 'Data lembur berhasil dihapus');
        return response()->json([
            'success' => true,
            'message' => 'Data lembur berhasil dihapus'
        ]);
    }

    public function exportLembur(Request $request)
    {
        $tanggal = $request->get('tanggal'); // filter perhari
        $dari    = $request->get('dari');    // filter range mulai
        $sampai  = $request->get('sampai');  // filter range sampai

        $query = Lembur::with(['employee', 'employee.division']);

        if ($tanggal) {
            $query->whereDate('tanggal_lembur', $tanggal);
        }

        if ($dari) {
            $query->whereDate('tanggal_lembur', '>=', $dari);
        }

        if ($sampai) {
            $query->whereDate('tanggal_lembur', '<=', $sampai);
        }

        $lemburs = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Footlight MT Light') // font yang kamu punya di storage
            ->setSize(11);


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
        $drawing2->setCoordinates('H1');
        $drawing2->setHeight(55);
        $drawing2->setWorksheet($sheet);
        $sheet->mergeCells('H1:K1');

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

        // Gunakan tanggal sesuai filter
        if ($tanggal) {
            $tglPermohonan = Carbon::parse($tanggal)->format('d-m-Y');
        } elseif ($dari && $sampai) {
            $tglPermohonan = Carbon::parse($dari)->format('d-m-Y') . ' s/d ' . Carbon::parse($sampai)->format('d-m-Y');
        } elseif ($dari) {
            $tglPermohonan = 'Mulai ' . Carbon::parse($dari)->format('d-m-Y');
        } elseif ($sampai) {
            $tglPermohonan = 'Sampai ' . Carbon::parse($sampai)->format('d-m-Y');
        } else {
            $tglPermohonan = Carbon::now()->format('d-m-Y');
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

        // Merge J:K untuk header
        $sheet->mergeCells('J12:K12')->setCellValue('J12', '上司の承認');
        $sheet->mergeCells('J13:K13')->setCellValue('J13', 'Persetujuan Atasan');

        $sheet->getStyle('A12:K13')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        $sheet->getRowDimension(12)->setRowHeight(15);
        $sheet->getRowDimension(13)->setRowHeight(30);

        // Atur lebar kolom header
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
        $endRow   = max($startRow + count($lemburs) - 1, 37); // minimal sampai baris 37

        foreach ($lemburs as $i => $item) {
            $row = $startRow + $i;

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("E{$row}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("H{$row}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("J{$row}:K{$row}")->getAlignment()->setWrapText(true);

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $item->employee->nama ?? '');
            $sheet->setCellValue("C{$row}", $item->employee->division->nama ?? '');
            $sheet->setCellValue("E{$row}", Carbon::parse($item->tanggal_lembur)->format('d-m-Y'));
            $sheet->setCellValue("F{$row}", $item->waktu_lembur ?? '');
            $sheet->setCellValue("G{$row}", $item->durasi_lembur ?? '');
            $sheet->setCellValue("H{$row}", $item->keterangan_lembur ?? '');
            $sheet->setCellValue("I{$row}", $item->makan_lembur ?? '');
            $sheet->setCellValue("J{$row}", $item->approval_lembur ?? '');
        }

        // ========== MERGE KOLOM C-D & J-K SAMPAI BARIS 37 ==========
        for ($row = $startRow; $row <= $endRow; $row++) {
            $sheet->mergeCells("C{$row}:D{$row}");
            $sheet->mergeCells("J{$row}:K{$row}");
        }

        // ========== BORDER SAMPAI BARIS 37 ==========
        $sheet->getStyle("A12:K{$endRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Hitung total jam lembur dari data yang difilter
        $totalJam = $lemburs->sum(function ($item) {
            // Pastikan durasi_lembur adalah angka
            return is_numeric($item->durasi_lembur) ? $item->durasi_lembur : 0;
        });

        // ========== TOTAL JAM ==========
        // Merge sel A38:F38 untuk tulisan
        $sheet->mergeCells('A38:F38')->setCellValue('A38', 'TOTAL JAM');

        // Merge sel G38:I38 untuk menampilkan total jam
        $sheet->mergeCells('G38:I38')->setCellValue('G38', $totalJam);

        // Merge sel J38:K38 kosong
        $sheet->mergeCells('J38:K38')->setCellValue('J38', '');

        // Style borders dan alignment
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

        $writer = new Xlsx($spreadsheet);

        // Tentukan nama file sesuai filter
        if ($tanggal) {
            $fileName = 'Laporan_Lembur_' . Carbon::parse($tanggal)->format('Y-m-d') . '.xlsx';
        } elseif ($dari && $sampai) {
            $fileName = 'Laporan_Lembur_' . Carbon::parse($dari)->format('Y-m-d') . '_sampai_' . Carbon::parse($sampai)->format('Y-m-d') . '.xlsx';
        } elseif ($dari) {
            $fileName = 'Laporan_Lembur_mulai_' . Carbon::parse($dari)->format('Y-m-d') . '.xlsx';
        } elseif ($sampai) {
            $fileName = 'Laporan_Lembur_sampai_' . Carbon::parse($sampai)->format('Y-m-d') . '.xlsx';
        } else {
            $fileName = 'Laporan_Lembur_' . Carbon::now()->format('Y-m-d') . '.xlsx';
        }

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}
