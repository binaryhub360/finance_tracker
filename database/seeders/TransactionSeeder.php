<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $accounts = Account::where('user_id', $user->id)->get();
        $incomeCategories = IncomeCategory::where('user_id', $user->id)->get();
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->get();

        $bank = $accounts->firstWhere('name', 'Main Bank Account');
        $cash = $accounts->firstWhere('name', 'Cash');
        $savings = $accounts->firstWhere('name', 'Savings Account');
        $bkash = $accounts->firstWhere('name', 'bKash');

        $salary = $incomeCategories->firstWhere('name', 'Salary');
        $freelance = $incomeCategories->firstWhere('name', 'Freelance');
        $interest = $incomeCategories->firstWhere('name', 'Interest');

        $food = $expenseCategories->firstWhere('name', 'Food');
        $rent = $expenseCategories->firstWhere('name', 'Rent');
        $utilities = $expenseCategories->firstWhere('name', 'Utilities');
        $transport = $expenseCategories->firstWhere('name', 'Transportation');
        $shopping = $expenseCategories->firstWhere('name', 'Shopping');
        $family = $expenseCategories->firstWhere('name', 'Family');
        $medical = $expenseCategories->firstWhere('name', 'Medical');

        // Current month transactions
        $currentMonth = now()->startOfMonth();

        $transactions = [
            // Income
            ['type' => 'income', 'date' => $currentMonth->copy()->addDays(0), 'amount' => 100000, 'account_id' => $bank->id, 'income_category_id' => $salary->id, 'description' => 'Monthly salary'],
            ['type' => 'income', 'date' => $currentMonth->copy()->addDays(5), 'amount' => 25000, 'account_id' => $bank->id, 'income_category_id' => $freelance->id, 'description' => 'Web development project'],
            ['type' => 'income', 'date' => $currentMonth->copy()->addDays(10), 'amount' => 500, 'account_id' => $savings->id, 'income_category_id' => $interest->id, 'description' => 'Monthly interest'],

            // Expenses
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(1), 'amount' => 25000, 'account_id' => $bank->id, 'expense_category_id' => $rent->id, 'description' => 'Monthly rent'],
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(2), 'amount' => 3500, 'account_id' => $bank->id, 'expense_category_id' => $utilities->id, 'description' => 'Electricity bill'],
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(3), 'amount' => 1200, 'account_id' => $cash->id, 'expense_category_id' => $food->id, 'description' => 'Groceries'],
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(4), 'amount' => 500, 'account_id' => $cash->id, 'expense_category_id' => $transport->id, 'description' => 'Rickshaw and CNG'],
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(6), 'amount' => 8000, 'account_id' => $bank->id, 'expense_category_id' => $shopping->id, 'description' => 'New headphones'],
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(7), 'amount' => 5000, 'account_id' => $bkash->id, 'expense_category_id' => $family->id, 'description' => 'Sent to parents'],
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(8), 'amount' => 2500, 'account_id' => $cash->id, 'expense_category_id' => $medical->id, 'description' => 'Doctor visit'],
            ['type' => 'expense', 'date' => $currentMonth->copy()->addDays(9), 'amount' => 800, 'account_id' => $cash->id, 'expense_category_id' => $food->id, 'description' => 'Restaurant lunch'],

            // Transfers
            ['type' => 'transfer', 'date' => $currentMonth->copy()->addDays(1), 'amount' => 10000, 'from_account_id' => $bank->id, 'to_account_id' => $cash->id, 'description' => 'Cash withdrawal'],
            ['type' => 'transfer', 'date' => $currentMonth->copy()->addDays(5), 'amount' => 5000, 'from_account_id' => $bank->id, 'to_account_id' => $bkash->id, 'description' => 'Top up bKash'],
        ];

        // Last month transactions
        $lastMonth = now()->subMonth()->startOfMonth();

        $lastMonthTransactions = [
            ['type' => 'income', 'date' => $lastMonth->copy()->addDays(0), 'amount' => 100000, 'account_id' => $bank->id, 'income_category_id' => $salary->id, 'description' => 'Monthly salary'],
            ['type' => 'income', 'date' => $lastMonth->copy()->addDays(10), 'amount' => 15000, 'account_id' => $bank->id, 'income_category_id' => $freelance->id, 'description' => 'Logo design project'],
            ['type' => 'expense', 'date' => $lastMonth->copy()->addDays(1), 'amount' => 25000, 'account_id' => $bank->id, 'expense_category_id' => $rent->id, 'description' => 'Monthly rent'],
            ['type' => 'expense', 'date' => $lastMonth->copy()->addDays(2), 'amount' => 3000, 'account_id' => $bank->id, 'expense_category_id' => $utilities->id, 'description' => 'Electricity bill'],
            ['type' => 'expense', 'date' => $lastMonth->copy()->addDays(5), 'amount' => 6000, 'account_id' => $cash->id, 'expense_category_id' => $food->id, 'description' => 'Weekly groceries'],
            ['type' => 'expense', 'date' => $lastMonth->copy()->addDays(8), 'amount' => 2000, 'account_id' => $cash->id, 'expense_category_id' => $transport->id, 'description' => 'Uber rides'],
            ['type' => 'transfer', 'date' => $lastMonth->copy()->addDays(0), 'amount' => 15000, 'from_account_id' => $bank->id, 'to_account_id' => $cash->id, 'description' => 'Cash withdrawal'],
        ];

        foreach (array_merge($transactions, $lastMonthTransactions) as $txn) {
            Transaction::create(array_merge($txn, [
                'user_id' => $user->id,
            ]));
        }
    }
}
