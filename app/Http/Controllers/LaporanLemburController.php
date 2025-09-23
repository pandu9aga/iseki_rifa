<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanLemburController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year); // default tahun ini

        $employees = Employee::with('division')
            ->withSum(['lemburs as total_lembur' => function ($query) use ($tahun) {
                $query->whereYear('tanggal_lembur', $tahun);
            }], 'durasi_lembur')
            ->get();

        // bikin list tahun (misal 5 tahun terakhir)
        $tahunList = range(now()->year, now()->year - 4);

        return view('laporan.index', compact('employees', 'tahun', 'tahunList'));
    }

    

    public function export(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $employees = Employee::with('division')
            ->withSum(['lemburs as total_lembur' => function ($query) use ($tahun) {
                $query->whereYear('tanggal_lembur', $tahun);
            }], 'durasi_lembur')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', "LAPORAN LEMBUR TAHUN {$tahun}");
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Header tabel
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Nama Karyawan');
        $sheet->setCellValue('C3', 'Divisi');
        $sheet->setCellValue('D3', 'Total Jam Lembur');

        $sheet->getStyle('A3:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:D3')->getFont()->setBold(true);
        $sheet->getStyle('A3:D3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Isi data
        $row = 4;
        foreach ($employees as $i => $emp) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $emp->nama);
            $sheet->setCellValue("C{$row}", $emp->division->nama ?? '-');
            $sheet->setCellValue("D{$row}", $emp->total_lembur ?? 0);

            $sheet->getStyle("A{$row}:D{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Data_Jam_Lembur_{$tahun}.xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}
