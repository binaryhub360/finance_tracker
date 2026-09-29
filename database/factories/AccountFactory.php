<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company(),
            'type' => fake()->randomElement(['cash', 'bank', 'mobile_wallet', 'credit_card', 'other']),
            'opening_balance' => fake()->randomFloat(2, 0, 100000),
            'opening_balance_date' => fake()->date(),
            'is_active' => true,
            'notes' => fake()->optional()->sentence(),
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
