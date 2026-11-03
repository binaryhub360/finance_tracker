<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'user_id',
    'account_id',
    'filename',
    'file_type',
    'status',
    'total_rows',
    'imported_count',
    'skipped_count',
    'error_count',
    'error_log',
])]
class ImportBatch extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'imported_count' => 'integer',
            'skipped_count' => 'integer',
            'error_count' => 'integer',
            'error_log' => 'array',
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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Rollback the entire batch by deleting all transactions imported under this batch.
     */
    public function rollback(): int
    {
        return DB::transaction(function () {
            $count = $this->transactions()->count();
            $this->transactions()->delete();

            $this->status = 'rolled_back';
            $this->save();

            return $count;
        });
    }

    /**
     * Get user-friendly status badge styling.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'completed' => [
                'label' => 'Completed',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
            ],
            'rolled_back' => [
                'label' => 'Rolled Back',
                'class' => 'bg-slate-100 text-slate-600 border-slate-200',
            ],
            'failed' => [
                'label' => 'Failed',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200/60',
            ],
            default => [
                'label' => ucfirst($this->status),
                'class' => 'bg-slate-50 text-slate-700 border-slate-200',
            ],
        };
    }
}
