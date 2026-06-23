<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TransactionObserver::class)]
class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'account_id', 'category_id', 'type', 'amount',
        'date', 'description', 'status', 'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
