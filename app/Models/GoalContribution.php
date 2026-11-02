<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'goal_id',
    'user_id',
    'type',
    'amount',
    'date',
    'account_id',
    'transaction_id',
    'notes',
])]
class GoalContribution extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getTypeBadgeClassesAttribute(): string
    {
        return $this->type === 'deposit'
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60'
            : 'bg-rose-50 text-rose-700 border-rose-200/60';
    }
}
