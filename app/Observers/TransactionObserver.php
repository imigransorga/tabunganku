<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;

class TransactionObserver
{
    /** Efek transaksi terhadap saldo akun (+income / -expense). 0 jika belum approved. */
    private function effect(string $type, string $status, float $amount): float
    {
        if ($status !== 'approved') {
            return 0;
        }

        return $type === 'income' ? $amount : -$amount;
    }

    private function applyToAccount(?int $accountId, float $delta): void
    {
        if ($accountId && $delta != 0) {
            Account::whereKey($accountId)->increment('balance', $delta);
        }
    }

    public function created(Transaction $transaction): void
    {
        $this->applyToAccount(
            $transaction->account_id,
            $this->effect($transaction->type, $transaction->status, (float) $transaction->amount)
        );
    }

    public function updated(Transaction $transaction): void
    {
        // Batalkan efek nilai lama, lalu terapkan efek nilai baru.
        $oldEffect = $this->effect(
            $transaction->getOriginal('type'),
            $transaction->getOriginal('status'),
            (float) $transaction->getOriginal('amount')
        );
        $this->applyToAccount((int) $transaction->getOriginal('account_id'), -$oldEffect);

        $newEffect = $this->effect($transaction->type, $transaction->status, (float) $transaction->amount);
        $this->applyToAccount($transaction->account_id, $newEffect);
    }

    public function deleted(Transaction $transaction): void
    {
        $this->applyToAccount(
            $transaction->account_id,
            -$this->effect($transaction->type, $transaction->status, (float) $transaction->amount)
        );
    }
}
