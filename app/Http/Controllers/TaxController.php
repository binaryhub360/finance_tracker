<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Services\TaxService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TaxController extends Controller
{
    /**
     * Display the Tax Center dashboard.
     */
    public function index(Request $request, TaxService $taxService): View
    {
        $user = $request->user();
        $selectedYear = $request->filled('year') ? (int) $request->year : null;
        $startMonth = (int) Setting::get('tax_fiscal_year_start_month', 1);

        $dates = TaxService::getFiscalYearDates($selectedYear, $startMonth);
        $availableYears = TaxService::getAvailableFiscalYears($user->id, $startMonth);

        // Fetch Tax Summary metrics
        $taxSummary = $taxService->getTaxSummary($user->id, $dates['start'], $dates['end']);

        // Schedule of Deductions grouped by Tax Category
        $categoryBreakdown = $taxService->getCategoryBreakdown($user->id, $dates['start'], $dates['end']);

        // Monthly timeline of deductions
        $monthlyBreakdown = $taxService->getMonthlyBreakdown($user->id, $dates['start'], $dates['end']);

        // Deductible transactions query
        $deductibleQuery = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('is_tax_deductible', true)
            ->whereBetween('date', [$dates['start'], $dates['end']])
            ->with(['account', 'expenseCategory']);

        if ($request->filled('tax_category')) {
            $deductibleQuery->where('tax_category', $request->tax_category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $deductibleQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('tax_notes', 'like', "%{$search}%");
            });
        }

        $deductibleTransactions = $deductibleQuery->latest('date')->latest('id')->paginate(15, ['*'], 'deductible_page')->withQueryString();

        // Unreviewed expenses (potential deductions not yet claimed)
        $unreviewedExpenses = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('is_tax_deductible', false)
            ->whereBetween('date', [$dates['start'], $dates['end']])
            ->with(['account', 'expenseCategory'])
            ->latest('date')
            ->paginate(10, ['*'], 'unreviewed_page')
            ->withQueryString();

        // Tax profile settings
        $taxSettings = [
            'start_month' => $startMonth,
            'tax_rate' => (float) Setting::get('tax_estimated_rate', 25.0),
            'tax_id' => Setting::get('tax_id_number', ''),
            'entity_name' => Setting::get('tax_entity_name', $user->name),
            'country' => Setting::get('tax_country_standard', 'GLOBAL'),
        ];

        $taxCategories = TaxService::CATEGORIES;

        return view('taxes.index', compact(
            'dates',
            'availableYears',
            'taxSummary',
            'categoryBreakdown',
            'monthlyBreakdown',
            'deductibleTransactions',
            'unreviewedExpenses',
            'taxSettings',
            'taxCategories'
        ));
    }

    /**
     * Export accountant-ready CSV for the fiscal year.
     */
    public function exportCsv(Request $request, TaxService $taxService): Response
    {
        $user = $request->user();
        $selectedYear = $request->filled('year') ? (int) $request->year : null;
        $startMonth = (int) Setting::get('tax_fiscal_year_start_month', 1);

        $dates = TaxService::getFiscalYearDates($selectedYear, $startMonth);

        $transactions = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('is_tax_deductible', true)
            ->whereBetween('date', [$dates['start'], $dates['end']])
            ->with(['account', 'expenseCategory'])
            ->orderBy('date', 'asc')
            ->get();

        $taxRate = (float) Setting::get('tax_estimated_rate', 25.0);
        $taxId = Setting::get('tax_id_number', 'N/A');
        $entityName = Setting::get('tax_entity_name', $user->name);

        $headers = [
            'Date',
            'Transaction ID',
            'Reference #',
            'Account',
            'Expense Category',
            'Tax Schedule / Category',
            'Native Amount',
            'Currency',
            'Exchange Rate',
            'Base Amount',
            'Deduction %',
            'Deductible Claimed',
            'Tax Justification / Notes',
            'Description',
        ];

        $rows = [];

        // Meta headers
        $rows[] = ["# TAX DEDUCTION LEDGER - FY {$dates['year']}", '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ["# Entity Name: {$entityName}", "# Tax ID: {$taxId}", "# Reporting Period: {$dates['start']->format('Y-m-d')} to {$dates['end']->format('Y-m-d')}", '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = $headers;

        $totalGross = 0;
        $totalDeductible = 0;

        foreach ($transactions as $txn) {
            $catDef = TaxService::getCategory($txn->tax_category);
            $scheduleName = ($catDef['name'] ?? 'Other Deductible') . ' (' . ($catDef['tax_schedule'] ?? 'Schedule C') . ')';

            $totalGross += (float) ($txn->base_amount ?? $txn->amount);
            $totalDeductible += (float) $txn->tax_deductible_amount;

            $rows[] = [
                $txn->date->format('Y-m-d'),
                'TXN-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT),
                $txn->reference ?? '',
                $txn->account?->name ?? 'N/A',
                $txn->expenseCategory?->name ?? 'Uncategorized',
                $scheduleName,
                number_format((float) $txn->amount, 2, '.', ''),
                $txn->currency ?? 'BDT',
                number_format((float) ($txn->exchange_rate ?? 1.0), 6, '.', ''),
                number_format((float) ($txn->base_amount ?? $txn->amount), 2, '.', ''),
                number_format((float) ($txn->deduction_percentage ?? 100), 1, '.', '') . '%',
                number_format((float) $txn->tax_deductible_amount, 2, '.', ''),
                $txn->tax_notes ?? '',
                $txn->description ?? '',
            ];
        }

        // Summary rows
        $estimatedSavings = round($totalDeductible * ($taxRate / 100), 2);
        $rows[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['--- SUMMARY TOTALS ---', '', '', '', '', '', '', '', 'Gross Expenses:', number_format($totalGross, 2, '.', ''), 'Total Deductible:', number_format($totalDeductible, 2, '.', ''), 'Est. Savings (' . $taxRate . '%):', number_format($estimatedSavings, 2, '.', '')];

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                $field = str_replace('"', '""', (string) $field);
                return '"' . $field . '"';
            }, $row)) . "\n";
        }

        $filename = "Tax_Deductions_FY{$dates['year']}_" . Carbon::now()->format('Ymd_His') . ".csv";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Display printable CPA Accountant Pack statement.
     */
    public function report(Request $request, TaxService $taxService): View
    {
        $user = $request->user();
        $selectedYear = $request->filled('year') ? (int) $request->year : null;
        $startMonth = (int) Setting::get('tax_fiscal_year_start_month', 1);

        $dates = TaxService::getFiscalYearDates($selectedYear, $startMonth);
        $taxSummary = $taxService->getTaxSummary($user->id, $dates['start'], $dates['end']);
        $categoryBreakdown = $taxService->getCategoryBreakdown($user->id, $dates['start'], $dates['end']);

        $transactions = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('is_tax_deductible', true)
            ->whereBetween('date', [$dates['start'], $dates['end']])
            ->with(['account', 'expenseCategory'])
            ->orderBy('date', 'asc')
            ->get();

        $taxSettings = [
            'start_month' => $startMonth,
            'tax_rate' => (float) Setting::get('tax_estimated_rate', 25.0),
            'tax_id' => Setting::get('tax_id_number', 'N/A'),
            'entity_name' => Setting::get('tax_entity_name', $user->name),
            'country' => Setting::get('tax_country_standard', 'GLOBAL'),
        ];

        return view('taxes.report', compact(
            'dates',
            'taxSummary',
            'categoryBreakdown',
            'transactions',
            'taxSettings'
        ));
    }

    /**
     * Quick AJAX toggle to claim or un-claim an expense transaction as tax-deductible.
     */
    public function quickToggle(Request $request, Transaction $transaction): JsonResponse|RedirectResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($transaction->type !== 'expense') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Only expense transactions can be tax deductible.'], 422);
            }
            return redirect()->back()->with('error', 'Only expense transactions can be tax deductible.');
        }

        $nowDeductible = !$transaction->is_tax_deductible;
        $transaction->is_tax_deductible = $nowDeductible;

        if ($nowDeductible) {
            // Check if expense category has defaults
            $cat = $transaction->expenseCategory;
            $transaction->tax_category = $request->input('tax_category')
                ?? $cat?->default_tax_category
                ?? 'other_deductible';

            $transaction->deduction_percentage = $request->input('deduction_percentage')
                ?? $cat?->default_deduction_percentage
                ?? (TaxService::getCategory($transaction->tax_category)['default_percentage'] ?? 100.00);

            $transaction->tax_notes = $request->input('tax_notes') ?? $transaction->tax_notes ?? 'Claimed in Tax Review';
        }

        $transaction->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_tax_deductible' => $transaction->is_tax_deductible,
                'tax_deductible_amount' => (float) $transaction->tax_deductible_amount,
                'tax_category' => $transaction->tax_category,
                'tax_category_label' => $transaction->tax_category_label,
                'deduction_percentage' => (float) $transaction->deduction_percentage,
                'message' => $nowDeductible ? 'Expense claimed as tax deductible!' : 'Expense unmarked from tax deductions.',
            ]);
        }

        return redirect()->back()->with('success', $nowDeductible ? 'Expense claimed as tax deductible!' : 'Expense unmarked from tax deductions.');
    }

    /**
     * Update Tax Profile & Fiscal Year Settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tax_fiscal_year_start_month' => ['required', 'integer', 'min:1', 'max:12'],
            'tax_estimated_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_id_number' => ['nullable', 'string', 'max:50'],
            'tax_entity_name' => ['nullable', 'string', 'max:150'],
            'tax_country_standard' => ['required', 'string', 'in:GLOBAL,US_IRS,BD_NBR,UK_HMRC'],
        ]);

        foreach ($validated as $key => $val) {
            Setting::set($key, (string) $val);
        }

        return redirect()->back()->with('success', 'Tax Profile & Fiscal Year settings updated successfully.');
    }
}
