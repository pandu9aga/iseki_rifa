<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $budgets = Budget::whereRaw("SUBSTRING(Tanggal_Budget, 1, 4) = ?", [$tahun])->get();

        $budgetData = [];
        foreach ($budgets as $budget) {
            $budgetData[$budget->Tanggal_Budget] = $budget->Jumlah_Budget;
        }

        return view('lemburs.budget', compact('tahun', 'budgetData'));
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'tahun' => 'required|digits:4',
            'budgets' => 'nullable|array',
            'budgets.*' => 'nullable|integer|min:0'
        ]);

        $tahun = $request->tahun;
        $input = $request->budgets ?? [];

        $bulanList = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        foreach ($bulanList as $bulan) {
            $key = "$tahun-$bulan";
            $nilai = $input[$key] ?? null;

            if ($nilai === null || $nilai === '') {
                Budget::where('Tanggal_Budget', $key)->delete();
            } else {
                Budget::updateOrCreate(
                    ['Tanggal_Budget' => $key],
                    ['Jumlah_Budget' => (int) $nilai]
                );
            }
        }

        return redirect()->route('budget.lembur.index', ['tahun' => $tahun])
            ->with('success', 'Budget berhasil diperbarui.');
    }
}
