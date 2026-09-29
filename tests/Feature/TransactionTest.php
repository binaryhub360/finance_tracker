<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Account $bankAccount;
    private Account $cashAccount;
    private IncomeCategory $salaryCategory;
    private ExpenseCategory $foodCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->bankAccount = Account::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Bank',
            'type' => 'bank',
            'opening_balance' => 50000,
        ]);
        $this->cashAccount = Account::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Cash',
            'type' => 'cash',
            'opening_balance' => 5000,
        ]);
        $this->salaryCategory = IncomeCategory::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Salary',
        ]);
        $this->foodCategory = ExpenseCategory::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Food',
        ]);
    }

    public function test_transaction_index_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('transactions.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_income_transaction(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'date' => '2024-06-15',
            'amount' => 100000,
            'account_id' => $this->bankAccount->id,
            'income_category_id' => $this->salaryCategory->id,
            'description' => 'Monthly salary',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'type' => 'income',
            'amount' => 100000,
            'account_id' => $this->bankAccount->id,
            'income_category_id' => $this->salaryCategory->id,
        ]);
    }

    public function test_income_increases_account_balance(): void
    {
        Transaction::create([
            'user_id' => $this->user->id,
            'type' => 'income',
            'date' => '2024-06-15',
            'amount' => 20000,
            'account_id' => $this->bankAccount->id,
            'income_category_id' => $this->salaryCategory->id,
        ]);

        $this->bankAccount->refresh();
        $this->assertEquals('70000.00', $this->bankAccount->balance);
    }

    public function test_user_can_create_expense_transaction(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'expense',
            'date' => '2024-06-15',
            'amount' => 1500,
            'account_id' => $this->cashAccount->id,
            'expense_category_id' => $this->foodCategory->id,
            'description' => 'Groceries',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'type' => 'expense',
            'amount' => 1500,
        ]);
    }

    public function test_expense_decreases_account_balance(): void
    {
        Transaction::create([
            'user_id' => $this->user->id,
            'type' => 'expense',
            'date' => '2024-06-15',
            'amount' => 10000,
            'account_id' => $this->bankAccount->id,
            'expense_category_id' => $this->foodCategory->id,
        ]);

        $this->bankAccount->refresh();
        $this->assertEquals('40000.00', $this->bankAccount->balance);
    }

    public function test_user_can_create_transfer(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'transfer',
            'date' => '2024-06-15',
            'amount' => 10000,
            'from_account_id' => $this->bankAccount->id,
            'to_account_id' => $this->cashAccount->id,
            'description' => 'ATM withdrawal',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'type' => 'transfer',
            'amount' => 10000,
            'from_account_id' => $this->bankAccount->id,
            'to_account_id' => $this->cashAccount->id,
        ]);
    }

    public function test_transfer_decreases_source_and_increases_destination(): void
    {
        Transaction::create([
            'user_id' => $this->user->id,
            'type' => 'transfer',
            'date' => '2024-06-15',
            'amount' => 10000,
            'from_account_id' => $this->bankAccount->id,
            'to_account_id' => $this->cashAccount->id,
        ]);

        $this->bankAccount->refresh();
        $this->cashAccount->refresh();

        $this->assertEquals('40000.00', $this->bankAccount->balance);
        $this->assertEquals('15000.00', $this->cashAccount->balance);
    }

    public function test_transfer_does_not_affect_total_balance(): void
    {
        $totalBefore = bcadd($this->bankAccount->balance, $this->cashAccount->balance, 2);

        Transaction::create([
            'user_id' => $this->user->id,
            'type' => 'transfer',
            'date' => '2024-06-15',
            'amount' => 10000,
            'from_account_id' => $this->bankAccount->id,
            'to_account_id' => $this->cashAccount->id,
        ]);

        $this->bankAccount->refresh();
        $this->cashAccount->refresh();

        $totalAfter = bcadd($this->bankAccount->balance, $this->cashAccount->balance, 2);

        $this->assertEquals($totalBefore, $totalAfter);
    }

    public function test_zero_amount_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'date' => '2024-06-15',
            'amount' => 0,
            'account_id' => $this->bankAccount->id,
            'income_category_id' => $this->salaryCategory->id,
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_negative_amount_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'date' => '2024-06-15',
            'amount' => -100,
            'account_id' => $this->bankAccount->id,
            'income_category_id' => $this->salaryCategory->id,
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_transfer_same_account_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'transfer',
            'date' => '2024-06-15',
            'amount' => 5000,
            'from_account_id' => $this->bankAccount->id,
            'to_account_id' => $this->bankAccount->id,
        ]);

        $response->assertSessionHasErrors('from_account_id');
    }

    public function test_user_can_delete_transaction(): void
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'type' => 'expense',
            'date' => '2024-06-15',
            'amount' => 1000,
            'account_id' => $this->cashAccount->id,
            'expense_category_id' => $this->foodCategory->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('transactions.destroy', $transaction));

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }
}
