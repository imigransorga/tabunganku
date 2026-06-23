<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\SavingsDeposit;

class SavingsDepositObserver
{
    private function applyToAccount(?int $accountId, float $delta): void
    {
        if ($accountId && $delta != 0) {
            Account::whereKey($accountId)->increment('balance', $delta);
        }
    }

    public function created(SavingsDeposit $deposit): void
    {
        // Setoran tabungan mengurangi saldo akun sumber.
        $this->applyToAccount($deposit->account_id, -(float) $deposit->amount);
    }

    public function updated(SavingsDeposit $deposit): void
    {
        $this->applyToAccount((int) $deposit->getOriginal('account_id'), (float) $deposit->getOriginal('amount'));
        $this->applyToAccount($deposit->account_id, -(float) $deposit->amount);
    }

    public function deleted(SavingsDeposit $deposit): void
    {
        $this->applyToAccount($deposit->account_id, (float) $deposit->amount);
    }
}
