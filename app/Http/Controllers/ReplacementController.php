<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Division;
use App\Models\Report;
use App\Models\Replacement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReplacementController extends Controller
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

        return view('replacements.read', compact('absensis', 'divisions', 'reportToday', 'teamsWithReport', 'allTeams'));
    }

    public function create($id)
    {
        $absensi = Absensi::where('id', $id)
            ->with('employee')
            ->first();

        return view('replacements.create', compact('absensi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'absensi_id' => 'required',
            'replacer_nik' => 'required',
            'production_number' => 'required',
        ]);

        // Cek apakah sudah ada data dengan absensi_id & production_number yang sama
        $exists = Replacement::where('absensi_id', $request->absensi_id)
            ->where('production_number', $request->production_number)
            ->exists();

        if ($exists) {
            return redirect()->route('replacements.read');
        }

        Replacement::create([
            'absensi_id' => $request->absensi_id,
            'replacer_nik' => $request->replacer_nik,
            'production_number' => $request->production_number,
            'created_at' => now(),
        ]);

        return redirect()->route('replacements.read');
    }

    public function byAbsensi($id)
    {
        $data = Replacement::where('absensi_id', $id)
            ->leftJoin('employees', 'employees.nik', '=', 'replacements.replacer_nik')
            ->select(
                'replacements.*',
                'employees.nama as nama_pengganti'
            )
            ->orderBy('replacements.created_at')
            ->get();

        return response()->json($data);
    }
}
