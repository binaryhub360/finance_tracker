<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'type', 'date', 'amount',
    'account_id', 'income_category_id', 'expense_category_id',
    'from_account_id', 'to_account_id',
    'description', 'reference',
])]
class Transaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function incomeCategory(): BelongsTo
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    /**
     * Get the category name based on transaction type.
     */
    public function getCategoryNameAttribute(): string
    {
        return match ($this->type) {
            'income' => $this->incomeCategory?->name ?? 'N/A',
            'expense' => $this->expenseCategory?->name ?? 'N/A',
            'transfer' => 'Transfer',
            default => 'N/A',
        };
    }

    /**
     * Get a human-readable account display.
     */
    public function getAccountDisplayAttribute(): string
    {
        return match ($this->type) {
            'income', 'expense' => $this->account?->name ?? 'N/A',
            'transfer' => ($this->fromAccount?->name ?? '?') . ' → ' . ($this->toAccount?->name ?? '?'),
            default => 'N/A',
        };
    }

    /**
     * Get the type label with proper casing.
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Get CSS class for the transaction type.
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'income' => 'text-emerald-600',
            'expense' => 'text-red-600',
            'transfer' => 'text-blue-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Get background CSS class for the transaction type.
     */
    public function getTypeBgColorAttribute(): string
    {
        return match ($this->type) {
            'income' => 'bg-emerald-100 text-emerald-800',
            'expense' => 'bg-red-100 text-red-800',
            'transfer' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Query scopes

    public function scopeIncome($query)
    {
        return $query->where('transactions.type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('transactions.type', 'expense');
    }

    public function scopeTransfer($query)
    {
        return $query->where('transactions.type', 'transfer');
    }

    public function scopeDateBetween($query, $from, $to)
    {
        if ($from) {
            $query->where('transactions.date', '>=', $from);
        }
        if ($to) {
            $query->where('transactions.date', '<=', $to);
        }
        return $query;
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('transactions.user_id', $userId);
    }
}
