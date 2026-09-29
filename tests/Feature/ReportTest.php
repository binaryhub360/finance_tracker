<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_reports_index_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_income_report_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.income'));
        $response->assertStatus(200);
    }

    public function test_expense_report_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.expense'));
        $response->assertStatus(200);
    }

    public function test_transfer_report_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.transfer'));
        $response->assertStatus(200);
    }

    public function test_summary_report_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.summary'));
        $response->assertStatus(200);
    }

    public function test_income_csv_export(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $cat = IncomeCategory::factory()->create(['user_id' => $this->user->id]);

        Transaction::create([
            'user_id' => $this->user->id, 'type' => 'income', 'date' => now(),
            'amount' => 50000, 'account_id' => $account->id, 'income_category_id' => $cat->id,
            'description' => 'Test income',
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.export.income', ['period' => 'this_month']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv');
        $response->assertSee('50000.00');
    }

    public function test_summary_report_shows_correct_calculations(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'opening_balance' => 10000]);
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

        $response = $this->actingAs($this->user)->get(route('reports.summary', ['period' => 'this_month']));
        $response->assertStatus(200);
        $response->assertSee('50,000.00');
        $response->assertSee('20,000.00');
    }
}
