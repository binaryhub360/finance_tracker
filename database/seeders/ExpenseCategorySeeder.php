<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $categories = [
            ['name' => 'Food', 'sort_order' => 1],
            ['name' => 'Rent', 'sort_order' => 2],
            ['name' => 'Utilities', 'sort_order' => 3],
            ['name' => 'Transportation', 'sort_order' => 4],
            ['name' => 'Shopping', 'sort_order' => 5],
            ['name' => 'Family', 'sort_order' => 6],
            ['name' => 'Education', 'sort_order' => 7],
            ['name' => 'Medical', 'sort_order' => 8],
            ['name' => 'Entertainment', 'sort_order' => 9],
            ['name' => 'Other', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create(array_merge($category, [
                'user_id' => $user->id,
                'is_active' => true,
            ]));
        }
    }
}
