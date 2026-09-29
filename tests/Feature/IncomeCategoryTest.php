<?php

namespace Tests\Feature;

use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_income_category_index_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('income-categories.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_income_category(): void
    {
        $response = $this->actingAs($this->user)->post(route('income-categories.store'), [
            'name' => 'Salary',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('income-categories.index'));
        $this->assertDatabaseHas('income_categories', ['name' => 'Salary', 'user_id' => $this->user->id]);
    }

    public function test_user_can_update_income_category(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id, 'name' => 'Old']);

        $this->actingAs($this->user)->put(route('income-categories.update', $category), [
            'name' => 'New Name',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('income_categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    public function test_category_name_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('income-categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
