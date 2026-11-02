<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'currency:update';
    protected $description = 'Fetch and update live foreign exchange rates for all active currencies';

    public function handle(ExchangeRateService $service): int
    {
        $this->info('Synchronizing live exchange rates from global forex markets...');

        $result = $service->updateRates();

        if ($result['success']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        } else {
            $this->warn($result['message']);
            return Command::FAILURE;
        }
    }
}
