<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'amount',
        'currency',
        'account_id',
        'expense_category_id',
        'income_category_id',
        'frequency',
        'interval',
        'start_date',
        'end_date',
        'next_due_date',
        'last_processed_date',
        'status',
        'auto_log',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interval' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
        'last_processed_date' => 'date',
        'auto_log' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function incomeCategory(): BelongsTo
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'active')
            ->where('next_due_date', '<=', Carbon::today()->format('Y-m-d'));
    }

    public function getCategoryNameAttribute(): string
    {
        if ($this->type === 'expense') {
            return $this->expenseCategory?->name ?? 'Uncategorized';
        }

        return $this->incomeCategory?->name ?? 'Uncategorized';
    }

    public function getFrequencyLabelAttribute(): string
    {
        $interval = max(1, $this->interval);

        if ($interval === 1) {
            return match ($this->frequency) {
                'daily' => 'Daily',
                'weekly' => 'Weekly',
                'monthly' => 'Monthly',
                'quarterly' => 'Quarterly',
                'yearly' => 'Yearly',
                default => ucfirst($this->frequency),
            };
        }

        $unit = match ($this->frequency) {
            'daily' => 'Days',
            'weekly' => 'Weeks',
            'monthly' => 'Months',
            'quarterly' => 'Quarters',
            'yearly' => 'Years',
            default => $this->frequency,
        };

        return "Every {$interval} {$unit}";
    }

    public function getMonthlyEquivalentAmountAttribute(): float
    {
        $amount = (float) $this->amount;
        $interval = max(1, $this->interval);

        $equiv = match ($this->frequency) {
            'daily' => $amount * (30 / $interval),
            'weekly' => ($amount * 52 / 12) / $interval,
            'monthly' => $amount / $interval,
            'quarterly' => ($amount / 3) / $interval,
            'yearly' => ($amount / 12) / $interval,
            default => $amount / $interval,
        };

        return round($equiv, 2);
    }

    public function getYearlyEquivalentAmountAttribute(): float
    {
        return round($this->monthly_equivalent_amount * 12, 2);
    }

    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->next_due_date) {
            return 0;
        }

        $today = Carbon::today();
        $due = Carbon::parse($this->next_due_date)->startOfDay();

        return (int) $today->diffInDays($due, false);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status !== 'active' || !$this->next_due_date) {
            return false;
        }

        return Carbon::today()->gt(Carbon::parse($this->next_due_date)->startOfDay());
    }

    public function getIsDueTodayAttribute(): bool
    {
        if ($this->status !== 'active' || !$this->next_due_date) {
            return false;
        }

        return Carbon::today()->isSameDay(Carbon::parse($this->next_due_date));
    }

    public function getIsDueSoonAttribute(): bool
    {
        if ($this->status !== 'active' || $this->is_overdue) {
            return false;
        }

        return $this->days_until_due >= 0 && $this->days_until_due <= 7;
    }

    public function getDueBadgeLabelAttribute(): string
    {
        if ($this->status !== 'active') {
            return ucfirst($this->status);
        }

        if ($this->is_overdue) {
            $days = abs($this->days_until_due);
            return "Overdue by {$days} " . ($days === 1 ? 'day' : 'days');
        }

        if ($this->is_due_today) {
            return 'Due Today';
        }

        if ($this->days_until_due === 1) {
            return 'Due Tomorrow';
        }

        if ($this->days_until_due <= 7) {
            return "Due in {$this->days_until_due} days";
        }

        return 'Renews ' . $this->next_due_date->format('M d');
    }

    public function getDueBadgeClassesAttribute(): string
    {
        if ($this->status === 'paused') {
            return 'bg-amber-500/10 text-amber-700 border border-amber-500/20';
        }

        if ($this->status === 'cancelled') {
            return 'bg-slate-500/10 text-slate-500 border border-slate-500/20';
        }

        if ($this->is_overdue) {
            return 'bg-rose-500/10 text-rose-700 border border-rose-500/20 animate-pulse';
        }

        if ($this->is_due_today) {
            return 'bg-amber-500/15 text-amber-800 border border-amber-500/30 font-bold';
        }

        if ($this->is_due_soon) {
            return 'bg-amber-500/10 text-amber-700 border border-amber-500/20 font-medium';
        }

        return 'bg-slate-100 text-slate-700 border border-slate-200/80';
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-emerald-500/10 text-emerald-700 border border-emerald-500/20',
            'paused' => 'bg-amber-500/10 text-amber-700 border border-amber-500/20',
            'cancelled' => 'bg-slate-500/10 text-slate-600 border border-slate-500/20',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        };
    }

    public function computeNextDueDate(?Carbon $from = null): Carbon
    {
        $base = $from ? $from->copy()->startOfDay() : Carbon::parse($this->next_due_date)->startOfDay();
        $interval = max(1, $this->interval);

        return match ($this->frequency) {
            'daily' => $base->addDays($interval),
            'weekly' => $base->addWeeks($interval),
            'monthly' => $base->addMonthsNoOverflow($interval),
            'quarterly' => $base->addMonthsNoOverflow($interval * 3),
            'yearly' => $base->addYearsNoOverflow($interval),
            default => $base->addMonthsNoOverflow($interval),
        };
    }

    public function logOccurrence(?string $date = null): Transaction
    {
        return DB::transaction(function () use ($date) {
            $occurrenceDate = $date ?: $this->next_due_date->format('Y-m-d');

            $transaction = Transaction::create([
                'user_id' => $this->user_id,
                'type' => $this->type,
                'date' => $occurrenceDate,
                'amount' => $this->amount,
                'account_id' => $this->account_id,
                'expense_category_id' => $this->type === 'expense' ? $this->expense_category_id : null,
                'income_category_id' => $this->type === 'income' ? $this->income_category_id : null,
                'recurring_transaction_id' => $this->id,
                'description' => "Recurring " . ucfirst($this->type) . ": {$this->name}",
                'reference' => 'REC-' . str_pad((string)$this->id, 5, '0', STR_PAD_LEFT),
            ]);

            $nextDue = $this->computeNextDueDate(Carbon::parse($occurrenceDate));

            $updates = [
                'last_processed_date' => $occurrenceDate,
                'next_due_date' => $nextDue->format('Y-m-d'),
            ];

            if ($this->end_date && $nextDue->gt(Carbon::parse($this->end_date))) {
                $updates['status'] = 'cancelled';
            }

            $this->update($updates);

            return $transaction;
        });
    }

    public function skipNextDueDate(): void
    {
        $nextDue = $this->computeNextDueDate();

        $updates = [
            'next_due_date' => $nextDue->format('Y-m-d'),
        ];

        if ($this->end_date && $nextDue->gt(Carbon::parse($this->end_date))) {
            $updates['status'] = 'cancelled';
        }

        $this->update($updates);
    }
}
