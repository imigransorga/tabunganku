<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SavingsGoalController extends Controller
{
    public function index(Request $request)
    {
        $goals = $request->user()->savingsGoals()->latest()->get();

        return view('savings.index', compact('goals'));
    }

    public function create(Request $request)
    {
        return view('savings.create', [
            'accounts' => $request->user()->accounts()->get(),
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $request->user()->savingsGoals()->create($data);

        return redirect()->route('savings.index')->with('success', 'Target tabungan dibuat.');
    }

    public function show(Request $request, SavingsGoal $saving)
    {
        $this->authorizeOwner($saving);
        $saving->load(['account', 'deposits' => fn ($q) => $q->latest('date')->latest('id')]);
        $accounts = $request->user()->accounts()->where('is_active', true)->get();

        return view('savings.show', [
            'goal' => $saving,
            'accounts' => $accounts,
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    public function edit(Request $request, SavingsGoal $saving)
    {
        $this->authorizeOwner($saving);

        return view('savings.edit', [
            'goal' => $saving,
            'accounts' => $request->user()->accounts()->get(),
        ]);
    }

    public function update(Request $request, SavingsGoal $saving)
    {
        $this->authorizeOwner($saving);
        $data = $this->validateData($request);
        $saving->update($data);

        return redirect()->route('savings.index')->with('success', 'Target tabungan diperbarui.');
    }

    public function destroy(Request $request, SavingsGoal $saving)
    {
        $this->authorizeOwner($saving);
        $saving->delete();

        return redirect()->route('savings.index')->with('success', 'Target tabungan dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'in:daily,weekly,monthly'],
            'amount_per_period' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['required', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:active,completed,paused'],
        ]);
    }

    private function authorizeOwner(SavingsGoal $goal): void
    {
        abort_unless($goal->user_id === request()->user()->id, 403);
    }
}
