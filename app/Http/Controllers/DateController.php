<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use App\Models\SpecialDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DateController extends Controller
{
    public function read()
    {
        $dates = SpecialDate::get();

        return view('special_dates.read', compact('dates'));
    }

    public function create()
    {
        return view('special_dates.create');
    }

    public function store(Request $request)
    {
        $tanggalList = $request->input('tanggal', []);
        $jenisList = $request->input('jenis_tanggal', []);

        foreach ($tanggalList as $index => $tanggal) {
            SpecialDate::create([
                'tanggal' => $tanggal,
                'jenis_tanggal' => $jenisList[$index],
            ]);
        }

        return redirect()->route('dates.read')->with('success', 'Data berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $date = SpecialDate::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required',
            'jenis_tanggal' => 'required',
        ]);

        $date->tanggal = $validated['tanggal'];
        $date->jenis_tanggal = $validated['jenis_tanggal'];

        $date->save();

        return response()->json(['message' => 'Data berhasil diupdate.'], 200);
    }

    public function destroy($id)
    {
        $date = SpecialDate::find($id);

        if (!$date) {
            return response()->json(['error' => 'Data tanggal tidak ditemukan.'], 404);
        }

        $date->delete();

        return response()->json(['success' => 'Data tanggal berhasil dihapus.']);
    }
}
