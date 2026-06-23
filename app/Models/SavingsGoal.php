<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends Model
{
    protected $fillable = [
        'user_id', 'account_id', 'name', 'target_amount', 'frequency',
        'amount_per_period', 'start_date', 'target_date', 'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'amount_per_period' => 'decimal:2',
        'start_date' => 'date',
        'target_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(SavingsDeposit::class);
    }

    /** Total dana yang sudah terkumpul. */
    public function getSavedAmountAttribute(): float
    {
        return (float) $this->deposits()->sum('amount');
    }

    /** Jumlah periode wajib yang sudah berlalu sejak start_date s/d hari ini. */
    public function periodsElapsed(): int
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $today = Carbon::today();
        if ($today->lt($start)) {
            return 0;
        }

        return match ($this->frequency) {
            'daily' => $start->diffInDays($today) + 1,
            'weekly' => intdiv($start->diffInDays($today), 7) + 1,
            'monthly' => $start->diffInMonths($today) + 1,
            default => 0,
        };
    }

    /** Target setoran yang seharusnya sudah masuk s/d hari ini. */
    public function getExpectedAmountAttribute(): float
    {
        return $this->periodsElapsed() * (float) $this->amount_per_period;
    }

    /** Tunggakan = target s/d hari ini - yang sudah disetor (minimal 0). */
    public function getArrearsAttribute(): float
    {
        return max(0, $this->expected_amount - $this->saved_amount);
    }

    /** Progress terhadap target total (%). */
    public function getProgressPercentAttribute(): float
    {
        if ((float) $this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->saved_amount / (float) $this->target_amount) * 100, 1));
    }
}
