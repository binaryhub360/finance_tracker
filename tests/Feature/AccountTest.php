<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_account_index_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('accounts.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_account(): void
    {
        $response = $this->actingAs($this->user)->post(route('accounts.store'), [
            'name' => 'Test Bank',
            'type' => 'bank',
            'opening_balance' => 10000,
            'opening_balance_date' => '2024-01-01',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseHas('accounts', [
            'name' => 'Test Bank',
            'type' => 'bank',
            'opening_balance' => 10000,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_update_account(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name']);

        $response = $this->actingAs($this->user)->put(route('accounts.update', $account), [
            'name' => 'New Name',
            'type' => 'bank',
            'opening_balance' => 5000,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'name' => 'New Name']);
    }

    public function test_user_can_toggle_account_status(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'is_active' => true]);

        $this->actingAs($this->user)->patch(route('accounts.toggle', $account));

        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'is_active' => false]);
    }

    public function test_user_can_delete_account_without_transactions(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('accounts.destroy', $account));

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    public function test_account_name_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('accounts.store'), [
            'name' => '',
            'type' => 'bank',
            'opening_balance' => 0,
        ]);

        $response->assertSessionHasErrors('name');
    }
}
