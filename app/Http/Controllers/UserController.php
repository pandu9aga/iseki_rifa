<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function read()
    {
        $users = User::get();
        $divisions = Division::orderBy('nama')->get();
        $types = DB::table('users')
                ->select('type', DB::raw('count(*) as total'))
                ->groupBy('type')
                ->get();

        return view('users.read', compact('users', 'divisions', 'types'));
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
        $statusList = $request->input('status', []);
        $divisionList = $request->input('divisi', []);
        $teamList = $request->input('team', []);

        foreach ($namaList as $index => $nama) {
            Employee::create([
                'nama' => $nama,
                'status' => $statusList[$index],
                'division_id' => $divisionList[$index],
                'team' => $teamList[$index] ?? '-',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('employees.read')->with('success', 'Data berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'type' => 'required|string',
            'division' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->type = $validated['type'];
        $user->division = $validated['division'] ?? null;

        // Jika password diisi, update dan hash
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

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
