<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $totalBalance = $user->accounts()->where('is_active', true)->sum('balance');

        $incomeMonth = $user->transactions()->approved()->where('type', 'income')
            ->whereBetween('date', [$startMonth, $endMonth])->sum('amount');

        $expenseMonth = $user->transactions()->approved()->where('type', 'expense')
            ->whereBetween('date', [$startMonth, $endMonth])->sum('amount');

        $pendingCount = $user->transactions()->where('status', 'pending')->count();

        $totalSaved = $user->savingsDeposits()->sum('amount');

        // Pengeluaran per kategori (bulan ini) untuk pie chart.
        $expenseByCategory = $user->transactions()->approved()
            ->where('type', 'expense')
            ->whereBetween('date', [$startMonth, $endMonth])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->category->name ?? 'Tanpa Kategori',
                'color' => $row->category->color ?? '#9ca3af',
                'total' => (float) $row->total,
            ]);

        // Tren 6 bulan terakhir (income vs expense) untuk bar chart.
        $trend = collect(range(5, 0))->map(function ($i) use ($user) {
            $month = Carbon::now()->subMonths($i);
            $from = $month->copy()->startOfMonth();
            $to = $month->copy()->endOfMonth();

            return [
                'label' => $month->translatedFormat('M Y'),
                'income' => (float) $user->transactions()->approved()->where('type', 'income')
                    ->whereBetween('date', [$from, $to])->sum('amount'),
                'expense' => (float) $user->transactions()->approved()->where('type', 'expense')
                    ->whereBetween('date', [$from, $to])->sum('amount'),
            ];
        });

        $recent = $user->transactions()->with(['account', 'category'])
            ->latest('date')->latest('id')->limit(8)->get();

        $goals = $user->savingsGoals()->where('status', 'active')->withSum('deposits', 'amount')->get();

        return view('dashboard', compact(
            'totalBalance', 'incomeMonth', 'expenseMonth', 'pendingCount',
            'totalSaved', 'expenseByCategory', 'trend', 'recent', 'goals'
        ));
    }
}
