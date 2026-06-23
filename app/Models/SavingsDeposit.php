<?php

namespace App\Models;

use App\Observers\SavingsDepositObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(SavingsDepositObserver::class)]
class SavingsDeposit extends Model
{
    protected $fillable = [
        'user_id', 'savings_goal_id', 'account_id', 'amount', 'date', 'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(SavingsGoal::class, 'savings_goal_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
