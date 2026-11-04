<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowForecastService
{
    /**
     * Get the total liquid cash balance across all active accounts converted to base currency.
     */
    public function getCurrentLiquidCash(int $userId): float
    {
        $accounts = Account::where('user_id', $userId)
            ->active()
            ->get();

        $total = 0.0;
        foreach ($accounts as $account) {
            $total += (float) $account->base_balance;
        }

        return round($total, 2);
    }

    /**
     * Compute historical burn rate, average income, and runway metrics.
     */
    public function getBurnRateAnalytics(int $userId, int $lookbackMonths = 3): array
    {
        $currentCash = $this->getCurrentLiquidCash($userId);
        $lookbackDays = $lookbackMonths * 30;
        $startDate = Carbon::today()->subDays($lookbackDays)->toDateString();
        $endDate = Carbon::today()->toDateString();

        // Historical expenses in base currency
        $totalExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(base_amount, amount)'));

        // Historical income in base currency
        $totalIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(base_amount, amount)'));

        $months = max(1, $lookbackMonths);
        $avgMonthlyExpense = round($totalExpense / $months, 2);
        $avgMonthlyIncome = round($totalIncome / $months, 2);
        $avgMonthlyNetBurn = round($avgMonthlyExpense - $avgMonthlyIncome, 2);

        $runwayMonths = null;
        $runwayZeroDate = null;
        $status = 'positive';
        $statusLabel = 'Cash Flow Positive / Self-Sustaining';
        $statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';

        if ($avgMonthlyNetBurn > 0) {
            $runwayMonths = $currentCash > 0 ? round($currentCash / $avgMonthlyNetBurn, 1) : 0.0;
            $runwayDays = (int) round($runwayMonths * 30.41);
            $runwayZeroDate = Carbon::today()->addDays($runwayDays)->toDateString();

            if ($runwayMonths >= 12) {
                $status = 'healthy';
                $statusLabel = 'Healthy Runway (>12 Months)';
                $statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif ($runwayMonths >= 6) {
                $status = 'moderate';
                $statusLabel = 'Moderate Runway (6–12 Months)';
                $statusBadge = 'bg-blue-50 text-blue-700 border-blue-200';
            } elseif ($runwayMonths >= 3) {
                $status = 'caution';
                $statusLabel = 'Caution Runway (3–6 Months)';
                $statusBadge = 'bg-amber-50 text-amber-700 border-amber-200';
            } else {
                $status = 'critical';
                $statusLabel = 'Critical Runway (<3 Months)';
                $statusBadge = 'bg-rose-50 text-rose-700 border-rose-200';
            }
        }

        return [
            'current_cash' => $currentCash,
            'lookback_months' => $lookbackMonths,
            'total_expense' => $totalExpense,
            'total_income' => $totalIncome,
            'avg_monthly_expense' => $avgMonthlyExpense, // Gross burn
            'avg_monthly_income' => $avgMonthlyIncome,
            'avg_monthly_net_burn' => $avgMonthlyNetBurn, // Net burn
            'runway_months' => $runwayMonths,
            'runway_zero_date' => $runwayZeroDate,
            'status' => $status,
            'status_label' => $statusLabel,
            'status_badge' => $statusBadge,
        ];
    }

    /**
     * Generate forward-looking cash flow forecast for a given time horizon and scenario.
     *
     * @param int $userId
     * @param int $horizonDays (e.g. 30, 60, 90, 180)
     * @param string $scenario ('baseline', 'conservative', 'optimistic')
     */
    public function generateForecast(int $userId, int $horizonDays = 90, string $scenario = 'baseline'): array
    {
        $currentCash = $this->getCurrentLiquidCash($userId);
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($horizonDays);

        // Daily bucket mapping: 'YYYY-MM-DD' => ['inflows' => 0.0, 'outflows' => 0.0, 'events' => []]
        $dailyBuckets = [];
        $currentCursor = $startDate->copy();
        while ($currentCursor->lte($endDate)) {
            $key = $currentCursor->toDateString();
            $dailyBuckets[$key] = [
                'date' => $key,
                'inflows' => 0.0,
                'outflows' => 0.0,
                'events' => [],
            ];
            $currentCursor->addDay();
        }

        $allDiscreteEvents = [];

        // 1. Invoices (Accounts Receivable)
        $invoices = Invoice::where('user_id', $userId)
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->with('client')
            ->get();

        foreach ($invoices as $inv) {
            $balanceDue = (float) $inv->balance_due;
            if ($balanceDue <= 0) {
                continue;
            }

            // Convert to base currency
            $baseAmount = Currency::toBase($balanceDue, $inv->currency ?? 'BDT');

            // Apply scenario factor
            $effectiveAmount = match ($scenario) {
                'conservative' => round($baseAmount * 0.75, 2), // 75% collection probability
                'optimistic' => round($baseAmount * 1.05, 2),   // 5% faster / bonus collection
                default => round($baseAmount, 2),               // 100% baseline
            };

            // Determine scheduled date
            $targetDate = $inv->due_date ? Carbon::parse($inv->due_date) : $startDate;

            // In conservative scenario, add 10 days collection delay
            if ($scenario === 'conservative') {
                $targetDate = $targetDate->copy()->addDays(10);
            }

            // If due in past (overdue), schedule for today or tomorrow
            if ($targetDate->lt($startDate)) {
                $targetDate = $startDate->copy()->addDay();
            }

            $dateKey = $targetDate->toDateString();
            if (isset($dailyBuckets[$dateKey])) {
                $dailyBuckets[$dateKey]['inflows'] += $effectiveAmount;

                $event = [
                    'date' => $dateKey,
                    'type' => 'inflow',
                    'category' => 'Invoice Receivable',
                    'title' => "Invoice #{$inv->invoice_number}",
                    'subtitle' => $inv->client ? "Client: {$inv->client->name}" : 'Invoice Payment',
                    'amount' => $effectiveAmount,
                    'original_amount' => $baseAmount,
                    'icon' => 'document-text',
                ];

                $dailyBuckets[$dateKey]['events'][] = $event;
                $allDiscreteEvents[] = $event;
            }
        }

        // 2. Recurring Transactions (Subscriptions & Recurring Income)
        $recurring = RecurringTransaction::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        foreach ($recurring as $item) {
            $itemBaseAmount = Currency::toBase((float)$item->amount, $item->currency ?? 'BDT');
            if ($itemBaseAmount <= 0) {
                continue;
            }

            // In conservative scenario, add a 10% inflation buffer on recurring expenses
            if ($item->type === 'expense' && $scenario === 'conservative') {
                $itemBaseAmount = round($itemBaseAmount * 1.10, 2);
            }

            // Advance occurrences from next_due_date through horizon
            $occurrence = $item->next_due_date ? Carbon::parse($item->next_due_date) : $startDate->copy();
            
            // If overdue, catch up starting from today
            if ($occurrence->lt($startDate)) {
                $occurrence = $startDate->copy();
            }

            while ($occurrence->lte($endDate)) {
                // If past item end_date, break
                if ($item->end_date && $occurrence->gt(Carbon::parse($item->end_date))) {
                    break;
                }

                $dateKey = $occurrence->toDateString();
                if (isset($dailyBuckets[$dateKey])) {
                    if ($item->type === 'income') {
                        $dailyBuckets[$dateKey]['inflows'] += $itemBaseAmount;
                    } else {
                        $dailyBuckets[$dateKey]['outflows'] += $itemBaseAmount;
                    }

                    $event = [
                        'date' => $dateKey,
                        'type' => $item->type === 'income' ? 'inflow' : 'outflow',
                        'category' => 'Recurring ' . ucfirst($item->type),
                        'title' => $item->name,
                        'subtitle' => ucfirst($item->frequency) . ' cadence',
                        'amount' => $itemBaseAmount,
                        'original_amount' => $itemBaseAmount,
                        'icon' => $item->type === 'income' ? 'arrow-down' : 'arrow-path',
                    ];

                    $dailyBuckets[$dateKey]['events'][] = $event;
                    $allDiscreteEvents[] = $event;
                }

                // Step to next due date
                $occurrence = $item->computeNextDueDate($occurrence);
            }
        }

        // Sort all discrete events chronologically
        usort($allDiscreteEvents, fn($a, $b) => strcmp($a['date'], $b['date']));

        // 3. Compute running balance, chart points, and extremes
        $runningBalance = $currentCash;
        $cumulativeInflows = 0.0;
        $cumulativeOutflows = 0.0;

        $chartLabels = [];
        $chartBalances = [];
        $chartInflows = [];
        $chartOutflows = [];

        $lowestBalance = $currentCash;
        $lowestBalanceDate = $startDate->toDateString();
        $highestBalance = $currentCash;
        $highestBalanceDate = $startDate->toDateString();
        $dipsBelowZero = $currentCash < 0;
        $firstNegativeDate = $currentCash < 0 ? $startDate->toDateString() : null;

        $stepInterval = $horizonDays > 90 ? 3 : ($horizonDays > 45 ? 2 : 1);
        $dayCounter = 0;

        foreach ($dailyBuckets as $dayKey => $data) {
            $inflow = $data['inflows'];
            $outflow = $data['outflows'];

            $cumulativeInflows += $inflow;
            $cumulativeOutflows += $outflow;
            $runningBalance = round($runningBalance + $inflow - $outflow, 2);

            if ($runningBalance < $lowestBalance) {
                $lowestBalance = $runningBalance;
                $lowestBalanceDate = $dayKey;
            }

            if ($runningBalance > $highestBalance) {
                $highestBalance = $runningBalance;
                $highestBalanceDate = $dayKey;
            }

            if ($runningBalance < 0 && !$dipsBelowZero) {
                $dipsBelowZero = true;
                $firstNegativeDate = $dayKey;
            }

            // Downsample points for clean Chart.js display if horizon is long
            if ($dayCounter % $stepInterval === 0 || $dayKey === $endDate->toDateString()) {
                $chartLabels[] = Carbon::parse($dayKey)->format('M d');
                $chartBalances[] = $runningBalance;
                $chartInflows[] = round($inflow, 2);
                $chartOutflows[] = round($outflow, 2);
            }

            $dayCounter++;
        }

        $endingBalance = $runningBalance;
        $netChange = round($endingBalance - $currentCash, 2);
        $percentageChange = $currentCash != 0 ? round(($netChange / abs($currentCash)) * 100, 1) : 0.0;

        // 4. Build Weekly / Period Breakdown Table
        $periods = [];
        $periodStart = $startDate->copy();
        $periodCounter = 1;
        $periodRunningBalance = $currentCash;

        while ($periodStart->lt($endDate)) {
            $periodEnd = $periodStart->copy()->addDays(6);
            if ($periodEnd->gt($endDate)) {
                $periodEnd = $endDate->copy();
            }

            $periodInflows = 0.0;
            $periodOutflows = 0.0;
            $cursor = $periodStart->copy();

            while ($cursor->lte($periodEnd)) {
                $k = $cursor->toDateString();
                if (isset($dailyBuckets[$k])) {
                    $periodInflows += $dailyBuckets[$k]['inflows'];
                    $periodOutflows += $dailyBuckets[$k]['outflows'];
                }
                $cursor->addDay();
            }

            $startBal = $periodRunningBalance;
            $periodNet = round($periodInflows - $periodOutflows, 2);
            $endBal = round($startBal + $periodNet, 2);
            $periodRunningBalance = $endBal;

            $statusMargin = 'safe';
            if ($endBal < 0) {
                $statusMargin = 'deficit';
            } elseif ($endBal < ($currentCash * 0.25)) {
                $statusMargin = 'tight';
            }

            $periods[] = [
                'period_number' => $periodCounter,
                'label' => "Week {$periodCounter} (" . $periodStart->format('M d') . ' – ' . $periodEnd->format('M d') . ')',
                'start_date' => $periodStart->toDateString(),
                'end_date' => $periodEnd->toDateString(),
                'starting_balance' => $startBal,
                'inflows' => round($periodInflows, 2),
                'outflows' => round($periodOutflows, 2),
                'net_change' => $periodNet,
                'ending_balance' => $endBal,
                'status' => $statusMargin,
            ];

            $periodStart->addDays(7);
            $periodCounter++;
        }

        return [
            'scenario' => $scenario,
            'horizon_days' => $horizonDays,
            'summary' => [
                'current_cash' => $currentCash,
                'ending_balance' => $endingBalance,
                'net_change' => $netChange,
                'percentage_change' => $percentageChange,
                'total_inflows' => round($cumulativeInflows, 2),
                'total_outflows' => round($cumulativeOutflows, 2),
                'lowest_balance' => $lowestBalance,
                'lowest_balance_date' => $lowestBalanceDate,
                'highest_balance' => $highestBalance,
                'highest_balance_date' => $highestBalanceDate,
                'dips_below_zero' => $dipsBelowZero,
                'first_negative_date' => $firstNegativeDate,
            ],
            'chart_data' => [
                'labels' => $chartLabels,
                'balances' => $chartBalances,
                'inflows' => $chartInflows,
                'outflows' => $chartOutflows,
            ],
            'periods' => $periods,
            'upcoming_events' => array_slice($allDiscreteEvents, 0, 20),
        ];
    }

    /**
     * Compute comparative metrics across Baseline, Conservative, and Optimistic scenarios.
     */
    public function getScenarioComparison(int $userId, int $horizonDays = 90): array
    {
        $baseline = $this->generateForecast($userId, $horizonDays, 'baseline');
        $conservative = $this->generateForecast($userId, $horizonDays, 'conservative');
        $optimistic = $this->generateForecast($userId, $horizonDays, 'optimistic');

        return [
            'baseline' => $baseline['summary'],
            'conservative' => $conservative['summary'],
            'optimistic' => $optimistic['summary'],
        ];
    }
}
