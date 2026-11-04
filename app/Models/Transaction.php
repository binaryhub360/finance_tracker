<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'type', 'date', 'amount',
    'currency', 'exchange_rate', 'base_amount',
    'account_id', 'income_category_id', 'expense_category_id',
    'recurring_transaction_id', 'goal_id', 'import_batch_id',
    'from_account_id', 'to_account_id',
    'description', 'reference',
    'is_tax_deductible', 'tax_category', 'deduction_percentage',
    'tax_deductible_amount', 'tax_notes',
])]
class Transaction extends Model
{
    use HasFactory, Auditable;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'base_amount' => 'decimal:2',
            'is_tax_deductible' => 'boolean',
            'deduction_percentage' => 'decimal:2',
            'tax_deductible_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Transaction $transaction) {
            if (!$transaction->currency) {
                // If account is linked, default to account currency
                if ($transaction->account_id && $transaction->account) {
                    $transaction->currency = $transaction->account->currency ?? 'BDT';
                } else {
                    $transaction->currency = 'BDT';
                }
            }

            if (!$transaction->exchange_rate || (float) $transaction->exchange_rate <= 0) {
                $c = Currency::where('code', $transaction->currency)->first();
                $transaction->exchange_rate = $c ? (float) $c->exchange_rate : 1.000000;
            }

            if ($transaction->base_amount === null || (float) $transaction->base_amount == 0) {
                $transaction->base_amount = round((float) $transaction->amount * (float) $transaction->exchange_rate, 2);
            }

            // Tax Deduction calculations
            if ($transaction->type === 'expense' && $transaction->is_tax_deductible) {
                $percentage = (float) ($transaction->deduction_percentage ?? 100.00);
                if ($percentage <= 0 || $percentage > 100) {
                    $percentage = 100.00;
                }
                $transaction->deduction_percentage = $percentage;

                $base = (float) $transaction->base_amount;
                $transaction->tax_deductible_amount = round($base * ($percentage / 100), 2);
            } else {
                $transaction->is_tax_deductible = false;
                $transaction->tax_deductible_amount = 0.00;
            }
        });
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

    public function recurringTransaction(): BelongsTo
    {
        return $this->belongsTo(RecurringTransaction::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
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

    public function scopeTaxDeductible($query)
    {
        return $query->where('transactions.is_tax_deductible', true);
    }

    public function scopeFiscalYear($query, $start, $end)
    {
        return $query->whereBetween('transactions.date', [$start, $end]);
    }

    /**
     * Get tax category definition from TaxService.
     */
    public function getTaxCategoryDefinitionAttribute(): ?array
    {
        return \App\Services\TaxService::getCategory($this->tax_category);
    }

    /**
     * Get human-friendly tax category name.
     */
    public function getTaxCategoryLabelAttribute(): ?string
    {
        $def = $this->tax_category_definition;
        return $def ? $def['name'] : ($this->tax_category ? ucwords(str_replace('_', ' ', $this->tax_category)) : null);
    }
}
