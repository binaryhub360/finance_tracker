<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function income(Request $request): View
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);
        $from = $dates['from'];
        $to = $dates['to'];

        $query = Transaction::forUser($user->id)->income()->dateBetween($from, $to);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('income_category_id', $request->category_id);
        }

        // Filter by account
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        $transactions = $query->with(['account', 'incomeCategory'])
            ->latest('date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $totalIncome = (clone $query)->sum('amount');
        $transactionCount = (clone $query)->count();

        // Category breakdown
        $categoryBreakdown = Transaction::forUser($user->id)
            ->income()
            ->dateBetween($from, $to)
            ->when($request->filled('account_id'), fn($q) => $q->where('transactions.account_id', $request->account_id))
            ->join('income_categories', 'transactions.income_category_id', '=', 'income_categories.id')
            ->selectRaw('income_categories.name as category_name, SUM(transactions.amount) as total, COUNT(*) as count')
            ->groupBy('income_categories.name')
            ->orderByDesc('total')
            ->get();

        // Account breakdown
        $accountBreakdown = Transaction::forUser($user->id)
            ->income()
            ->dateBetween($from, $to)
            ->when($request->filled('category_id'), fn($q) => $q->where('transactions.income_category_id', $request->category_id))
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->selectRaw('accounts.name as account_name, SUM(transactions.amount) as total, COUNT(*) as count')
            ->groupBy('accounts.name')
            ->orderByDesc('total')
            ->get();

        $categories = IncomeCategory::where('user_id', $user->id)->active()->ordered()->get();
        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();

        return view('reports.income', compact(
            'transactions', 'totalIncome', 'transactionCount',
            'categoryBreakdown', 'accountBreakdown',
            'categories', 'accounts', 'from', 'to',
        ));
    }

    public function expense(Request $request): View
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);
        $from = $dates['from'];
        $to = $dates['to'];

        $query = Transaction::forUser($user->id)->expense()->dateBetween($from, $to);

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        $transactions = $query->with(['account', 'expenseCategory'])
            ->latest('date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $totalExpense = (clone $query)->sum('amount');
        $transactionCount = (clone $query)->count();

        $categoryBreakdown = Transaction::forUser($user->id)
            ->expense()
            ->dateBetween($from, $to)
            ->when($request->filled('account_id'), fn($q) => $q->where('transactions.account_id', $request->account_id))
            ->join('expense_categories', 'transactions.expense_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category_name, SUM(transactions.amount) as total, COUNT(*) as count')
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $accountBreakdown = Transaction::forUser($user->id)
            ->expense()
            ->dateBetween($from, $to)
            ->when($request->filled('category_id'), fn($q) => $q->where('transactions.expense_category_id', $request->category_id))
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->selectRaw('accounts.name as account_name, SUM(transactions.amount) as total, COUNT(*) as count')
            ->groupBy('accounts.name')
            ->orderByDesc('total')
            ->get();

        $categories = ExpenseCategory::where('user_id', $user->id)->active()->ordered()->get();
        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();

        return view('reports.expense', compact(
            'transactions', 'totalExpense', 'transactionCount',
            'categoryBreakdown', 'accountBreakdown',
            'categories', 'accounts', 'from', 'to',
        ));
    }

    public function transfer(Request $request): View
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);
        $from = $dates['from'];
        $to = $dates['to'];

        $query = Transaction::forUser($user->id)->transfer()->dateBetween($from, $to);

        if ($request->filled('from_account_id')) {
            $query->where('from_account_id', $request->from_account_id);
        }

        if ($request->filled('to_account_id')) {
            $query->where('to_account_id', $request->to_account_id);
        }

        $transactions = $query->with(['fromAccount', 'toAccount'])
            ->latest('date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $totalTransfers = (clone $query)->sum('amount');
        $transferCount = (clone $query)->count();

        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();

        return view('reports.transfer', compact(
            'transactions', 'totalTransfers', 'transferCount',
            'accounts', 'from', 'to',
        ));
    }

    public function summary(Request $request): View
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);
        $from = $dates['from'];
        $to = $dates['to'];

        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();

        // Calculate opening balance (sum of all account opening balances)
        $openingBalance = $accounts->sum('opening_balance');

        // All-time totals up to the start of the period (for opening balance adjustment)
        $incomeBeforePeriod = Transaction::forUser($user->id)
            ->income()
            ->where('date', '<', $from)
            ->sum('amount');

        $expenseBeforePeriod = Transaction::forUser($user->id)
            ->expense()
            ->where('date', '<', $from)
            ->sum('amount');

        $adjustedOpeningBalance = bcadd($openingBalance, $incomeBeforePeriod, 2);
        $adjustedOpeningBalance = bcsub($adjustedOpeningBalance, $expenseBeforePeriod, 2);

        // Period totals
        $totalIncome = Transaction::forUser($user->id)
            ->income()
            ->dateBetween($from, $to)
            ->sum('amount');

        $totalExpense = Transaction::forUser($user->id)
            ->expense()
            ->dateBetween($from, $to)
            ->sum('amount');

        $totalTransfers = Transaction::forUser($user->id)
            ->transfer()
            ->dateBetween($from, $to)
            ->sum('amount');

        $transferCount = Transaction::forUser($user->id)
            ->transfer()
            ->dateBetween($from, $to)
            ->count();

        $netCashFlow = bcsub($totalIncome, $totalExpense, 2);

        $closingBalance = bcadd($adjustedOpeningBalance, $totalIncome, 2);
        $closingBalance = bcsub($closingBalance, $totalExpense, 2);

        return view('reports.summary', compact(
            'adjustedOpeningBalance', 'totalIncome', 'totalExpense',
            'netCashFlow', 'totalTransfers', 'transferCount',
            'closingBalance', 'from', 'to',
        ));
    }

    /**
     * Export income transactions as CSV.
     */
    public function exportIncome(Request $request): Response
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);

        $transactions = Transaction::forUser($user->id)
            ->income()
            ->dateBetween($dates['from'], $dates['to'])
            ->with(['account', 'incomeCategory'])
            ->latest('date')
            ->get();

        return $this->generateCsv($transactions, 'income', $dates['from'], $dates['to']);
    }

    /**
     * Export expense transactions as CSV.
     */
    public function exportExpense(Request $request): Response
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);

        $transactions = Transaction::forUser($user->id)
            ->expense()
            ->dateBetween($dates['from'], $dates['to'])
            ->with(['account', 'expenseCategory'])
            ->latest('date')
            ->get();

        return $this->generateCsv($transactions, 'expense', $dates['from'], $dates['to']);
    }

    /**
     * Export transfer transactions as CSV.
     */
    public function exportTransfer(Request $request): Response
    {
        $user = $request->user();
        $dates = $this->getDateRange($request);

        $transactions = Transaction::forUser($user->id)
            ->transfer()
            ->dateBetween($dates['from'], $dates['to'])
            ->with(['fromAccount', 'toAccount'])
            ->latest('date')
            ->get();

        return $this->generateCsv($transactions, 'transfer', $dates['from'], $dates['to']);
    }

    /**
     * Generate CSV response from transactions.
     */
    private function generateCsv($transactions, string $type, $from, $to): Response
    {
        $filename = "{$type}_report_{$from->format('Y-m-d')}_to_{$to->format('Y-m-d')}.csv";

        $headers = match ($type) {
            'income' => ['Date', 'Category', 'Account', 'Amount', 'Description', 'Reference'],
            'expense' => ['Date', 'Category', 'Account', 'Amount', 'Description', 'Reference'],
            'transfer' => ['Date', 'From Account', 'To Account', 'Amount', 'Description', 'Reference'],
        };

        $rows = $transactions->map(function ($txn) use ($type) {
            return match ($type) {
                'income' => [
                    $txn->date->format('Y-m-d'),
                    $txn->incomeCategory?->name ?? '',
                    $txn->account?->name ?? '',
                    $txn->amount,
                    $txn->description ?? '',
                    $txn->reference ?? '',
                ],
                'expense' => [
                    $txn->date->format('Y-m-d'),
                    $txn->expenseCategory?->name ?? '',
                    $txn->account?->name ?? '',
                    $txn->amount,
                    $txn->description ?? '',
                    $txn->reference ?? '',
                ],
                'transfer' => [
                    $txn->date->format('Y-m-d'),
                    $txn->fromAccount?->name ?? '',
                    $txn->toAccount?->name ?? '',
                    $txn->amount,
                    $txn->description ?? '',
                    $txn->reference ?? '',
                ],
            };
        });

        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                // Escape CSV fields
                $field = str_replace('"', '""', (string) $field);
                return '"' . $field . '"';
            }, $row)) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Get date range from request.
     */
    private function getDateRange(Request $request): array
    {
        $period = $request->get('period', 'this_month');

        return match ($period) {
            'today' => ['from' => Carbon::today(), 'to' => Carbon::today()],
            'yesterday' => ['from' => Carbon::yesterday(), 'to' => Carbon::yesterday()],
            'this_week' => ['from' => Carbon::now()->startOfWeek(), 'to' => Carbon::now()->endOfWeek()],
            'this_month' => ['from' => Carbon::now()->startOfMonth(), 'to' => Carbon::now()->endOfMonth()],
            'last_month' => ['from' => Carbon::now()->subMonth()->startOfMonth(), 'to' => Carbon::now()->subMonth()->endOfMonth()],
            'this_year' => ['from' => Carbon::now()->startOfYear(), 'to' => Carbon::now()->endOfYear()],
            'last_year' => ['from' => Carbon::now()->subYear()->startOfYear(), 'to' => Carbon::now()->subYear()->endOfYear()],
            'custom' => [
                'from' => $request->get('from') ? Carbon::parse($request->get('from')) : Carbon::now()->startOfMonth(),
                'to' => $request->get('to') ? Carbon::parse($request->get('to')) : Carbon::now()->endOfMonth(),
            ],
            default => ['from' => Carbon::now()->startOfMonth(), 'to' => Carbon::now()->endOfMonth()],
        };
    }
}
