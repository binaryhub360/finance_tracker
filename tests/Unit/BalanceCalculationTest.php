<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceCalculationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Account $account;
    private IncomeCategory $incomeCategory;
    private ExpenseCategory $expenseCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create([
            'user_id' => $this->user->id,
            'opening_balance' => 10000,
        ]);
        $this->incomeCategory = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->expenseCategory = ExpenseCategory::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_account_balance_with_no_transactions_equals_opening_balance(): void
    {
        $this->assertEquals('10000.00', $this->account->balance);
    }

    public function test_income_increases_balance(): void
    {
        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'income', 'date' => now(),
            'amount' => 5000, 'account_id' => $this->account->id,
            'income_category_id' => $this->incomeCategory->id,
        ]);

        $this->account->refresh();
        $this->assertEquals('15000.00', $this->account->balance);
    }

    public function test_expense_decreases_balance(): void
    {
        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'expense', 'date' => now(),
            'amount' => 3000, 'account_id' => $this->account->id,
            'expense_category_id' => $this->expenseCategory->id,
        ]);

        $this->account->refresh();
        $this->assertEquals('7000.00', $this->account->balance);
    }

    public function test_transfer_in_increases_balance(): void
    {
        $source = Account::factory()->create(['user_id' => $this->user->id, 'opening_balance' => 50000]);

        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'transfer', 'date' => now(),
            'amount' => 5000, 'from_account_id' => $source->id,
            'to_account_id' => $this->account->id,
        ]);

        $this->account->refresh();
        $this->assertEquals('15000.00', $this->account->balance);
    }

    public function test_transfer_out_decreases_balance(): void
    {
        $destination = Account::factory()->create(['user_id' => $this->user->id, 'opening_balance' => 0]);

        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'transfer', 'date' => now(),
            'amount' => 4000, 'from_account_id' => $this->account->id,
            'to_account_id' => $destination->id,
        ]);

        $this->account->refresh();
        $this->assertEquals('6000.00', $this->account->balance);
    }

    public function test_mixed_transactions_calculate_correctly(): void
    {
        // Opening: 10000
        // + Income 20000 = 30000
        // - Expense 5000 = 25000
        // + Transfer In 3000 = 28000
        // - Transfer Out 8000 = 20000

        $other = Account::factory()->create(['user_id' => $this->user->id, 'opening_balance' => 50000]);

        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'income', 'date' => now(),
            'amount' => 20000, 'account_id' => $this->account->id,
            'income_category_id' => $this->incomeCategory->id,
        ]);
        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'expense', 'date' => now(),
            'amount' => 5000, 'account_id' => $this->account->id,
            'expense_category_id' => $this->expenseCategory->id,
        ]);
        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'transfer', 'date' => now(),
            'amount' => 3000, 'from_account_id' => $other->id,
            'to_account_id' => $this->account->id,
        ]);
        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'transfer', 'date' => now(),
            'amount' => 8000, 'from_account_id' => $this->account->id,
            'to_account_id' => $other->id,
        ]);

        $this->account->refresh();
        $this->assertEquals('20000.00', $this->account->balance);
    }
}
