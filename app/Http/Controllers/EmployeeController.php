<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function read()
    {
        $employees = Employee::with('division')->get();
        $divisions = Division::withCount('employees')->orderBy('nama')->get();

        return view('employees.read', compact('employees', 'divisions'));
    }

    public function create()
    {
        $divisions = Division::orderBy('nama')->get();
        return view('employees.create', compact('divisions'));
    }

    public function getIdByName(Request $request)
    {
        $nama = $request->query('nama');

        if (!$nama) {
            return response()->json(['error' => 'Nama kosong'], 400);
        }

        $employee = Employee::where('nama', $nama)->first();

        if (!$employee) {
            return response()->json(['error' => 'Karyawan tidak ditemukan'], 404);
        }

        return response()->json(['id' => $employee->id]);
    }

    public function getDetailById($id)
    {
        $employee = Employee::with('division')->find($id);

        if (!$employee) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'nama' => $employee->nama,
            'divisi' => $employee->division ? $employee->division->nama : null,
            'team' => $employee->team,
            'status' => $employee->status,
        ]);
    }

    public function store(Request $request)
    {
        $namaList = $request->input('nama', []);
        $nikList = $request->input('nik', []);
        $statusList = $request->input('status', []);
        $divisionList = $request->input('divisi', []);
        $teamList = $request->input('team', []);

        foreach ($namaList as $index => $nama) {
            Employee::create([
                'nama' => $nama,
                'nik' => $nikList[$index],
                'status' => $statusList[$index],
                'division_id' => $divisionList[$index],
                'team' => $teamList[$index] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('employees.read')->with('success', 'Data berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
        ]);

        $employee = Employee::findOrFail($id);

        $division = Division::where('nama', $request->divisi)->first();
        $employee->update([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'status' => $request->status,
            'division_id' => $division->id ?? null,
            'team' => $request->team,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Data berhasil diupdate.'], 200);
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['error' => 'Data absensi tidak ditemukan.'], 404);
        }

        $employee->delete();

        return response()->json(['success' => 'Data absensi berhasil dihapus.']);
    }
}
