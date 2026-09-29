<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_expense_category_index_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('expense-categories.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_expense_category(): void
    {
        $response = $this->actingAs($this->user)->post(route('expense-categories.store'), [
            'name' => 'Food',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('expense-categories.index'));
        $this->assertDatabaseHas('expense_categories', ['name' => 'Food', 'user_id' => $this->user->id]);
    }

    public function test_user_can_update_expense_category(): void
    {
        $category = ExpenseCategory::factory()->create(['user_id' => $this->user->id, 'name' => 'Old']);

        $this->actingAs($this->user)->put(route('expense-categories.update', $category), [
            'name' => 'New Name',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('expense_categories', ['id' => $category->id, 'name' => 'New Name']);
    }
}
