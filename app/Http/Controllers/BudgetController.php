<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Semua kategori pengeluaran + budget-nya (kalau ada).
        $categories = $user->categories()->where('type', 'expense')
            ->with('budget')->orderBy('name')->get();

        $budgets = $user->budgets()->with('category')->get();

        $totalBudget = (float) $budgets->sum('amount');
        $totalSpent = $budgets->sum(fn ($b) => $b->spentThisMonth());

        return view('budgets.index', [
            'categories' => $categories,
            'budgets' => $budgets,
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'month' => Carbon::now()->translatedFormat('F Y'),
        ]);
    }

    /** Simpan/ubah budget sebuah kategori (upsert). amount 0 = hapus budget. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $category = $request->user()->categories()->where('type', 'expense')->findOrFail($data['category_id']);

        if ((float) $data['amount'] <= 0) {
            $category->budget()->delete();

            return back()->with('success', 'Budget kategori dihapus.');
        }

        $request->user()->budgets()->updateOrCreate(
            ['category_id' => $category->id],
            ['amount' => $data['amount']]
        );

        return back()->with('success', 'Budget disimpan.');
    }

    public function destroy(Request $request, Budget $budget)
    {
        abort_unless($budget->user_id === $request->user()->id, 403);
        $budget->delete();

        return back()->with('success', 'Budget dihapus.');
    }
}
