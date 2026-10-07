<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\ExpenseCategory;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);
        $from = $dates['from'];
        $to = $dates['to'];

        // Summary cards
        $totalIncome = Transaction::forUser($user->id)
            ->income()
            ->dateBetween($from, $to)
            ->sum('amount');

        $totalExpense = Transaction::forUser($user->id)
            ->expense()
            ->dateBetween($from, $to)
            ->sum('amount');

        $netCashFlow = bcsub($totalIncome, $totalExpense, 2);

        // All accounts with balances
        $accounts = Account::where('user_id', $user->id)
            ->active()
            ->ordered()
            ->get();

        $totalBalance = '0.00';
        foreach ($accounts as $account) {
            $totalBalance = bcadd($totalBalance, $account->balance, 2);
        }

        // Recent transactions
        $recentTransactions = Transaction::forUser($user->id)
            ->with(['account', 'incomeCategory', 'expenseCategory', 'fromAccount', 'toAccount'])
            ->latest('date')
            ->latest('id')
            ->limit(10)
            ->get();

        // Monthly income vs expense data for chart (last 6 months)
        $monthlyData = $this->getMonthlyData($user->id);

        // Expense breakdown by category for selected period
        $expenseBreakdown = Transaction::forUser($user->id)
            ->expense()
            ->dateBetween($from, $to)
            ->join('expense_categories', 'transactions.expense_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category_name, SUM(transactions.amount) as total')
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->get();
        // Active budgets for the current month
        $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
        $activeBudgets = Budget::where('user_id', $user->id)
            ->where('start_date', $currentMonthStart)
            ->where('end_date', $currentMonthEnd)
            ->with('category')
            ->get();

        return view('dashboard.index', compact(
            'totalBalance',
            'totalIncome',
            'totalExpense',
            'netCashFlow',
            'accounts',
            'recentTransactions',
            'monthlyData',
            'expenseBreakdown',
            'activeBudgets',
            'from',
            'to',
        ));
    }

    /**
     * Get date range from request or default to current month.
     */
    private function getDateRange(Request $request): array
    {
        $period = $request->get('period', 'this_month');

        return match ($period) {
            'today' => [
                'from' => Carbon::today(),
                'to' => Carbon::today(),
            ],
            'yesterday' => [
                'from' => Carbon::yesterday(),
                'to' => Carbon::yesterday(),
            ],
            'this_week' => [
                'from' => Carbon::now()->startOfWeek(),
                'to' => Carbon::now()->endOfWeek(),
            ],
            'this_month' => [
                'from' => Carbon::now()->startOfMonth(),
                'to' => Carbon::now()->endOfMonth(),
            ],
            'last_month' => [
                'from' => Carbon::now()->subMonth()->startOfMonth(),
                'to' => Carbon::now()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                'from' => Carbon::now()->startOfYear(),
                'to' => Carbon::now()->endOfYear(),
            ],
            'last_year' => [
                'from' => Carbon::now()->subYear()->startOfYear(),
                'to' => Carbon::now()->subYear()->endOfYear(),
            ],
            'custom' => [
                'from' => $request->get('from') ? Carbon::parse($request->get('from')) : Carbon::now()->startOfMonth(),
                'to' => $request->get('to') ? Carbon::parse($request->get('to')) : Carbon::now()->endOfMonth(),
            ],
            default => [
                'from' => Carbon::now()->startOfMonth(),
                'to' => Carbon::now()->endOfMonth(),
            ],
        };
    }

    /**
     * Get monthly income and expense data for the last 6 months.
     */
    private function getMonthlyData(int $userId): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $incomeData[] = (float) Transaction::forUser($userId)
                ->income()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $expenseData[] = (float) Transaction::forUser($userId)
                ->expense()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData,
        ];
    }
}
