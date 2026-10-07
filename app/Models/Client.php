<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'tax_number',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalBilledAttribute(): float
    {
        return (float) $this->invoices()->where('status', '!=', 'cancelled')->sum('total');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->invoices()->where('status', '!=', 'cancelled')->sum('paid_amount');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return max(0, $this->total_billed - $this->total_paid);
    }
}
