<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'recurring:process {--all : Process all due items regardless of auto_log setting}';

    protected $description = 'Process due recurring transactions and record them into the financial ledger';

    public function handle(): int
    {
        $today = Carbon::today()->format('Y-m-d');
        $processAll = $this->option('all');

        $query = RecurringTransaction::where('status', 'active')
            ->where('next_due_date', '<=', $today);

        if (!$processAll) {
            $query->where('auto_log', true);
        }

        $items = $query->with(['user', 'account'])->get();

        if ($items->isEmpty()) {
            $this->info('No recurring transactions are due for processing today.');
            return Command::SUCCESS;
        }

        $this->info("Found {$items->count()} due recurring transaction(s). Processing...");

        $successCount = 0;
        foreach ($items as $item) {
            try {
                $transaction = $item->logOccurrence();
                $this->line("  [OK] Processed '{$item->name}' for {$item->user->name} ({$transaction->amount} {$item->currency}) -> Next due: {$item->next_due_date->format('Y-m-d')}");
                $successCount++;
            } catch (\Throwable $e) {
                $this->error("  [FAILED] Failed processing '{$item->name}' (ID: {$item->id}): {$e->getMessage()}");
            }
        }

        $this->info("Completed. Successfully recorded {$successCount} transaction(s).");

        return Command::SUCCESS;
    }
}
