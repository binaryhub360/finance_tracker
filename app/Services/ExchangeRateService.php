<?php

namespace App\Services;

use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Fetch and update exchange rates for all active currencies relative to the Base currency.
     */
    public function updateRates(): array
    {
        $baseCurrency = Currency::getBaseCurrency();
        $baseCode = strtoupper($baseCurrency->code ?? 'BDT');
        $activeCurrencies = Currency::where('is_active', true)->get();

        $ratesFromApi = [];
        $source = 'open.er-api.com';

        try {
            // Fetch rates anchored on USD for universal currency support
            $response = Http::timeout(15)->get("https://open.er-api.com/v6/latest/USD");

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates']) && is_array($data['rates'])) {
                    $ratesFromApi = $data['rates'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Primary exchange rate API failed: " . $e->getMessage());
        }

        // Fallback to Frankfurter if empty
        if (empty($ratesFromApi)) {
            try {
                $fallback = Http::timeout(15)->get("https://api.frankfurter.app/latest?from=USD");
                if ($fallback->successful()) {
                    $fbData = $fallback->json();
                    if (isset($fbData['rates']) && is_array($fbData['rates'])) {
                        $ratesFromApi = $fbData['rates'];
                        $ratesFromApi['USD'] = 1.0;
                        $source = 'frankfurter.app';
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Fallback exchange rate API failed: " . $e->getMessage());
            }
        }

        if (empty($ratesFromApi)) {
            return [
                'success' => false,
                'message' => 'Unable to connect to live exchange rate services at this time. Existing rates preserved.',
                'updated_count' => 0,
            ];
        }

        // If Base is USD:
        // rate for X is simply ratesFromApi[Base] / ratesFromApi[X]
        // Example: Base = BDT, 1 USD = 122.50 BDT (ratesFromApi['BDT'] = 122.5)
        // 1 EUR = (122.5 / ratesFromApi['EUR']) BDT
        $usdInBase = (float) ($ratesFromApi[$baseCode] ?? 1.0);
        if ($usdInBase <= 0) {
            $usdInBase = 1.0;
        }

        $now = Carbon::now();
        $updatedCount = 0;

        foreach ($activeCurrencies as $currency) {
            $code = strtoupper($currency->code);

            if ($currency->is_base || $code === $baseCode) {
                $currency->exchange_rate = 1.000000;
                $currency->source = 'base';
                $currency->last_updated_at = $now;
                $currency->save();
                continue;
            }

            if (isset($ratesFromApi[$code])) {
                $usdToTarget = (float) $ratesFromApi[$code];

                if ($usdToTarget > 0) {
                    // How many Base currency units 1 unit of $code buys:
                    // 1 unit of $code = (1 / $usdToTarget) USD = ($usdInBase / $usdToTarget) Base
                    $rateInBase = $usdInBase / $usdToTarget;

                    $currency->exchange_rate = round($rateInBase, 6);
                    $currency->source = $source;
                    $currency->last_updated_at = $now;
                    $currency->save();
                    $updatedCount++;
                }
            }
        }

        Currency::clearCurrencyCache();

        return [
            'success' => true,
            'message' => "Successfully synchronized {$updatedCount} currency exchange rates relative to {$baseCode}.",
            'updated_count' => $updatedCount,
            'source' => $source,
            'timestamp' => $now->toDateTimeString(),
        ];
    }
}
