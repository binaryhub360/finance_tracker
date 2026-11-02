<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Setting;
use App\Services\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function index(Request $request): View
    {
        $baseCurrency = Currency::getBaseCurrency();
        $currencies = Currency::orderBy('is_base', 'desc')
            ->orderBy('is_active', 'desc')
            ->orderBy('code', 'asc')
            ->get();

        $activeCurrencies = $currencies->where('is_active', true);
        $lastSync = Currency::whereNotNull('last_updated_at')->max('last_updated_at');

        // Initial default converter values
        $convertAmount = (float) $request->get('amount', 100);
        $convertFrom = strtoupper($request->get('from', 'USD'));
        $convertTo = strtoupper($request->get('to', $baseCurrency->code ?? 'BDT'));
        $convertedResult = Currency::convert($convertAmount, $convertFrom, $convertTo);

        return view('currencies.index', compact(
            'currencies',
            'baseCurrency',
            'activeCurrencies',
            'lastSync',
            'convertAmount',
            'convertFrom',
            'convertTo',
            'convertedResult'
        ));
    }

    /**
     * Trigger live exchange rate sync from external APIs.
     */
    public function syncRates(ExchangeRateService $service): RedirectResponse
    {
        $result = $service->updateRates();

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('warning', $result['message']);
        }
    }

    /**
     * AJAX endpoint for live currency conversion calculations.
     */
    public function convert(Request $request): JsonResponse
    {
        $amount = (float) $request->get('amount', 1);
        $from = strtoupper($request->get('from', 'USD'));
        $to = strtoupper($request->get('to', 'BDT'));

        $converted = Currency::convert($amount, $from, $to);
        $singleUnitRate = Currency::convert(1, $from, $to);

        return response()->json([
            'amount' => $amount,
            'from' => $from,
            'from_symbol' => Currency::getSymbol($from),
            'to' => $to,
            'to_symbol' => Currency::getSymbol($to),
            'converted' => $converted,
            'unit_rate' => $singleUnitRate,
            'formatted_result' => format_currency($converted, $to),
        ]);
    }

    /**
     * Set a currency as the system base currency.
     */
    public function setBase(Currency $currency): RedirectResponse
    {
        if ($currency->is_base) {
            return redirect()->back()->with('info', "{$currency->code} is already the base currency.");
        }

        $oldBase = Currency::where('is_base', true)->first();
        $oldRate = (float) $currency->exchange_rate;

        if ($oldRate <= 0) {
            return redirect()->back()->with('error', "Cannot set {$currency->code} as base because its rate is 0.");
        }

        // Recalculate all other currencies relative to the new base
        // New rate for X = current rate for X / oldRate
        foreach (Currency::all() as $c) {
            if ($c->id === $currency->id) {
                $c->exchange_rate = 1.000000;
                $c->is_base = true;
                $c->is_active = true;
            } else {
                $newRate = (float) $c->exchange_rate / $oldRate;
                $c->exchange_rate = round($newRate, 6);
                $c->is_base = false;
            }
            $c->last_updated_at = Carbon::now();
            $c->save();
        }

        // Update settings table
        Setting::set('currency_code', $currency->code);
        Setting::set('currency_symbol', $currency->symbol);

        Currency::clearCurrencyCache();

        return redirect()->back()->with('success', "Base currency updated to {$currency->name} ({$currency->code}). All rates re-indexed.");
    }

    /**
     * Update an existing currency rate or toggle active status.
     */
    public function update(Request $request, Currency $currency): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'is_active' => 'nullable|boolean',
        ]);

        if ($currency->is_base) {
            $validated['exchange_rate'] = 1.000000;
            $validated['is_active'] = true;
        }

        $currency->update([
            'name' => $validated['name'],
            'symbol' => $validated['symbol'],
            'exchange_rate' => $validated['exchange_rate'],
            'is_active' => $request->boolean('is_active'),
            'source' => 'manual',
            'last_updated_at' => Carbon::now(),
        ]);

        Currency::clearCurrencyCache();

        return redirect()->back()->with('success', "Currency {$currency->code} updated successfully.");
    }

    /**
     * Add a new currency.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
        ]);

        Currency::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'symbol' => $validated['symbol'],
            'exchange_rate' => $validated['exchange_rate'],
            'is_base' => false,
            'is_active' => true,
            'source' => 'manual',
            'last_updated_at' => Carbon::now(),
        ]);

        Currency::clearCurrencyCache();

        return redirect()->back()->with('success', "Added currency {$validated['code']} successfully.");
    }
}
