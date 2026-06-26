<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Budget extends Model
{
    protected $fillable = ['user_id', 'category_id', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Total pengeluaran kategori ini pada bulan berjalan.
     * Hitung yang approved + pending (rejected tidak dihitung), karena pending = rencana belanja.
     */
    public function spentThisMonth(): float
    {
        return (float) Transaction::query()
            ->where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereIn('status', ['approved', 'pending'])
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('amount');
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->amount - $this->spentThisMonth();
    }

    public function getProgressPercentAttribute(): float
    {
        if ((float) $this->amount <= 0) {
            return 0;
        }

        return min(100, round(($this->spentThisMonth() / (float) $this->amount) * 100, 1));
    }
}
