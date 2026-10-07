<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expense_category_id',
        'amount',
        'period',
        'start_date',
        'end_date',
        'alert_threshold',
        'notify_overrun',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'alert_threshold' => 'integer',
        'notify_overrun' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function getSpentAmountAttribute(): float
    {
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('type', 'expense')
            ->where('expense_category_id', $this->expense_category_id)
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, round((float) $this->amount - $this->spent_amount, 2));
    }

    public function getOverrunAmountAttribute(): float
    {
        return max(0, round($this->spent_amount - (float) $this->amount, 2));
    }

    public function getSpentPercentageAttribute(): float
    {
        $budgeted = (float) $this->amount;
        if ($budgeted <= 0) {
            return 0;
        }

        return round(($this->spent_amount / $budgeted) * 100, 1);
    }

    public function getProgressWidthAttribute(): float
    {
        return min(100, $this->spent_percentage);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->spent_amount > (float) $this->amount;
    }

    public function getIsNearLimitAttribute(): bool
    {
        return !$this->is_over_budget && ($this->spent_percentage >= $this->alert_threshold);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_over_budget) {
            return 'Over Budget';
        }
        if ($this->is_near_limit) {
            return 'Near Limit (' . $this->alert_threshold . '%)';
        }
        return 'On Track';
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        if ($this->is_over_budget) {
            return 'bg-rose-500/10 text-rose-600 border border-rose-500/20';
        }
        if ($this->is_near_limit) {
            return 'bg-amber-500/10 text-amber-600 border border-amber-500/20';
        }
        return 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20';
    }

    public function getProgressBarClassesAttribute(): string
    {
        if ($this->is_over_budget) {
            return 'bg-gradient-to-r from-rose-500 to-red-600 shadow-sm shadow-rose-500/30';
        }
        if ($this->is_near_limit) {
            return 'bg-gradient-to-r from-amber-500 to-amber-600 shadow-sm shadow-amber-500/30';
        }
        return 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-sm shadow-emerald-500/30';
    }
}
