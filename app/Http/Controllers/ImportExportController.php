<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Client;
use App\Models\Goal;
use App\Models\ImportBatch;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\CsvImportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImportExportController extends Controller
{
    /**
     * Display the Import/Export Hub dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $batches = ImportBatch::where('user_id', $user->id)->with('account')->latest()->paginate(10);

        $totalImported = ImportBatch::where('user_id', $user->id)->where('status', 'completed')->sum('imported_count');
        $totalBatches = ImportBatch::where('user_id', $user->id)->count();

        return view('import-export.index', compact('accounts', 'batches', 'totalImported', 'totalBatches'));
    }

    /**
     * Step 2: Upload CSV and show interactive column mapping preview.
     */
    public function preview(Request $request, CsvImportService $importService): View|RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'account_id' => ['required', 'exists:accounts,id'],
            'default_type' => ['required', 'in:auto,expense,income'],
            'skip_duplicates' => ['nullable', 'boolean'],
        ]);

        $account = Account::where('user_id', $request->user()->id)->findOrFail($request->account_id);
        $file = $request->file('csv_file');
        $originalFilename = $file->getClientOriginalName();
        $content = File::get($file->getRealPath());

        $parsed = $importService->parseCsv($content, maxRows: 1000);

        if (empty($parsed['headers'])) {
            return redirect()->back()->with('error', 'The uploaded CSV file is empty or cannot be parsed.');
        }

        $guessedMappings = $importService->guessColumnMappings($parsed['headers']);

        // Save file content in temporary storage for process step
        $importToken = Str::random(40);
        $tempPath = 'imports/' . $request->user()->id . '_' . $importToken . '.csv';
        Storage::disk('local')->put($tempPath, $content);

        $previewRows = array_slice($parsed['rows'], 0, 6);
        $totalRows = count($parsed['rows']);

        $options = [
            'account_id' => $account->id,
            'default_type' => $request->default_type,
            'skip_duplicates' => $request->boolean('skip_duplicates', true),
            'filename' => $originalFilename,
            'import_token' => $importToken,
        ];

        return view('import-export.preview', [
            'account' => $account,
            'headers' => $parsed['headers'],
            'previewRows' => $previewRows,
            'totalRows' => $totalRows,
            'guessedMappings' => $guessedMappings,
            'options' => $options,
            'delimiter' => $parsed['delimiter'],
        ]);
    }

    /**
     * Step 3: Execute the import using user-confirmed column mappings.
     */
    public function process(Request $request, CsvImportService $importService): RedirectResponse
    {
        $request->validate([
            'import_token' => ['required', 'string'],
            'account_id' => ['required', 'exists:accounts,id'],
            'filename' => ['required', 'string'],
            'mappings' => ['required', 'array'],
            'date_format' => ['nullable', 'string'],
            'default_type' => ['required', 'in:auto,expense,income'],
            'skip_duplicates' => ['nullable', 'boolean'],
        ]);

        $userId = $request->user()->id;
        $account = Account::where('user_id', $userId)->findOrFail($request->account_id);

        $tempPath = 'imports/' . $userId . '_' . $request->import_token . '.csv';

        if (!Storage::disk('local')->exists($tempPath)) {
            return redirect()->route('import-export.index')
                ->with('error', 'Import session expired. Please re-upload your file.');
        }

        $content = Storage::disk('local')->get($tempPath);
        Storage::disk('local')->delete($tempPath);

        $parsed = $importService->parseCsv($content, maxRows: 5000);

        if (empty($parsed['rows'])) {
            return redirect()->route('import-export.index')->with('error', 'No data rows found to import.');
        }

        // Clean user mappings
        $mappings = [];
        foreach ($request->mappings as $key => $val) {
            $mappings[$key] = ($val !== '' && $val !== null) ? (int) $val : null;
        }

        $options = [
            'skip_duplicates' => $request->boolean('skip_duplicates', true),
            'default_type' => $request->default_type,
            'date_format' => $request->input('date_format', 'auto'),
        ];

        $batch = $importService->executeImport(
            userId: $userId,
            account: $account,
            filename: $request->filename,
            headers: $parsed['headers'],
            rows: $parsed['rows'],
            mappings: $mappings,
            options: $options
        );

        $msg = "Processed {$batch->filename}: {$batch->imported_count} imported successfully";
        if ($batch->skipped_count > 0) {
            $msg .= ", {$batch->skipped_count} skipped as duplicates";
        }
        if ($batch->error_count > 0) {
            $msg .= ", {$batch->error_count} row errors";
        }
        $msg .= ".";

        return redirect()->route('import-export.index')->with('success', $msg);
    }

    /**
     * 1-Click Rollback: undo all transactions created by an import batch.
     */
    public function rollback(Request $request, ImportBatch $batch): RedirectResponse
    {
        if ($batch->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($batch->status === 'rolled_back') {
            return redirect()->back()->with('warning', 'This batch has already been rolled back.');
        }

        $count = $batch->rollback();

        return redirect()->route('import-export.index')
            ->with('success', "Successfully rolled back {$batch->filename}. {$count} transactions deleted.");
    }

    /**
     * Download pre-formatted CSV template.
     */
    public function downloadTemplate(string $type): Response
    {
        if ($type === 'bank_statement') {
            $headers = ['Date', 'Description', 'Debit', 'Credit', 'Reference'];
            $sampleRows = [
                ['2026-11-01', 'AWS Cloud Infrastructure', '145.50', '', 'AWS-INV-9901'],
                ['2026-11-02', 'Client Wire Transfer Payment', '', '3200.00', 'WIRE-44102'],
                ['2026-11-03', 'Office Supply Store', '62.00', '', 'REC-0012'],
                ['2026-11-04', 'Stripe Merchant Payout', '', '1850.25', 'PO-88214'],
            ];
            $filename = "Bank_Statement_Template.csv";
        } else {
            $headers = ['Date', 'Type', 'Category', 'Amount', 'Description', 'Reference'];
            $sampleRows = [
                ['2026-11-01', 'expense', 'Software & Subscriptions', '120.00', 'Cloud hosting subscription', 'INV-2026-01'],
                ['2026-11-02', 'income', 'Consulting & Services', '2500.00', 'Software development milestone', 'INV-CLIENT-88'],
                ['2026-11-03', 'expense', 'Office Supplies', '45.00', 'Stationery and desk equipment', 'PO-1029'],
            ];
            $filename = "Finance_Transactions_Template.csv";
        }

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleRows as $row) {
            $csv .= implode(',', array_map(fn ($f) => '"' . str_replace('"', '""', $f) . '"', $row)) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Unified Export Hub endpoint.
     */
    public function export(Request $request, string $dataset): Response
    {
        $user = $request->user();
        $timestamp = Carbon::now()->format('Ymd_His');

        switch ($dataset) {
            case 'transactions':
                return $this->exportTransactions($user->id, $timestamp);
            case 'accounts':
                return $this->exportAccounts($user->id, $timestamp);
            case 'invoices':
                return $this->exportInvoices($user->id, $timestamp);
            case 'budgets':
                return $this->exportBudgets($user->id, $timestamp);
            case 'goals':
                return $this->exportGoals($user->id, $timestamp);
            case 'backup_json':
                return $this->exportFullJsonBackup($user->id, $timestamp);
            default:
                abort(404);
        }
    }

    private function exportTransactions(int $userId, string $timestamp): Response
    {
        $transactions = Transaction::where('user_id', $userId)
            ->with(['account', 'incomeCategory', 'expenseCategory', 'fromAccount', 'toAccount'])
            ->latest('date')
            ->get();

        $headers = [
            'ID', 'Date', 'Type', 'Account', 'Category', 'Native Amount',
            'Currency', 'Exchange Rate', 'Base Amount', 'Is Tax Deductible',
            'Tax Category', 'Deduction %', 'Deductible Amount', 'Description', 'Reference',
        ];

        $rows = [];
        foreach ($transactions as $t) {
            $categoryName = match($t->type) {
                'income' => $t->incomeCategory?->name ?? 'Income',
                'expense' => $t->expenseCategory?->name ?? 'Expense',
                'transfer' => 'Transfer (' . ($t->fromAccount?->name ?? 'N/A') . ' -> ' . ($t->toAccount?->name ?? 'N/A') . ')',
                default => 'N/A',
            };

            $rows[] = [
                $t->id,
                $t->date->format('Y-m-d'),
                $t->type,
                $t->account?->name ?? ($t->fromAccount?->name ?? 'N/A'),
                $categoryName,
                number_format((float) $t->amount, 2, '.', ''),
                $t->currency ?? 'BDT',
                number_format((float) ($t->exchange_rate ?? 1.0), 6, '.', ''),
                number_format((float) ($t->base_amount ?? $t->amount), 2, '.', ''),
                $t->is_tax_deductible ? 'Yes' : 'No',
                $t->tax_category_label ?? '',
                number_format((float) ($t->deduction_percentage ?? 100), 1, '.', '') . '%',
                number_format((float) ($t->tax_deductible_amount ?? 0), 2, '.', ''),
                $t->description ?? '',
                $t->reference ?? '',
            ];
        }

        return $this->makeCsvResponse($headers, $rows, "Transactions_Export_{$timestamp}.csv");
    }

    private function exportAccounts(int $userId, string $timestamp): Response
    {
        $accounts = Account::where('user_id', $userId)->ordered()->get();
        $headers = ['ID', 'Account Name', 'Type', 'Currency', 'Native Balance', 'Base Balance (BDT)', 'Account Number', 'Status'];

        $rows = [];
        foreach ($accounts as $a) {
            $rows[] = [
                $a->id,
                $a->name,
                $a->type_label,
                $a->currency ?? 'BDT',
                number_format((float) $a->balance, 2, '.', ''),
                number_format((float) $a->base_balance, 2, '.', ''),
                $a->account_number ?? '',
                $a->is_active ? 'Active' : 'Inactive',
            ];
        }

        return $this->makeCsvResponse($headers, $rows, "Accounts_Export_{$timestamp}.csv");
    }

    private function exportInvoices(int $userId, string $timestamp): Response
    {
        $invoices = Invoice::where('user_id', $userId)->with('client')->latest('issue_date')->get();
        $headers = ['Invoice #', 'Client Name', 'Issue Date', 'Due Date', 'Currency', 'Subtotal', 'Tax', 'Total Amount', 'Paid Amount', 'Status'];

        $rows = [];
        foreach ($invoices as $inv) {
            $rows[] = [
                $inv->invoice_number,
                $inv->client?->name ?? 'N/A',
                $inv->issue_date->format('Y-m-d'),
                $inv->due_date->format('Y-m-d'),
                $inv->currency ?? 'BDT',
                number_format((float) $inv->subtotal, 2, '.', ''),
                number_format((float) $inv->tax_total, 2, '.', ''),
                number_format((float) $inv->total, 2, '.', ''),
                number_format((float) $inv->amount_paid, 2, '.', ''),
                ucfirst($inv->status),
            ];
        }

        return $this->makeCsvResponse($headers, $rows, "Invoices_Export_{$timestamp}.csv");
    }

    private function exportBudgets(int $userId, string $timestamp): Response
    {
        $budgets = Budget::where('user_id', $userId)->with('expenseCategory')->latest('month')->get();
        $headers = ['ID', 'Expense Category', 'Month', 'Target Limit', 'Current Spent', 'Remaining', 'Status'];

        $rows = [];
        foreach ($budgets as $b) {
            $rows[] = [
                $b->id,
                $b->expenseCategory?->name ?? 'N/A',
                $b->month,
                number_format((float) $b->amount, 2, '.', ''),
                number_format((float) $b->spent_amount, 2, '.', ''),
                number_format((float) $b->remaining_amount, 2, '.', ''),
                $b->is_exceeded ? 'Over Budget' : 'Within Limit',
            ];
        }

        return $this->makeCsvResponse($headers, $rows, "Budgets_Export_{$timestamp}.csv");
    }

    private function exportGoals(int $userId, string $timestamp): Response
    {
        $goals = Goal::where('user_id', $userId)->with('account')->latest()->get();
        $headers = ['ID', 'Vault / Goal Name', 'Target Amount', 'Current Saved', 'Progress %', 'Target Date', 'Linked Account', 'Status', 'Locked'];

        $rows = [];
        foreach ($goals as $g) {
            $rows[] = [
                $g->id,
                $g->name,
                number_format((float) $g->target_amount, 2, '.', ''),
                number_format((float) $g->current_amount, 2, '.', ''),
                number_format((float) $g->progress_percentage, 1, '.', '') . '%',
                $g->target_date ? $g->target_date->format('Y-m-d') : 'No deadline',
                $g->account?->name ?? 'Unlinked',
                ucfirst($g->status),
                $g->is_locked ? 'Locked' : 'Unlocked',
            ];
        }

        return $this->makeCsvResponse($headers, $rows, "Savings_Vaults_Export_{$timestamp}.csv");
    }

    private function exportFullJsonBackup(int $userId, string $timestamp): Response
    {
        $backupData = [
            'exported_at' => Carbon::now()->toIso8601String(),
            'app_version' => '1.0',
            'accounts' => Account::where('user_id', $userId)->get(),
            'clients' => Client::where('user_id', $userId)->get(),
            'transactions' => Transaction::where('user_id', $userId)->get(),
            'invoices' => Invoice::where('user_id', $userId)->with(['items', 'payments'])->get(),
            'budgets' => Budget::where('user_id', $userId)->get(),
            'goals' => Goal::where('user_id', $userId)->with('contributions')->get(),
        ];

        $json = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $filename = "FinanceTracker_Complete_Backup_{$timestamp}.json";

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function makeCsvResponse(array $headers, array $rows, string $filename): Response
    {
        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                $field = str_replace('"', '""', (string) $field);
                return '"' . $field . '"';
            }, $row)) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
