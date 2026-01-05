<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Division;
use App\Models\NilaiPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PenilaianTahunanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $divisionId = $request->input('division_id');
        $status = $request->input('status');

        $employees = Employee::with([
            'division',
            'nilaiTahunan' => fn($q) => $q->whereYear('tanggal_penilaian', $tahun)
        ])
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->whereNull('deleted_at')
            ->orderBy('nik')
            ->get();

        // Generate opsi tahun: 2022–2026
        $tahunOptions = range(date('Y') + 1, 2025);
        rsort($tahunOptions);

        $divisions = Division::orderBy('nama')->get();

        return view('penilaian.index', compact('employees', 'tahun', 'tahunOptions', 'divisions', 'divisionId', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        $tahun = $request->tahun;
        $nilaiList = $request->input('nilai', []);

        // ✅ Hapus semua nilai untuk tahun ini dulu (strategi: replace)
        NilaiPegawai::whereYear('tanggal_penilaian', $tahun)->delete();

        $inserts = [];
        foreach ($nilaiList as $employeeId => $nilai) {
            if (in_array($nilai, ['AA', 'A', 'B', 'C', 'D', 'E'])) {
                $inserts[] = [
                    'employee_id' => $employeeId,
                    'nilai' => $nilai,
                    'tanggal_penilaian' => "$tahun-12-31", // akhir tahun
                    // 'created_at' => now(),
                    // 'updated_at' => now(),
                ];
            }
        }

        if (!empty($inserts)) {
            NilaiPegawai::insert($inserts);
        }

        return back()->with('success', "Penilaian tahun $tahun berhasil disimpan.");
    }

    // Export ke CSV (dengan judul dan pemisah sederhana)
    public function export(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $data = Employee::select(
            'employees.nama',
            'employees.nik',
            DB::raw("COALESCE(divisions.nama, '-') as divisi"),
            'employees.team',
            'employees.status',
            DB::raw("COALESCE(nilai.nilai, '-') as nilai")
        )
            ->leftJoin('divisions', 'employees.division_id', '=', 'divisions.id')
            ->leftJoin('nilai_pegawai as nilai', function ($join) use ($tahun) {
                $join->on('nilai.employee_id', '=', 'employees.id')
                    ->whereYear('nilai.tanggal_penilaian', $tahun);
            })
            ->whereNull('employees.deleted_at')
            ->orderBy('employees.nik')
            ->get();

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul
        $sheet->setCellValue('A1', "Penilaian Karyawan Tahun {$tahun}");
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'DDDDDD']
            ]
        ]);

        // Header kolom
        $headers = ['Nama', 'NIK', 'Divisi', 'Tim', 'Status', 'Nilai'];
        $sheet->fromArray($headers, null, 'A2');

        // Styling header
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'EEEEEE']
            ]
        ]);

        // Isi data
        $rowIndex = 3;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $rowIndex, $row->nama ?? '');
            $sheet->setCellValue('B' . $rowIndex, $row->nik ?? '');
            $sheet->setCellValue('C' . $rowIndex, $row->divisi ?? '-');
            $sheet->setCellValue('D' . $rowIndex, $row->team ?? '-');
            $sheet->setCellValue('E' . $rowIndex, $row->status ?? '-');
            $sheet->setCellValue('F' . $rowIndex, $row->nilai ?? '-');

            // Tambahkan border untuk setiap sel data
            $sheet->getStyle("A{$rowIndex}:F{$rowIndex}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);

            $rowIndex++;
        }

        // Atur lebar kolom otomatis
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set header response untuk Excel
        $fileName = "Penilaian_Karyawan_Tahun_{$tahun}.xlsx";
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $fileName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        );
    }
}
