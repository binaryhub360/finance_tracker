<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'type', 'opening_balance', 'opening_balance_date', 'is_active', 'notes', 'sort_order'])]
class Account extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'opening_balance_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /**
     * Calculate the current balance of this account.
     *
     * Balance = opening_balance
     *         + income to this account
     *         + transfers into this account
     *         - expenses from this account
     *         - transfers out of this account
     */
    public function getBalanceAttribute(): string
    {
        $income = $this->transactions()
            ->where('type', 'income')
            ->sum('amount');

        $expense = $this->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        $transfersIn = $this->incomingTransfers()->sum('amount');
        $transfersOut = $this->outgoingTransfers()->sum('amount');

        $balance = bcadd($this->opening_balance ?? '0', $income, 2);
        $balance = bcadd($balance, $transfersIn, 2);
        $balance = bcsub($balance, $expense, 2);
        $balance = bcsub($balance, $transfersOut, 2);

        return $balance;
    }

    /**
     * Get the type label for display.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'cash' => 'Cash',
            'bank' => 'Bank',
            'mobile_wallet' => 'Mobile Wallet',
            'credit_card' => 'Credit Card',
            'other' => 'Other',
            default => ucfirst($this->type),
        };
    }

    /**
     * Scope to get only active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order then name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
