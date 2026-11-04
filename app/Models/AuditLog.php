<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

#[Fillable([
    'user_id',
    'auditable_type',
    'auditable_id',
    'entity_label',
    'action',
    'description',
    'old_values',
    'new_values',
    'ip_address',
    'user_agent',
    'created_at',
])]
class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->created_at = $log->created_at ?? now();
            if (empty($log->ip_address)) {
                $log->ip_address = Request::ip();
            }
            if (empty($log->user_agent)) {
                $log->user_agent = Request::userAgent();
            }
            if (empty($log->user_id) && Auth::check()) {
                $log->user_id = Auth::id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Manually record an audit log event.
     */
    public static function record(
        string $action,
        string $description,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): self {
        $entityLabel = null;
        if ($auditable) {
            $entityLabel = method_exists($auditable, 'getAuditLabel')
                ? $auditable->getAuditLabel()
                : class_basename($auditable) . ' #' . ($auditable->id ?? '');
        }

        return self::create([
            'user_id' => $userId ?? Auth::id(),
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'entity_label' => $entityLabel,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Get clean short name of the auditable entity.
     */
    public function getEntityTypeNameAttribute(): string
    {
        if (!$this->auditable_type) {
            return 'General';
        }

        return match (class_basename($this->auditable_type)) {
            'Transaction' => 'Transaction',
            'Account' => 'Account',
            'Invoice' => 'Invoice',
            'InvoicePayment' => 'Invoice Payment',
            'Budget' => 'Budget',
            'Goal' => 'Savings Goal',
            'GoalContribution' => 'Goal Contribution',
            'RecurringTransaction' => 'Subscription Rule',
            'Currency' => 'Currency',
            'Setting' => 'Setting',
            'ImportBatch' => 'Import Batch',
            default => class_basename($this->auditable_type),
        };
    }

    /**
     * Get styling badge for the action type.
     */
    public function getActionBadgeAttribute(): array
    {
        return match ($this->action) {
            'created' => [
                'label' => 'Created',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                'dot' => 'bg-emerald-500',
            ],
            'updated' => [
                'label' => 'Updated',
                'class' => 'bg-blue-50 text-blue-700 border-blue-200/60',
                'dot' => 'bg-blue-500',
            ],
            'deleted' => [
                'label' => 'Deleted',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200/60',
                'dot' => 'bg-rose-500',
            ],
            'status_changed' => [
                'label' => 'Status Changed',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                'dot' => 'bg-amber-500',
            ],
            'rolled_back' => [
                'label' => 'Rolled Back',
                'class' => 'bg-purple-50 text-purple-700 border-purple-200/60',
                'dot' => 'bg-purple-500',
            ],
            'payment_recorded' => [
                'label' => 'Payment Logged',
                'class' => 'bg-teal-50 text-teal-700 border-teal-200/60',
                'dot' => 'bg-teal-500',
            ],
            default => [
                'label' => ucfirst(str_replace('_', ' ', $this->action)),
                'class' => 'bg-slate-50 text-slate-700 border-slate-200',
                'dot' => 'bg-slate-500',
            ],
        };
    }

    /**
     * Compute field-level diff between old and new values.
     */
    public function getDiffAttribute(): array
    {
        $diff = [];
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];

        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($allKeys as $key) {
            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            if ($oldVal !== $newVal) {
                $diff[$key] = [
                    'field' => ucwords(str_replace('_', ' ', $key)),
                    'old' => is_bool($oldVal) ? ($oldVal ? 'true' : 'false') : (is_array($oldVal) ? json_encode($oldVal) : (string)$oldVal),
                    'new' => is_bool($newVal) ? ($newVal ? 'true' : 'false') : (is_array($newVal) ? json_encode($newVal) : (string)$newVal),
                ];
            }
        }

        return $diff;
    }
}

