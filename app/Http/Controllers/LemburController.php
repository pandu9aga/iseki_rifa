<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Employee;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    public function index()
    {
        $lemburs = Lembur::with('employee.division')->orderBy('tanggal_lembur', 'desc')->get();
        $employees = Employee::with('division')->get(); // <--- tambah ini
        return view('lemburs.index', compact('lemburs', 'employees'));
    }
    public function create()
    {
        $employees = Employee::with('division')->get();
        return view('lemburs.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_lembur' => 'required|date',
            'waktu_lembur' => 'nullable|date_format:H:i',
            'durasi_lembur' => 'nullable',
            'keterangan_lembur' => 'nullable|string',
            'makan_lembur' => 'nullable|string',
            'approval_lembur' => 'nullable|string',
        ]);

        // Ambil employee untuk mendapatkan divisi
        $employee = Employee::with('division')->findOrFail($request->employee_id);
        $division = $employee->division->nama ?? '-';

        // Simpan data lembur
        Lembur::create([
            'employee_id' => $employee->id,
            'division' => $division,
            'tanggal_lembur' => $request->tanggal_lembur,
            'waktu_lembur' => $request->jam_mulai . ' - ' . $request->jam_selesai, // gabungkan jadi satu            
            'durasi_lembur' => $request->durasi_lembur,
            'keterangan_lembur' => $request->keterangan_lembur,
            'makan_lembur' => $request->makan_lembur,
            'approval_lembur' => $request->approval_lembur,
        ]);

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
}
