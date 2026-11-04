<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

trait Auditable
{
    protected static bool $auditDisabled = false;

    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            if (!static::shouldAudit()) {
                return;
            }

            $newValues = static::filterAuditAttributes($model->getAttributes(), $model->getAuditIgnoredFields());

            try {
                AuditLog::create([
                    'user_id' => $model->user_id ?? Auth::id(),
                    'auditable_type' => get_class($model),
                    'auditable_id' => $model->getKey(),
                    'entity_label' => $model->getAuditLabel(),
                    'action' => 'created',
                    'description' => 'Created ' . $model->getAuditLabel(),
                    'old_values' => null,
                    'new_values' => $newValues,
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Ensure audit failure does not break primary business logic
                report($e);
            }
        });

        static::updated(function (Model $model) {
            if (!static::shouldAudit()) {
                return;
            }

            $dirty = $model->getDirty();
            $ignored = $model->getAuditIgnoredFields();
            $changedKeys = array_diff(array_keys($dirty), $ignored);

            if (empty($changedKeys)) {
                return;
            }

            $oldValues = [];
            $newValues = [];

            foreach ($changedKeys as $key) {
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $model->getAttribute($key);
            }

            $fieldList = implode(', ', array_slice($changedKeys, 0, 3));
            if (count($changedKeys) > 3) {
                $fieldList .= ' +' . (count($changedKeys) - 3) . ' more';
            }

            try {
                AuditLog::create([
                    'user_id' => $model->user_id ?? Auth::id(),
                    'auditable_type' => get_class($model),
                    'auditable_id' => $model->getKey(),
                    'entity_label' => $model->getAuditLabel(),
                    'action' => 'updated',
                    'description' => 'Updated ' . $model->getAuditLabel() . ' (' . $fieldList . ')',
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        static::deleted(function (Model $model) {
            if (!static::shouldAudit()) {
                return;
            }

            $oldValues = static::filterAuditAttributes($model->getOriginal() ?: $model->getAttributes(), $model->getAuditIgnoredFields());

            try {
                AuditLog::create([
                    'user_id' => $model->user_id ?? Auth::id(),
                    'auditable_type' => get_class($model),
                    'auditable_id' => $model->getKey(),
                    'entity_label' => $model->getAuditLabel(),
                    'action' => 'deleted',
                    'description' => 'Deleted ' . $model->getAuditLabel(),
                    'old_values' => $oldValues,
                    'new_values' => null,
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }

    public static function withoutAuditing(callable $callback): mixed
    {
        static::$auditDisabled = true;
        try {
            return $callback();
        } finally {
            static::$auditDisabled = false;
        }
    }

    public static function shouldAudit(): bool
    {
        return !static::$auditDisabled;
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->orderByDesc('created_at');
    }

    public function getAuditLabel(): string
    {
        if (isset($this->invoice_number)) {
            return 'Invoice #' . $this->invoice_number . ($this->client_name ? " ({$this->client_name})" : '');
        }

        if (isset($this->name)) {
            return class_basename($this) . ': ' . $this->name;
        }

        if (isset($this->description) && !empty($this->description)) {
            $desc = Str::limit($this->description, 28);
            $amount = isset($this->amount) ? ' ' . number_format((float)$this->amount, 2) : '';
            return class_basename($this) . " #{$this->getKey()} ({$desc}{$amount})";
        }

        return class_basename($this) . ' #' . $this->getKey();
    }

    public function getAuditIgnoredFields(): array
    {
        return [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token',
            'password',
        ];
    }

    protected static function filterAuditAttributes(array $attributes, array $ignored): array
    {
        return array_diff_key($attributes, array_flip($ignored));
    }
}

