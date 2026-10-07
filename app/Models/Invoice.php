<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'status',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total',
        'paid_amount',
        'currency',
        'notes',
        'terms',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->total;
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, round((float)$this->total - (float)$this->paid_amount, 2));
    }

    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['paid', 'cancelled'])) {
            return false;
        }

        return $this->due_date && $this->due_date->isPast() && !$this->due_date->isToday();
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_overdue && $this->status !== 'paid') {
            return 'Overdue';
        }

        return match ($this->status) {
            'draft' => 'Draft',
            'sent' => 'Sent',
            'partially_paid' => 'Partially Paid',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        if ($this->is_overdue && $this->status !== 'paid') {
            return 'bg-rose-500/10 text-rose-600 border border-rose-500/20';
        }

        return match ($this->status) {
            'draft' => 'bg-slate-500/10 text-slate-600 border border-slate-500/20',
            'sent' => 'bg-blue-500/10 text-blue-600 border border-blue-500/20',
            'partially_paid' => 'bg-amber-500/10 text-amber-600 border border-amber-500/20',
            'paid' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20',
            'cancelled' => 'bg-gray-500/10 text-gray-500 border border-gray-500/20',
            default => 'bg-slate-500/10 text-slate-600 border border-slate-500/20',
        };
    }

    public function recalculateTotals(): void
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += (float)$item->total;
        }

        $taxRate = (float)$this->tax_rate;
        $taxAmount = round(($subtotal * $taxRate) / 100, 2);
        $discountAmount = (float)$this->discount_amount;
        $total = max(0, round($subtotal + $taxAmount - $discountAmount, 2));
        $paidAmount = (float)$this->payments()->sum('amount');

        $status = $this->status;
        if ($status !== 'cancelled' && $status !== 'draft') {
            if ($paidAmount >= $total && $total > 0) {
                $status = 'paid';
            } elseif ($paidAmount > 0) {
                $status = 'partially_paid';
            } elseif ($this->is_overdue) {
                $status = 'overdue';
            } else {
                $status = 'sent';
            }
        }

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'paid_amount' => $paidAmount,
            'status' => $status,
        ]);
    }

    public static function generateNextInvoiceNumber(int $userId): string
    {
        $year = date('Y');
        $prefix = "INV-{$year}-";

        $lastInvoice = self::where('user_id', $userId)
            ->where('invoice_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
