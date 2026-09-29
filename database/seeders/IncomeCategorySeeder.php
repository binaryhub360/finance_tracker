<?php

namespace Database\Seeders;

use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class IncomeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $categories = [
            ['name' => 'Salary', 'sort_order' => 1],
            ['name' => 'Freelance', 'sort_order' => 2],
            ['name' => 'Bonus', 'sort_order' => 3],
            ['name' => 'Interest', 'sort_order' => 4],
            ['name' => 'Other Income', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            IncomeCategory::create(array_merge($category, [
                'user_id' => $user->id,
                'is_active' => true,
            ]));
        }
    }
}
