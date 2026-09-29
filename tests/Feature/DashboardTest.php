<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_dashboard_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_dashboard_shows_correct_totals(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'opening_balance' => 0]);
        $incCat = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $expCat = ExpenseCategory::factory()->create(['user_id' => $this->user->id]);

        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'income', 'date' => now(),
            'amount' => 50000, 'account_id' => $account->id, 'income_category_id' => $incCat->id,
        ]);
        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'expense', 'date' => now(),
            'amount' => 20000, 'account_id' => $account->id, 'expense_category_id' => $expCat->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard', ['period' => 'this_month']));
        $response->assertStatus(200);
        $response->assertSee('50,000.00');
        $response->assertSee('20,000.00');
    }

    public function test_dashboard_date_filtering_works(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'opening_balance' => 0]);
        $incCat = IncomeCategory::factory()->create(['user_id' => $this->user->id]);

        // Transaction last month
        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'income',
            'date' => now()->subMonth()->startOfMonth()->addDay(),
            'amount' => 99999, 'account_id' => $account->id, 'income_category_id' => $incCat->id,
        ]);

        // This month view should not show last month's transaction in totals
        $response = $this->actingAs($this->user)->get(route('dashboard', ['period' => 'this_month']));
        $response->assertStatus(200);
        $response->assertDontSee('99,999.00');
    }
}
