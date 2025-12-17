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
        return view('users.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $namaList = $request->input('nama', []);
        $usernameList = $request->input('username', []);
        $typeList = $request->input('type', []);
        $divisionList = $request->input('divisi', []);
        $passwordList = $request->input('password', []);
        $teamList = $request->input('team', []);

        foreach ($namaList as $index => $nama) {
            // Cek apakah type admin, jika iya maka division & team = null
            $divisionName = null;
            $teamName = null;

            if ($typeList[$index] !== 'admin') {
                $divisionId = $divisionList[$index] ?? null;
                $divisionName = Division::find($divisionId)?->nama;
                $teamName = $teamList[$index];
            }

            User::create([
                'type' => $typeList[$index],
                'name' => $nama,
                'username' => $usernameList[$index],
                'division' => $divisionName,
                'password' => $passwordList[$index],
                'team' => $teamName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('users.read')->with('success', 'Data berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'type' => 'required|string',
            'division' => 'nullable|string',
            'team' => 'nullable|array',
            'team.*' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->type = $validated['type'];
        $user->division = $validated['division'] ?? null;
        $user->team = $validated['team'] ?? null;

        // Jika password diisi, update dan hash
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return response()->json(['message' => 'Data berhasil diupdate.'], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Data user tidak ditemukan.'], 404);
        }

        $user->delete();

        return response()->json(['success' => 'Data user berhasil dihapus.']);
    }
}
