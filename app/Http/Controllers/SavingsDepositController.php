<?php

namespace App\Http\Controllers;

use App\Models\SavingsDeposit;
use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsDepositController extends Controller
{
    public function store(Request $request, SavingsGoal $saving)
    {
        abort_unless($saving->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'account_id' => ['nullable', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
        $data['user_id'] = $request->user()->id;

        $saving->deposits()->create($data);

        // Tandai tercapai bila target sudah terpenuhi.
        if ((float) $saving->target_amount > 0 && $saving->fresh()->saved_amount >= (float) $saving->target_amount) {
            $saving->update(['status' => 'completed']);
        }

        return back()->with('success', 'Setoran tabungan dicatat.');
    }

    public function destroy(Request $request, SavingsGoal $saving, SavingsDeposit $deposit)
    {
        abort_unless($saving->user_id === $request->user()->id, 403);
        abort_unless($deposit->savings_goal_id === $saving->id, 404);

        $deposit->delete();

        return back()->with('success', 'Setoran dihapus.');
    }
}
