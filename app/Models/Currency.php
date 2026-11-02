<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'code',
    'name',
    'symbol',
    'exchange_rate',
    'is_base',
    'is_active',
    'source',
    'last_updated_at',
])]
class Currency extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:6',
            'is_base' => 'boolean',
            'is_active' => 'boolean',
            'last_updated_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }

    /**
     * Get the current active base currency.
     */
    public static function getBaseCurrency(): self
    {
        $baseId = Cache::remember('currency_base_id', 3600, function () {
            $b = self::where('is_base', true)->first()
                ?? self::where('code', Setting::get('currency_code', 'BDT'))->first()
                ?? self::first();
            return $b ? $b->id : null;
        });

        if ($baseId) {
            $base = self::find($baseId);
            if ($base) {
                return $base;
            }
        }

        return self::where('is_base', true)->first() ?? self::first() ?? new self([
            'code' => 'BDT',
            'name' => 'Bangladeshi Taka',
            'symbol' => '৳',
            'exchange_rate' => 1.0,
            'is_base' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Clear currency caches.
     */
    public static function clearCurrencyCache(): void
    {
        Cache::forget('currency_base');
        Cache::forget('currency_base_id');
        Cache::forget('active_currencies');
        Cache::forget('all_currencies_map');
        Cache::forget('currency_rates_map');
        Cache::forget('currency_symbols_map');
    }

    /**
     * Convert an amount from one currency to another.
     */
    public static function convert(float $amount, string $fromCode, string $toCode): float
    {
        if (strtoupper($fromCode) === strtoupper($toCode) || $amount == 0) {
            return round($amount, 2);
        }

        $rates = Cache::remember('currency_rates_map', 3600, function () {
            return self::pluck('exchange_rate', 'code')->mapWithKeys(function ($rate, $code) {
                return [strtoupper($code) => (float)$rate];
            })->all();
        });

        $fromRate = $rates[strtoupper($fromCode)] ?? null;
        $toRate = $rates[strtoupper($toCode)] ?? null;

        if ($fromRate === null || $toRate === null || $toRate == 0) {
            return round($amount, 2);
        }

        // Amount in Base = Amount * fromRate
        $baseVal = $amount * $fromRate;
        // Amount in Target = baseVal / toRate
        $targetVal = $baseVal / $toRate;

        return round($targetVal, 2);
    }

    /**
     * Convert an amount in a given currency directly into the Base currency.
     */
    public static function toBase(float $amount, string $currencyCode): float
    {
        $base = self::getBaseCurrency();
        return self::convert($amount, $currencyCode, $base->code);
    }

    /**
     * Get currency symbol by code.
     */
    public static function getSymbol(string $code): string
    {
        $symbols = Cache::remember('currency_symbols_map', 3600, function () {
            return self::pluck('symbol', 'code')->mapWithKeys(function ($sym, $c) {
                return [strtoupper($c) => $sym];
            })->all();
        });

        return $symbols[strtoupper($code)] ?? $code;
    }
}
