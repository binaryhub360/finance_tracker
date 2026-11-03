<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\ImportBatch;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CsvImportService
{
    /**
     * Parse raw CSV content into array of rows and detected headers.
     */
    public function parseCsv(string $content, int $maxRows = 1000): array
    {
        // Strip UTF-8 BOM
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        $lines = explode("\n", $content);
        $lines = array_filter($lines, fn ($l) => trim($l) !== '');

        if (empty($lines)) {
            return ['headers' => [], 'rows' => [], 'delimiter' => ','];
        }

        // Detect delimiter from first non-empty line
        $firstLine = $lines[0];
        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;

        foreach ($delimiters as $delim) {
            $count = substr_count($firstLine, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        $parsedRows = [];
        foreach ($lines as $line) {
            $row = str_getcsv($line, $bestDelimiter);
            if (!empty(array_filter($row, fn ($val) => trim((string)$val) !== ''))) {
                $parsedRows[] = array_map('trim', $row);
            }
            if (count($parsedRows) >= $maxRows) {
                break;
            }
        }

        if (empty($parsedRows)) {
            return ['headers' => [], 'rows' => [], 'delimiter' => $bestDelimiter];
        }

        $headers = $parsedRows[0];
        $dataRows = array_slice($parsedRows, 1);

        return [
            'headers' => $headers,
            'rows' => $dataRows,
            'delimiter' => $bestDelimiter,
            'total_detected' => count($lines) - 1,
        ];
    }

    /**
     * Guess column mappings automatically from header names.
     */
    public function guessColumnMappings(array $headers): array
    {
        $mappings = [
            'date' => null,
            'description' => null,
            'amount' => null,
            'debit' => null,
            'credit' => null,
            'reference' => null,
            'category' => null,
            'type' => null,
        ];

        $patterns = [
            'date' => ['date', 'txndate', 'transactiondate', 'postingdate', 'valuedate', 'bookingdate', 'time', 'effectivedate'],
            'description' => ['description', 'details', 'narrative', 'memo', 'particulars', 'payee', 'merchant', 'beneficiary', 'name', 'title', 'note', 'notes'],
            'amount' => ['amount', 'total', 'net', 'money', 'value', 'sum'],
            'debit' => ['debit', 'dr', 'outflow', 'withdrawal', 'paidout', 'charge', 'expense', 'payments'],
            'credit' => ['credit', 'cr', 'inflow', 'deposit', 'paidin', 'received', 'income', 'receipts'],
            'reference' => ['reference', 'ref', 'chequeno', 'checkno', 'transid', 'transactionid', 'invoiceno', 'docno', 'id', 'refno'],
            'category' => ['category', 'expensecategory', 'incomecategory', 'classification', 'tag'],
            'type' => ['type', 'transactiontype', 'drcr', 'dc', 'entrytype'],
        ];

        foreach ($headers as $index => $header) {
            $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $header));

            foreach ($patterns as $field => $matches) {
                if ($mappings[$field] === null) {
                    foreach ($matches as $match) {
                        if ($norm === $match || str_contains($norm, $match)) {
                            $mappings[$field] = $index;
                            break 2;
                        }
                    }
                }
            }
        }

        return $mappings;
    }

    /**
     * Execute batch import process.
     */
    public function executeImport(
        int $userId,
        Account $account,
        string $filename,
        array $headers,
        array $rows,
        array $mappings,
        array $options = []
    ): ImportBatch {
        $skipDuplicates = $options['skip_duplicates'] ?? true;
        $defaultType = $options['default_type'] ?? 'auto'; // auto, expense, income
        $dateFormat = $options['date_format'] ?? 'auto';

        // Load existing categories for fast matching
        $expenseCats = ExpenseCategory::where('user_id', $userId)->get();
        $incomeCats = IncomeCategory::where('user_id', $userId)->get();

        $defaultExpenseCat = $expenseCats->first() ?? ExpenseCategory::create([
            'user_id' => $userId,
            'name' => 'General Expenses',
            'is_active' => true,
        ]);

        $defaultIncomeCat = $incomeCats->first() ?? IncomeCategory::create([
            'user_id' => $userId,
            'name' => 'General Income',
            'is_active' => true,
        ]);

        return DB::transaction(function () use (
            $userId, $account, $filename, $rows, $mappings,
            $skipDuplicates, $defaultType, $dateFormat,
            $expenseCats, $incomeCats, $defaultExpenseCat, $defaultIncomeCat
        ) {
            $batch = ImportBatch::create([
                'user_id' => $userId,
                'account_id' => $account->id,
                'filename' => $filename,
                'file_type' => 'csv',
                'status' => 'completed',
                'total_rows' => count($rows),
                'imported_count' => 0,
                'skipped_count' => 0,
                'error_count' => 0,
                'error_log' => [],
            ]);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $rowIndex => $row) {
                try {
                    // 1. Resolve Date
                    $rawDate = isset($mappings['date']) && isset($row[$mappings['date']]) ? trim($row[$mappings['date']]) : null;
                    $parsedDate = $this->parseDate($rawDate, $dateFormat);

                    if (!$parsedDate) {
                        $errors[] = "Row #" . ($rowIndex + 2) . ": Invalid or missing date '{$rawDate}'";
                        continue;
                    }

                    // 2. Resolve Amount & Type
                    $rawAmount = isset($mappings['amount']) && isset($row[$mappings['amount']]) ? $row[$mappings['amount']] : null;
                    $rawDebit = isset($mappings['debit']) && isset($row[$mappings['debit']]) ? $row[$mappings['debit']] : null;
                    $rawCredit = isset($mappings['credit']) && isset($row[$mappings['credit']]) ? $row[$mappings['credit']] : null;
                    $rawType = isset($mappings['type']) && isset($row[$mappings['type']]) ? strtolower(trim($row[$mappings['type']])) : null;

                    $typeAndAmount = $this->resolveTypeAndAmount($rawAmount, $rawDebit, $rawCredit, $rawType, $defaultType);

                    if (!$typeAndAmount || $typeAndAmount['amount'] <= 0) {
                        $errors[] = "Row #" . ($rowIndex + 2) . ": Invalid or zero amount";
                        continue;
                    }

                    $txnType = $typeAndAmount['type'];
                    $txnAmount = $typeAndAmount['amount'];

                    // 3. Resolve Description & Reference
                    $description = isset($mappings['description']) && isset($row[$mappings['description']]) ? trim($row[$mappings['description']]) : null;
                    $reference = isset($mappings['reference']) && isset($row[$mappings['reference']]) ? trim($row[$mappings['reference']]) : null;

                    if (!$description && !$reference) {
                        $description = "Imported statement entry (" . $parsedDate->format('Y-m-d') . ")";
                    }

                    // 4. Duplicate Check
                    if ($skipDuplicates) {
                        $duplicateQuery = Transaction::where('user_id', $userId)
                            ->where('account_id', $account->id)
                            ->where('date', $parsedDate->toDateString())
                            ->where('amount', $txnAmount);

                        if ($reference) {
                            $duplicateQuery->where(function ($q) use ($reference, $description) {
                                $q->where('reference', $reference)
                                    ->orWhere('description', $description);
                            });
                        } elseif ($description) {
                            $duplicateQuery->where('description', $description);
                        }

                        if ($duplicateQuery->exists()) {
                            $skipped++;
                            continue;
                        }
                    }

                    // 5. Category Resolution & Tax Default Inheriting
                    $rawCat = isset($mappings['category']) && isset($row[$mappings['category']]) ? trim($row[$mappings['category']]) : null;
                    $expenseCatId = null;
                    $incomeCatId = null;
                    $isTaxDeductible = false;
                    $taxCategory = null;
                    $deductionPct = 100.00;

                    if ($txnType === 'expense') {
                        $matchedCat = null;
                        if ($rawCat) {
                            $matchedCat = $expenseCats->first(fn ($c) => strcasecmp($c->name, $rawCat) === 0);
                        }
                        $targetCat = $matchedCat ?? $defaultExpenseCat;
                        $expenseCatId = $targetCat->id;

                        // Check if category has tax defaults
                        if ($targetCat->is_tax_deductible_default) {
                            $isTaxDeductible = true;
                            $taxCategory = $targetCat->default_tax_category ?? 'other_deductible';
                            $deductionPct = (float) ($targetCat->default_deduction_percentage ?? 100.00);
                        }
                    } else {
                        $matchedCat = null;
                        if ($rawCat) {
                            $matchedCat = $incomeCats->first(fn ($c) => strcasecmp($c->name, $rawCat) === 0);
                        }
                        $targetCat = $matchedCat ?? $defaultIncomeCat;
                        $incomeCatId = $targetCat->id;
                    }

                    // 6. Create Transaction
                    Transaction::create([
                        'user_id' => $userId,
                        'account_id' => $account->id,
                        'import_batch_id' => $batch->id,
                        'type' => $txnType,
                        'date' => $parsedDate->toDateString(),
                        'amount' => $txnAmount,
                        'currency' => $account->currency ?? 'BDT',
                        'income_category_id' => $incomeCatId,
                        'expense_category_id' => $expenseCatId,
                        'description' => $description,
                        'reference' => $reference,
                        'is_tax_deductible' => $isTaxDeductible,
                        'tax_category' => $taxCategory,
                        'deduction_percentage' => $deductionPct,
                        'tax_notes' => $isTaxDeductible ? 'Auto-flagged from category defaults during import' : null,
                    ]);

                    $imported++;

                } catch (\Throwable $e) {
                    $errors[] = "Row #" . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            $batch->update([
                'imported_count' => $imported,
                'skipped_count' => $skipped,
                'error_count' => count($errors),
                'error_log' => $errors,
            ]);

            return $batch;
        });
    }

    /**
     * Resolve date from varied formats.
     */
    private function parseDate(?string $dateStr, string $formatHint = 'auto'): ?Carbon
    {
        if (!$dateStr) {
            return null;
        }

        $dateStr = trim($dateStr);

        if ($formatHint !== 'auto' && $formatHint !== '') {
            try {
                return Carbon::createFromFormat($formatHint, $dateStr)->startOfDay();
            } catch (\Throwable $e) {}
        }

        $commonFormats = [
            'Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'd.m.Y', 'Y/m/d',
            'Y-m-d H:i:s', 'd/m/Y H:i', 'm/d/Y H:i', 'M d, Y', 'd M Y',
        ];

        foreach ($commonFormats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $dateStr)->startOfDay();
            } catch (\Throwable $e) {}
        }

        try {
            return Carbon::parse($dateStr)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Resolve transaction type and clean amount.
     */
    private function resolveTypeAndAmount(
        ?string $rawAmount,
        ?string $rawDebit,
        ?string $rawCredit,
        ?string $rawType,
        string $defaultType
    ): ?array {
        $cleanNumber = function (?string $str): ?float {
            if ($str === null || trim($str) === '') {
                return null;
            }
            // Strip currency symbols and whitespace
            $cleaned = preg_replace('/[^0-9.\-()]/', '', trim($str));
            // Handle accounting brackets (150.00) => -150.00
            if (str_starts_with($cleaned, '(') && str_ends_with($cleaned, ')')) {
                $cleaned = '-' . trim($cleaned, '()');
            }
            return is_numeric($cleaned) ? (float)$cleaned : null;
        };

        $debitVal = $cleanNumber($rawDebit);
        $creditVal = $cleanNumber($rawCredit);
        $amountVal = $cleanNumber($rawAmount);

        // Case 1: Separate Debit and Credit columns (Standard bank statements)
        if ($debitVal !== null && $debitVal > 0 && ($creditVal === null || $creditVal == 0)) {
            return ['type' => 'expense', 'amount' => abs($debitVal)];
        }
        if ($creditVal !== null && $creditVal > 0 && ($debitVal === null || $debitVal == 0)) {
            return ['type' => 'income', 'amount' => abs($creditVal)];
        }

        // Case 2: Single Amount column with raw type column
        if ($amountVal !== null) {
            $absAmount = abs($amountVal);

            if ($rawType) {
                if (str_contains($rawType, 'exp') || str_contains($rawType, 'out') || str_contains($rawType, 'dr') || str_contains($rawType, 'debit')) {
                    return ['type' => 'expense', 'amount' => $absAmount];
                }
                if (str_contains($rawType, 'inc') || str_contains($rawType, 'in') || str_contains($rawType, 'cr') || str_contains($rawType, 'credit')) {
                    return ['type' => 'income', 'amount' => $absAmount];
                }
            }

            // Case 3: Forced default type
            if ($defaultType === 'expense') {
                return ['type' => 'expense', 'amount' => $absAmount];
            }
            if ($defaultType === 'income') {
                return ['type' => 'income', 'amount' => $absAmount];
            }

            // Case 4: Signed amount (negative = expense, positive = income)
            if ($amountVal < 0) {
                return ['type' => 'expense', 'amount' => $absAmount];
            }
            if ($amountVal > 0) {
                return ['type' => 'income', 'amount' => $absAmount];
            }
        }

        return null;
    }
}
