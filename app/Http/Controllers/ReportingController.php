<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Report;
use App\Models\SpecialDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border as StyleBorder;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleDriveService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportingController extends Controller
{
    public function read()
    {
        $divisions = Division::orderBy('nama')->get();
        $absensis = Absensi::with('employee', 'employee.division')
            ->orderBy('tanggal', 'desc')
            ->get();

        $reportToday = Report::with('user')
            ->whereDate('created_at', now())
            ->get();

        $teamsWithReport = Report::whereDate('created_at', now())
            ->whereHas('user', function ($q) {
                $q->whereNotNull('team');
            })
            ->with('user')
            ->get()
            ->pluck('user.team')
            ->unique()
            ->toArray();

        $allTeams = [
            'painting a',
            'painting b',
            'transmisi',
            'main line',
            'sub engine',
            'sub assy',
            'inspeksi',
            'mower collector',
            'dst',
        ];

        return view('reporting.read', compact('absensis', 'divisions', 'reportToday', 'teamsWithReport', 'allTeams'));
    }

    public function pdf()
    {
        // $date = '2025-07-14';
        // $absensis = Absensi::with('employee', 'employee.division')
        //     ->whereDate('tanggal', $date)
        //     ->orderBy('tanggal', 'desc')
        //     ->get();

        // $pdf = Pdf::loadView('reporting.pdf', compact('absensis', 'date'));
        
        // return $pdf->download('rekap_absensi.pdf');
        $date = '2025-07-14';
        $absensis = Absensi::with('employee', 'employee.division')
            ->whereDate('tanggal', $date)
            ->orderBy('tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('reporting.pdf', compact('absensis', 'date'));
        
        // Simpan dulu ke storage
        $fileName = 'rekap_absensi_' . now()->format('Ymd_His') . '.pdf';
        $filePath = storage_path('app/' . $fileName);
        file_put_contents($filePath, $pdf->output());

        // Upload ke Google Drive
        $drive = new GoogleDriveService();
        $folderId = '1i7ark9PZaKxm7GbdpFBSxz-WUwjHA5OP'; // Folder tujuan
        $drive->uploadFile($filePath, $folderId, $fileName);

        return "PDF berhasil di-upload ke Google Drive!";
    }

    public function excel(){
        $absensis = Absensi::with('employee', 'employee.division', 'replacements.employee')
            ->orderBy('tanggal')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Absensi');

        // Header
        $headers = [
            'No', 'Nama', 'Jenis Izin', 'Tanggal', 'Keterangan',
            'Status Persetujuan', 'Status', 'Divisi', 'Tim', 'Pengganti'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // Data
        $row = 2;
        foreach ($absensis as $index => $absen) {
            $statusPersetujuan = is_null($absen->is_approved)
                ? 'Menunggu'
                : ($absen->is_approved ? 'Disetujui' : 'Ditolak');

            // Format pengganti
            $penggantiText = '-';
            if ($absen->replacements && $absen->replacements->count() > 0) {
                $penggantiList = [];
                foreach ($absen->replacements as $rep) {
                    $penggantiList[] = sprintf(
                        "%s (%s) - %s",
                        $rep->employee->nama ?? '-',
                        $rep->production_number ?? '-',
                        $rep->created_at ? \Carbon\Carbon::parse($rep->created_at)->format('d/m/Y H:i') : '-'
                    );
                }
                // Gabungkan jadi teks multi-baris
                $penggantiText = implode("\n", $penggantiList);
            }

            $sheet->fromArray([
                $index + 1,
                $absen->employee->nama ?? '-',
                $absen->kategori_label ?? '-',
                $absen->tanggal ? $absen->tanggal->format('d/m/Y') : '-',
                $absen->keterangan ?? '-',
                $statusPersetujuan,
                $absen->employee->status ?? 'Contract',
                $absen->employee->division->nama ?? '-',
                $absen->employee->team ?? '-',
                $penggantiText
            ], NULL, 'A' . $row);

            $row++;
        }

        // Styling
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => StyleBorder::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'wrapText' => true, // penting untuk multi-line
            ],
        ];
        $sheet->getStyle('A1:J' . ($row - 1))->applyFromArray($styleArray);

        // Header bold
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Auto-size kolom
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // File sementara
        $filename = 'rekap_absensi.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->whereNull('deleted_at')->get();
        return view('reporting.create', compact('employees'));
    }

    public function approve(Request $request, $id)
    {
        $absen = Absensi::findOrFail($id);
        $approval = $request->input('approval');

        if ($approval === 'null') {
            $absen->is_approved = null;
        } else {
            $absen->is_approved = (bool) $approval;
        }

        $absen->save();

        // Hitung label dan warna
        if (is_null($absen->is_approved)) {
            $label = 'Menunggu Persetujuan';
            $class = 'bg-yellow';
        } elseif ($absen->is_approved) {
            $label = 'Disetujui';
            $class = 'bg-success';
        } else {
            $label = 'Ditolak';
            $class = 'bg-red';
        }

        // Kirim tombol baru
        $buttonHtml = view('partials.approval-buttons', ['absen' => $absen])->render();

        return response()->json([
            'status_label' => $label,
            'status_class' => $class,
            'button_html' => $buttonHtml
        ]);
    }

    public function store(Request $request)
    {
        $employeeIds = $request->input('nama', []);
        $jenisCutiList = $request->input('jenis_cuti', []);
        $keteranganCutiList = $request->input('keterangan', []);
        $tanggal = now()->format('Y-m-d');

        foreach ($employeeIds as $index => $employeeId) {
            $employee = Employee::with('division')->find($employeeId);

            if ($employee) {
                Absensi::create([
                    'employee_id' => $employee->id,
                    'tanggal' => $tanggal,
                    'kategori' => $this->getKategoriCode($jenisCutiList[$index] ?? ''),
                    'keterangan' => $keteranganCutiList[$index],
                ]);
            }
        }

        // Tambah atau update Report
        $userId = Auth::id();
        $today = now()->startOfDay();

        Report::updateOrCreate(
            [
                'user_id' => $userId,
                'created_at' => Report::query()
                    ->where('user_id', $userId)
                    ->whereDate('created_at', $today)
                    ->value('created_at') ?? now()
            ],
            [
                'updated_at' => now(),
                'divisi' => Auth::user()->division,
            ]
        );

        return redirect()->route('index')->with('success', 'Data berhasil disimpan!');
    }

    public function storeNihil(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Cek apakah sudah ada report user hari ini
        $existing = Report::where('user_id', $user->id)
            ->whereDate('updated_at', $today)
            ->first();

        if ($existing) {
            // Jika sudah ada, update timestamp
            $existing->touch(); // update updated_at
        } else {
            // Jika belum, buat baru
            Report::create([
                'user_id' => $user->id,
                'divisi' => $user->division,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('index')->with('success', 'Laporan Nihil berhasil dikirim.');
    }

    public function dailyReport(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->format('Y-m-d'));

        $absensis = Absensi::with(['employee.division'])
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->groupBy(fn($item) => $item->employee->division->nama ?? 'Lainnya');

        Carbon::setLocale('id');
        $formattedText = "Dear bu Yuli\n\n\n\nBerikut informasi absensi department produksi tanggal " . Carbon::parse($tanggal)->translatedFormat('d F Y') . "\n\n";

        if ($absensis->isEmpty()) {
            $formattedText .= "- Nihil\n\n";
        } else {
            foreach ($absensis as $divisi => $items) {
                $formattedText .= "Prod. " . $divisi . "\n";
                foreach ($items as $index => $item) {
                    $formattedText .= ($index + 1) . ". " . ($item->employee->nama ?? '-') . " (" . $item->kategori_label . ")\n";
                }
                $formattedText .= "\n";
            }
        }

        $formattedText .= "Mohon kerjasamanya\n\n\n\nSupri";

        return response()->json([
            'text' => $formattedText,
        ]);
    }

    public function getKategoriCode($kategori)
    {
        return match ($kategori) {
            'Cuti' => 'C',
            'Cuti Setengah Hari Pagi' => 'CSP',
            'Cuti Setengah Hari Siang' => 'CSS',
            'Terlambat' => 'T',
            'Izin Keluar' => 'IK',
            'Pulang Cepat' => 'P',
            'Absen' => 'A',
            'Absen Setengah Hari Pagi' => 'ASP',
            'Absen Setengah Hari Siang' => 'ASS',
            'Sakit' => 'S',
            'Cuti Khusus' => 'CK',
            'Serikat' => 'Sk',
            default => '',
        };
    }

    public function getEmployeeDetails(Request $request)
    {
        $employee_id = $request->input('employee_id');
        $employee = Employee::with('division')->find($employee_id);

        if ($employee) {
            return response()->json([
                'status' => $employee->status,
                'divisi' => $employee->division->nama ?? '',
                'team' => $employee->team ?? '',
            ]);
        }

        return response()->json([
            'status' => '',
            'divisi' => '',
            'team' => '',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->load('employee');

        $absensi->update([
            'kategori' => $this->getKategoriCode($request->kategori),
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'is_approved' => null,
            'approved_at' => null,
            'updated_at' => now(),
        ]);

        // Tambah atau update Report
        $userId = Auth::id();
        $today = now()->startOfDay();

        Report::updateOrCreate(
            [
                'user_id' => $userId,
                'created_at' => Report::query()
                    ->where('user_id', $userId)
                    ->whereDate('created_at', $today)
                    ->value('created_at') ?? now()
            ],
            [
                'updated_at' => now(),
                'divisi' => Auth::user()->division,
            ]
        );

        return response()->json(['message' => 'Data berhasil diupdate.'], 200);
    }

    public function destroy($id)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
            return response()->json(['error' => 'Data absensi tidak ditemukan.'], 404);
        }

        $absensi->delete();

        return response()->json(['success' => 'Data absensi berhasil dihapus.']);
    }

    public function export(Request $request)
    {   
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        Carbon::setLocale('id');

        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $bulan);
        $dateObj = Carbon::createFromDate($year, $month, 1);

        $specialDates = SpecialDate::whereYear('tanggal', $year)
        ->whereMonth('tanggal', $month)
        ->get()
        ->keyBy(function ($item) {
            return Carbon::parse($item->tanggal)->format('Y-m-d');
        });

        $daysInMonth = $dateObj->daysInMonth;
        $monthName = strtoupper($dateObj->translatedFormat('F'));

        $sheet->setTitle($monthName . '_' . $year);

        $employees = Employee::with('division')
            ->where(function ($query) use ($year, $month) {
                $query->whereNull('deleted_at')
                    ->orWhereYear('deleted_at', '<', $year)
                    ->orWhere(function ($q) use ($year, $month) {
                        $q->whereYear('deleted_at', $year)
                            ->whereMonth('deleted_at', '<', $month);
                    })
                    ->orWhere(function ($q) use ($year, $month) {
                        $q->whereYear('deleted_at', $year)
                            ->whereMonth('deleted_at', $month);
                    });
            })
            ->get();

        $absensisMonth = Absensi::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get();
        $absensisYear = Absensi::whereYear('tanggal', $year)
            ->whereMonth('tanggal', '<=', $month)
            ->get();

        // Summary per divisi & status
        $summary = [];
        $employeesByDivision = $employees->sortBy(fn($emp) => $emp->division->id ?? PHP_INT_MAX);
        foreach ($employeesByDivision as $emp) {
            $div = $emp->division->nama ?? '';
            $status = $emp->status ?? 'Contract';
            if (!isset($summary[$div])) {
                $summary[$div] = ['Permanent' => 0, 'Contract' => 0];
            }
            if (in_array($status, ['Permanent', 'Contract'])) {
                $summary[$div][$status]++;
            }
        }

        // Header summary
        $sheet->setCellValue('B1', 'Div');
        $sheet->setCellValue('C1', 'Member');
        $sheet->setCellValue('D1', 'Permanent');
        $sheet->setCellValue('E1', 'Contract');

        $row = 2;
        $totalPermanent = 0;
        $totalContract = 0;
        foreach ($summary as $div => $counts) {
            $sheet->setCellValue("B{$row}", strtoupper($div));
            $totalMember = $counts['Permanent'] + $counts['Contract'];
            $sheet->setCellValue("C{$row}", $totalMember);
            $sheet->setCellValue("D{$row}", $counts['Permanent']);
            $sheet->setCellValue("E{$row}", $counts['Contract']);
            $totalPermanent += $counts['Permanent'];
            $totalContract += $counts['Contract'];
            $row++;
        }

        // Baris total pegawai
        $headerRow = 6;
        $sheet->setCellValue('B' . ($headerRow - 1), 'Total');
        $sheet->setCellValue('C' . ($headerRow - 1), $employees->count());
        $sheet->setCellValue('D' . ($headerRow - 1), $totalPermanent);
        $sheet->setCellValue('E' . ($headerRow - 1), $totalContract);

        // Header kolom utama
        $sheet->setCellValue('A6', 'No')->mergeCells('A6:A7');
        $sheet->setCellValue('B6', 'Nama')->mergeCells('B6:B7');
        $sheet->setCellValue('C6', 'Status')->mergeCells('C6:C7');
        $sheet->setCellValue('D6', 'Div')->mergeCells('D6:D7');
        $sheet->setCellValue('E6', 'Team')->mergeCells('E6:E7');

        // Header bulan dan tanggal
        $startTanggalColIndex = 8;
        $endTanggalColIndex = $startTanggalColIndex + $daysInMonth - 1;

        $startTanggalCol = Coordinate::stringFromColumnIndex($startTanggalColIndex);
        $endTanggalCol = Coordinate::stringFromColumnIndex($endTanggalColIndex);

        $sheet->setCellValue("{$startTanggalCol}6", $monthName);
        $sheet->mergeCells("{$startTanggalCol}6:{$endTanggalCol}6");

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $col = Coordinate::stringFromColumnIndex($startTanggalColIndex + $day - 1);
            $sheet->setCellValue("{$col}7", $day);
        }

        // Total absensi tahun ini
        $kategoriList = ['C', 'CS', 'T', 'IK', 'P', 'A', 'AS', 'S', 'CK', 'Sk'];
        $startKategoriColIndex = $endTanggalColIndex + 1;
        $endKategoriColIndex = $startKategoriColIndex + count($kategoriList) - 1;

        $startKategoriCol = Coordinate::stringFromColumnIndex($startKategoriColIndex);
        $endKategoriCol = Coordinate::stringFromColumnIndex($endKategoriColIndex);
        $sheet->setCellValue("{$startKategoriCol}6", 'TOTAL ABSENSI TAHUN INI');
        $sheet->mergeCells("{$startKategoriCol}6:{$endKategoriCol}6");

        foreach ($kategoriList as $i => $kode) {
            $col = Coordinate::stringFromColumnIndex($startKategoriColIndex + $i);
            $sheet->setCellValue("{$col}7", $kode);
        }

        // Lookup absensi bulan & tahun
        $absensiLookupMonth = [];
        $approvedLookup = [];

        foreach ($absensisMonth as $absen) {
            $day = (int) $absen->tanggal->format('j');
            $absensiLookupMonth[$absen->employee_id][$day] = $absen->kategori;
            $approvedLookup[$absen->employee_id][$day] = $absen->is_approved ?? false;
        }

        $absensiLookupYear = [];
        foreach ($absensisYear as $absen) {
            if(!$absen->is_approved) continue;
            
            $empId = $absen->employee_id;
            $kategori = $absen->kategori;
            if (!isset($absensiLookupYear[$empId])) {
                $absensiLookupYear[$empId] = array_fill_keys($kategoriList, 0);
            }
            // Normalisasi kategori: CSP dan CSS dijadikan CS
            // $kategori = in_array($kategori, ['CSP', 'CSS']) ? 'CS' : $kategori;
            // Normalisasi kategori
            if (in_array($kategori, ['CSP', 'CSS'])) {
                $kategori = 'CS';
            } elseif (in_array($kategori, ['ASP', 'ASS'])) {
                $kategori = 'AS';
            }

            // Tambahkan key 'CS' kalau belum ada
            if (!isset($absensiLookupYear[$empId]['CS'])) {
                $absensiLookupYear[$empId]['CS'] = 0;
            }

            // Hitung hanya kategori yang diizinkan
            if (in_array($kategori, $kategoriList) || $kategori === 'CS') {
                $absensiLookupYear[$empId][$kategori]++;
            }
        }

        // Highlight jenis izin jika ada
        $row = $headerRow + 2;
        $no = 1;
        $highlightStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92D050'],
            ]
        ];

        // Data pegawai lengkap
        foreach ($employees as $emp) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $emp->nama);
            $sheet->setCellValue("C{$row}", $emp->status ?? '');
            $sheet->setCellValue("D{$row}", $emp->division->nama ?? '');
            $sheet->setCellValue("E{$row}", $emp->team ?? '');

            $deletedDay = null;
            if ($emp->deleted_at) {
                $deletedAt = Carbon::parse($emp->deleted_at);
                if ($deletedAt->year == $year && $deletedAt->month == $month) {
                    $deletedDay = $deletedAt->day;
                }
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $colIndex = $startTanggalColIndex + $day - 1;
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $date = Carbon::create($year, $month, $day);

                if ($deletedDay && $day >= $deletedDay) {
                    $sheet->setCellValue("{$col}{$row}", '-');
                } else {
                    $val = $absensiLookupMonth[$emp->id][$day] ?? '';
                    // CS untuk CSP & CSS
                    // $displayVal = in_array($val, ['CSP', 'CSS']) ? 'CS' : $val;
                    $displayVal = match ($val) {
                        'CSP', 'CSS' => 'CS',
                        'ASP', 'ASS' => 'AS',
                        default     => $val,
                    };

                    $sheet->setCellValue("{$col}{$row}", $displayVal);

                    if (
                        in_array($displayVal, $kategoriList) &&
                        ($approvedLookup[$emp->id][$day] ?? false)
                    ) {
                        $sheet->getStyle("{$col}{$row}")->applyFromArray($highlightStyle);
                    }

                    // Di dalam foreach tanggal
                    $isWeekend = $date->isWeekend();
                    $special = $specialDates[$date->format('Y-m-d')] ?? null;

                    $shouldHighlight = false;

                    if ($isWeekend) {
                        // Hanya highlight weekend kalau BUKAN libur masuk
                        $shouldHighlight = !($special && $special->jenis_tanggal === 'libur masuk');
                    } else {
                        // Hanya highlight weekday kalau ADA jenis libur resmi
                        if ($special && in_array($special->jenis_tanggal, ['libur nasional', 'cuti perusahaan', 'libur pengganti'])) {
                            $shouldHighlight = true;
                        }
                    }

                    $cell = "{$col}{$row}";

                    if ($shouldHighlight) {
                        $sheet->getStyle($cell)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'BF8F00'],
                            ],
                        ]);
                    }
                }
            }

            $startRangeCol = Coordinate::stringFromColumnIndex($startTanggalColIndex);
            $endRangeCol = Coordinate::stringFromColumnIndex($endTanggalColIndex);
            $empId = $emp->id;
            foreach ($kategoriList as $i => $kode) {
                $col = Coordinate::stringFromColumnIndex($startKategoriColIndex + $i);
                $val = $absensiLookupYear[$empId][$kode] ?? 0;
                $sheet->setCellValue("{$col}{$row}", $val);
            }

            $row++;
        }

        // Autofit semua kolom
        $highestColIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());
        for ($i = 1; $i <= 5; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        for ($i = 6; $i <= $highestColIndex; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setWidth(3.30);
        }
        $sheet->getRowDimension(7)->setRowHeight(31.50);

        $lastCol = $sheet->getHighestColumn();
        $lastColEmployee = 5;
        $lastRowEmployee = $sheet->getHighestRow();
        $startColMonth = $lastColEmployee + 1;
        $startRowTotal = $lastRowEmployee + 3;

        // Border cell data
        $sheet->getStyle("B1:E5")->getBorders()->getAllBorders()->setBorderStyle(StyleBorder::BORDER_THIN);
        $sheet->getStyle("A6:E{$lastRowEmployee}")->getBorders()->getAllBorders()->setBorderStyle(StyleBorder::BORDER_THIN);
        $sheet->getStyle("H6:{$lastCol}{$lastRowEmployee}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(StyleBorder::BORDER_THIN);

        // Total Per Hari, Per Kategori
        $row += 2;
        $sheet->setCellValue("E{$row}", 'TOTAL');
        $sheet->mergeCells("E{$row}:F{$row}");
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $col = Coordinate::stringFromColumnIndex($startTanggalColIndex + $day - 1);
            $formula = "=COUNTIF({$col}" . ($headerRow + 2) . ":{$col}" . ($lastRowEmployee) . ", \"<>\"&\"\")";
            $sheet->setCellValue("{$col}{$row}", $formula);
        }
        $row++;

        $kategoriLabel = [
            'C' => 'CUTI',
            'CS' => 'CUTI SETENGAH HARI',
            'T' => 'TERLAMBAT',
            'IK' => 'IZIN KELUAR',
            'P' => 'PULANG CEPAT',
            'A' => 'ABSEN',
            'AS' => 'ABSEN SETENGAH HARI',
            'S' => 'SAKIT',
            'CK' => 'CUTI KHUSUS',
            'Sk' => 'SERIKAT',
        ];

        foreach ($kategoriLabel as $kode => $label) {
            $sheet->setCellValue("E{$row}", $label);
            $sheet->mergeCells("E{$row}:F{$row}");
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $col = Coordinate::stringFromColumnIndex($startTanggalColIndex + $day - 1);
                $formula = "=COUNTIF({$col}" . ($headerRow + 2) . ":{$col}" . ($lastRowEmployee) . ", \"{$kode}\")";
                $sheet->setCellValue("{$col}{$row}", $formula);
            }
            $row++;
        }

        // Alignment
        $sheet->getStyle("B1:E1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C2:E5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B2:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("A6:E6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A7:A{$sheet->getHighestRow()}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C8:E{$sheet->getHighestRow()}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("A6:{$lastCol}{$sheet->getHighestRow()}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("H6:{$lastCol}{$sheet->getHighestRow()}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H7:{$lastCol}{$sheet->getHighestRow()}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        // Warna background
        $sheet->getStyle('B1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('000000');
        $sheet->getStyle("E{$startRowTotal}:AL{$startRowTotal}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FCE4D6');
        $sheet->getStyle('B1:E1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('B5:E5')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFFF00');

        // Font style
        $sheet->getStyle('A1:' . $lastCol . $sheet->getHighestRow())->getFont()->setName('Candara Light');
        $sheet->getStyle("B5:E5")->getFont()->setSize('20');
        $sheet->getStyle("B5:E5")->getFont()->setBold(true);
        $sheet->getStyle("E{$startRowTotal}:AL{$startRowTotal}")->getFont()->setSize('14');

        // Filter dan Freeze
        $sheet->setAutoFilter("A7:{$lastCol}7");
        $sheet->freezePane("G8");

        // Output file
        $filename = "laporan_izin_{$year}_{$month}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
