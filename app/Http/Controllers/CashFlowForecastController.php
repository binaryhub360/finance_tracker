<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\CashFlowForecastService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CashFlowForecastController extends Controller
{
    /**
     * Display the Cash Flow Forecast & Runway Analytics Dashboard.
     */
    public function index(Request $request, CashFlowForecastService $service): View
    {
        $userId = $request->user()->id;

        $horizon = (int) $request->input('horizon', 90);
        if (!in_array($horizon, [30, 60, 90, 180])) {
            $horizon = 90;
        }

        $scenario = $request->input('scenario', 'baseline');
        if (!in_array($scenario, ['baseline', 'conservative', 'optimistic'])) {
            $scenario = 'baseline';
        }

        $lookback = (int) $request->input('lookback', 3);
        if (!in_array($lookback, [1, 3, 6, 12])) {
            $lookback = 3;
        }

        $burnRate = $service->getBurnRateAnalytics($userId, $lookback);
        $forecast = $service->generateForecast($userId, $horizon, $scenario);
        $comparison = $service->getScenarioComparison($userId, $horizon);
        $baseCurrency = Currency::getBaseCurrency();

        return view('cash-flow.index', compact(
            'burnRate',
            'forecast',
            'comparison',
            'horizon',
            'scenario',
            'lookback',
            'baseCurrency'
        ));
    }

    /**
     * AJAX endpoint returning JSON forecast data for dynamic scenario/horizon changes.
     */
    public function data(Request $request, CashFlowForecastService $service): JsonResponse
    {
        $userId = $request->user()->id;

        $horizon = (int) $request->input('horizon', 90);
        if (!in_array($horizon, [30, 60, 90, 180])) {
            $horizon = 90;
        }

        $scenario = $request->input('scenario', 'baseline');
        if (!in_array($scenario, ['baseline', 'conservative', 'optimistic'])) {
            $scenario = 'baseline';
        }

        $lookback = (int) $request->input('lookback', 3);
        if (!in_array($lookback, [1, 3, 6, 12])) {
            $lookback = 3;
        }

        $burnRate = $service->getBurnRateAnalytics($userId, $lookback);
        $forecast = $service->generateForecast($userId, $horizon, $scenario);
        $comparison = $service->getScenarioComparison($userId, $horizon);

        return response()->json([
            'success' => true,
            'burn_rate' => $burnRate,
            'forecast' => $forecast,
            'comparison' => $comparison,
        ]);
    }

    /**
     * Export the cash flow projection schedule to a CSV file.
     */
    public function exportCsv(Request $request, CashFlowForecastService $service): Response
    {
        $userId = $request->user()->id;

        $horizon = (int) $request->input('horizon', 90);
        $scenario = $request->input('scenario', 'baseline');
        $lookback = (int) $request->input('lookback', 3);

        $burnRate = $service->getBurnRateAnalytics($userId, $lookback);
        $forecast = $service->generateForecast($userId, $horizon, $scenario);
        $baseCurrency = Currency::getBaseCurrency();
        $currCode = $baseCurrency->code ?? 'BDT';

        $rows = [
            ['--- CASH FLOW FORECAST & RUNWAY PROJECTION ---', '', '', '', '', '', ''],
            ['Generated On:', Carbon::now()->toDateTimeString(), '', '', '', '', ''],
            ['Scenario:', strtoupper($scenario), '', '', '', '', ''],
            ['Projection Horizon:', "{$horizon} Days", '', '', '', '', ''],
            ['Current Liquid Cash:', number_format($burnRate['current_cash'], 2) . ' ' . $currCode, '', '', '', '', ''],
            ['Avg Monthly Gross Burn:', number_format($burnRate['avg_monthly_expense'], 2) . ' ' . $currCode, '', '', '', '', ''],
            ['Avg Monthly Net Burn:', number_format($burnRate['avg_monthly_net_burn'], 2) . ' ' . $currCode, '', '', '', '', ''],
            ['Runway:', $burnRate['runway_months'] !== null ? "{$burnRate['runway_months']} Months" : 'Self-Sustaining (Cash Flow Positive)', '', '', '', '', ''],
            ['Projected Ending Balance:', number_format($forecast['summary']['ending_balance'], 2) . ' ' . $currCode, '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            [
                'Period #',
                'Period Range',
                'Start Date',
                'End Date',
                "Starting Balance ({$currCode})",
                "Expected Inflows ({$currCode})",
                "Scheduled Outflows ({$currCode})",
                "Net Cash Flow ({$currCode})",
                "Ending Balance ({$currCode})",
                'Safety Margin',
            ]
        ];

        foreach ($forecast['periods'] as $period) {
            $rows[] = [
                'W' . $period['period_number'],
                $period['label'],
                $period['start_date'],
                $period['end_date'],
                number_format($period['starting_balance'], 2, '.', ''),
                number_format($period['inflows'], 2, '.', ''),
                number_format($period['outflows'], 2, '.', ''),
                number_format($period['net_change'], 2, '.', ''),
                number_format($period['ending_balance'], 2, '.', ''),
                strtoupper($period['status']),
            ];
        }

        // Add discrete upcoming events section in CSV
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['--- SCHEDULED CASH FLOW EVENTS BREAKDOWN ---', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['Date', 'Event Type', 'Category', 'Description', 'Details', "Amount ({$currCode})", '', '', '', ''];

        foreach ($forecast['upcoming_events'] as $evt) {
            $rows[] = [
                $evt['date'],
                strtoupper($evt['type']),
                $evt['category'],
                $evt['title'],
                $evt['subtitle'] ?? '',
                ($evt['type'] === 'inflow' ? '+' : '-') . number_format($evt['amount'], 2, '.', ''),
                '', '', '', ''
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                $field = str_replace('"', '""', (string) $field);
                return '"' . $field . '"';
            }, $row)) . "\n";
        }

        $filename = "Cash_Flow_Forecast_{$scenario}_{$horizon}d_" . Carbon::now()->format('Ymd_His') . ".csv";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
