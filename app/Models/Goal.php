<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'user_id',
    'name',
    'description',
    'target_amount',
    'current_amount',
    'currency',
    'start_date',
    'target_date',
    'account_id',
    'color',
    'icon',
    'status',
    'is_locked',
    'notes',
])]
class Goal extends Model
{
    use HasFactory, Auditable;

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'start_date' => 'date',
            'target_date' => 'date',
            'is_locked' => 'boolean',
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

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class)->orderBy('date', 'desc')->orderBy('id', 'desc');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    public function getProgressPercentageAttribute(): float
    {
        if ((float) $this->target_amount <= 0) {
            return 0.0;
        }

        $percentage = ((float) $this->current_amount / (float) $this->target_amount) * 100;
        return round(min(100.0, max(0.0, $percentage)), 1);
    }

    public function getRemainingAmountAttribute(): float
    {
        $remaining = (float) $this->target_amount - (float) $this->current_amount;
        return (float) max(0.0, round($remaining, 2));
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed' || ((float) $this->current_amount >= (float) $this->target_amount && (float) $this->target_amount > 0);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->target_date) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->target_date->startOfDay(), false);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->is_completed || !$this->target_date) {
            return false;
        }

        return $this->target_date->startOfDay()->lt(Carbon::today());
    }

    public function getMonthsRemainingAttribute(): ?int
    {
        if (!$this->target_date) {
            return null;
        }

        $days = (int) Carbon::today()->diffInDays($this->target_date->startOfDay(), false);
        if ($days <= 0) {
            return 0;
        }

        return (int) max(1, ceil($days / 30.4));
    }

    public function getRecommendedMonthlyContributionAttribute(): ?float
    {
        $months = $this->months_remaining;
        if ($months === null || $months <= 0 || $this->remaining_amount <= 0) {
            return null;
        }

        return round($this->remaining_amount / $months, 2);
    }

    public function getColorThemeAttribute(): array
    {
        $color = strtolower($this->color ?? 'emerald');

        $themes = [
            'emerald' => [
                'name' => 'emerald',
                'bg_subtle' => 'bg-emerald-50/70',
                'border' => 'border-emerald-200/80',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
                'bar' => 'bg-emerald-500',
                'gradient' => 'from-emerald-500 to-teal-500',
                'text' => 'text-emerald-600',
                'glow' => 'shadow-emerald-500/20',
            ],
            'indigo' => [
                'name' => 'indigo',
                'bg_subtle' => 'bg-indigo-50/70',
                'border' => 'border-indigo-200/80',
                'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
                'bar' => 'bg-indigo-500',
                'gradient' => 'from-indigo-500 to-violet-500',
                'text' => 'text-indigo-600',
                'glow' => 'shadow-indigo-500/20',
            ],
            'sky' => [
                'name' => 'sky',
                'bg_subtle' => 'bg-sky-50/70',
                'border' => 'border-sky-200/80',
                'badge' => 'bg-sky-50 text-sky-700 border-sky-200/70',
                'bar' => 'bg-sky-500',
                'gradient' => 'from-sky-500 to-cyan-500',
                'text' => 'text-sky-600',
                'glow' => 'shadow-sky-500/20',
            ],
            'amber' => [
                'name' => 'amber',
                'bg_subtle' => 'bg-amber-50/70',
                'border' => 'border-amber-200/80',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200/70',
                'bar' => 'bg-amber-500',
                'gradient' => 'from-amber-500 to-orange-500',
                'text' => 'text-amber-600',
                'glow' => 'shadow-amber-500/20',
            ],
            'purple' => [
                'name' => 'purple',
                'bg_subtle' => 'bg-purple-50/70',
                'border' => 'border-purple-200/80',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200/70',
                'bar' => 'bg-purple-500',
                'gradient' => 'from-purple-500 to-pink-500',
                'text' => 'text-purple-600',
                'glow' => 'shadow-purple-500/20',
            ],
            'rose' => [
                'name' => 'rose',
                'bg_subtle' => 'bg-rose-50/70',
                'border' => 'border-rose-200/80',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200/70',
                'bar' => 'bg-rose-500',
                'gradient' => 'from-rose-500 to-red-500',
                'text' => 'text-rose-600',
                'glow' => 'shadow-rose-500/20',
            ],
        ];

        return $themes[$color] ?? $themes['emerald'];
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'completed' => [
                'label' => 'Goal Reached',
                'classes' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                'dot' => 'bg-emerald-500',
            ],
            'paused' => [
                'label' => 'Paused',
                'classes' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                'dot' => 'bg-amber-500',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'classes' => 'bg-slate-100 text-slate-600 border-slate-200',
                'dot' => 'bg-slate-400',
            ],
            default => [
                'label' => 'Active Vault',
                'classes' => 'bg-teal-50 text-teal-700 border-teal-200/60',
                'dot' => 'bg-teal-500',
            ],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Actions / Ledger Recalculation
    |--------------------------------------------------------------------------
    */

    public function recalculateCurrentAmount(): self
    {
        $deposits = $this->contributions()->where('type', 'deposit')->sum('amount');
        $withdrawals = $this->contributions()->where('type', 'withdraw')->sum('amount');
        $net = max(0.0, (float) $deposits - (float) $withdrawals);

        $this->current_amount = $net;

        if ($net >= (float) $this->target_amount && (float) $this->target_amount > 0 && $this->status === 'active') {
            $this->status = 'completed';
        }

        $this->save();

        return $this;
    }

    public function addContribution(string $type, float $amount, string $date, ?int $accountId = null, ?string $notes = null, bool $createTransaction = false): GoalContribution
    {
        return DB::transaction(function () use ($type, $amount, $date, $accountId, $notes, $createTransaction) {
            $transactionId = null;

            if ($createTransaction && $accountId) {
                $txType = $type === 'deposit' ? 'expense' : 'income';
                $description = $type === 'deposit'
                    ? "Savings Vault Deposit: {$this->name}"
                    : "Savings Vault Withdrawal: {$this->name}";

                $transaction = Transaction::create([
                    'user_id' => $this->user_id,
                    'account_id' => $accountId,
                    'goal_id' => $this->id,
                    'type' => $txType,
                    'amount' => $amount,
                    'date' => $date,
                    'description' => $description,
                    'reference' => 'VAULT-' . $this->id,
                ]);

                $transactionId = $transaction->id;
            }

            $contribution = $this->contributions()->create([
                'user_id' => $this->user_id,
                'type' => $type,
                'amount' => $amount,
                'date' => $date,
                'account_id' => $accountId,
                'transaction_id' => $transactionId,
                'notes' => $notes,
            ]);

            $this->recalculateCurrentAmount();

            return $contribution;
        });
    }

    public function getAuditLabel(): string
    {
        return "Savings Goal: {$this->name} (" . ($this->currency ?? 'BDT') . ' ' . number_format((float)$this->target_amount, 2) . ')';
    }
}
