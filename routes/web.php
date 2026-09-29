<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Application Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Accounts
    Route::resource('accounts', AccountController::class)->except(['show']);
    Route::patch('/accounts/{account}/toggle', [AccountController::class, 'toggle'])->name('accounts.toggle');

    // Income Categories
    Route::resource('income-categories', IncomeCategoryController::class)
        ->except(['show'])
        ->parameters(['income-categories' => 'incomeCategory']);
    Route::patch('/income-categories/{incomeCategory}/toggle', [IncomeCategoryController::class, 'toggle'])
        ->name('income-categories.toggle');

    // Expense Categories
    Route::resource('expense-categories', ExpenseCategoryController::class)
        ->except(['show'])
        ->parameters(['expense-categories' => 'expenseCategory']);
    Route::patch('/expense-categories/{expenseCategory}/toggle', [ExpenseCategoryController::class, 'toggle'])
        ->name('expense-categories.toggle');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/income', [ReportController::class, 'income'])->name('income');
        Route::get('/expense', [ReportController::class, 'expense'])->name('expense');
        Route::get('/transfer', [ReportController::class, 'transfer'])->name('transfer');
        Route::get('/summary', [ReportController::class, 'summary'])->name('summary');
        Route::get('/export/income', [ReportController::class, 'exportIncome'])->name('export.income');
        Route::get('/export/expense', [ReportController::class, 'exportExpense'])->name('export.expense');
        Route::get('/export/transfer', [ReportController::class, 'exportTransfer'])->name('export.transfer');
    });

    // Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});
