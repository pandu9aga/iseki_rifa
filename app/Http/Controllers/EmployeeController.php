<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function read(Request $request)
    {
        $tahun = $request->input('tahun', date('Y')); // default: tahun sekarang

        // Generate opsi tahun: 2022–2026
        $tahunOptions = range(date('Y') + 1, 2025);
        rsort($tahunOptions);

        $query = Employee::with([
            'division',
            'nilaiTahunan' => fn($q) => $q->whereYear('tanggal_penilaian', $tahun)
        ])->whereNull('deleted_at');

        // Filter berdasarkan division_id
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('nik')->get();

        $divisions = Division::withCount(['employees' => fn($q) => $q->whereNull('deleted_at')])
            ->orderBy('nama')
            ->get();

        return view('employees.read', compact('employees', 'divisions', 'tahun', 'tahunOptions'));
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

        $destinationPath = public_path('photo_employee');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        foreach ($namaList as $index => $nama) {
            $photoName = null;
            if ($request->hasFile("photo_employee.$index")) {
                $file = $request->file("photo_employee.$index");
                if ($file->isValid()) {
                    $photoName = time() . '_' . $index . '_' . Str::slug($nama) . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $photoName);
                }
            }

            Employee::create([
                'nama' => $nama,
                'nik' => $nikList[$index] ?? null,
                'password' => $this->generateRandomPassword(),
                'status' => $statusList[$index] ?? null,
                'division_id' => $divisionList[$index] ?? null,
                'team' => $teamList[$index] ?? null,
                'photo_employee' => $photoName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('employees.read')->with('success', 'Data berhasil disimpan!');
    }

    private function generateRandomPassword()
    {
        return Str::lower(Str::random(3)); // 3 karakter acak huruf dan angka, lowercase
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
            'password' => 'nullable|string|max:3', // Validasi maksimal 3 karakter
            'photo_employee' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $employee = Employee::findOrFail($id);

        if ($request->nik == '-') {
            $nik = null;
        } else {
            $nik = $request->nik;
        }

        $division = Division::where('nama', $request->divisi)->first();
        
        $updateData = [
            'nama' => $request->nama,
            'nik' => $nik,
            'status' => $request->status ?? null,
            'division_id' => $division->id ?? null,
            'team' => $request->team ?? null,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        if ($request->hasFile('photo_employee')) {
            $destinationPath = public_path('photo_employee');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            if ($employee->photo_employee && file_exists($destinationPath . '/' . $employee->photo_employee)) {
                @unlink($destinationPath . '/' . $employee->photo_employee);
            }

            $file = $request->file('photo_employee');
            $photoName = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $photoName);
            $updateData['photo_employee'] = $photoName;
        }

        $employee->update($updateData);

        return response()->json(['message' => 'Data berhasil diupdate.'], 200);
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['error' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $employee->deleted_at = Carbon::now();
        $employee->save();

        return response()->json(['success' => 'Data pegawai berhasil dihapus.']);
    }

    // public function destroy($id)
    // {
    //     $employee = Employee::find($id);

    //     if (!$employee) {
    //         return response()->json(['error' => 'Data pegawai tidak ditemukan.'], 404);
    //     }

    //     $employee->delete();

    //     return response()->json(['success' => 'Data pegawai berhasil dihapus.']);
    // }

    // request nuzul
    public function totalInDivisions()
    {
        $divisions = Division::withCount(['employees' => function ($query) {
            $query->whereNull('deleted_at');
        }])->orderBy('nama')->get();
        return response()->json($divisions);
    }
}
