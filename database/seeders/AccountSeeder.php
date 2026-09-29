<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $accounts = [
            ['name' => 'Cash', 'type' => 'cash', 'opening_balance' => 5000, 'sort_order' => 1],
            ['name' => 'Main Bank Account', 'type' => 'bank', 'opening_balance' => 50000, 'sort_order' => 2],
            ['name' => 'Savings Account', 'type' => 'bank', 'opening_balance' => 200000, 'sort_order' => 3],
            ['name' => 'bKash', 'type' => 'mobile_wallet', 'opening_balance' => 2000, 'sort_order' => 4],
        ];

        foreach ($accounts as $account) {
            Account::create(array_merge($account, [
                'user_id' => $user->id,
                'opening_balance_date' => now()->startOfMonth(),
                'is_active' => true,
            ]));
        }
    }
}
