<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\RecurringTransactionController;
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

    // Clients
    Route::resource('clients', ClientController::class);
    Route::patch('/clients/{client}/toggle', [ClientController::class, 'toggle'])->name('clients.toggle');

    // Invoices & Client Billing
    Route::resource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->name('invoices.mark-sent');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('/invoices/{invoice}/payments', [InvoicePaymentController::class, 'store'])->name('invoices.payments.store');
    Route::delete('/invoices/{invoice}/payments/{payment}', [InvoicePaymentController::class, 'destroy'])->name('invoices.payments.destroy');

    // Budgets & Spending Limits
    Route::post('/budgets/copy-previous', [BudgetController::class, 'copyPrevious'])->name('budgets.copy-previous');
    Route::resource('budgets', BudgetController::class)->except(['show']);

    // Recurring Transactions & Subscriptions Tracker
    Route::post('/recurring/process-due', [RecurringTransactionController::class, 'processDue'])->name('recurring.process-due');
    Route::post('/recurring/{recurring}/log', [RecurringTransactionController::class, 'logOccurrence'])->name('recurring.log');
    Route::post('/recurring/{recurring}/skip', [RecurringTransactionController::class, 'skip'])->name('recurring.skip');
    Route::patch('/recurring/{recurring}/toggle-pause', [RecurringTransactionController::class, 'togglePause'])->name('recurring.toggle-pause');
    Route::resource('recurring', RecurringTransactionController::class)->parameters(['recurring' => 'recurring']);

    // Savings Vaults & Financial Goals
    Route::post('/goals/{goal}/deposit', [GoalController::class, 'deposit'])->name('goals.deposit');
    Route::post('/goals/{goal}/withdraw', [GoalController::class, 'withdraw'])->name('goals.withdraw');
    Route::patch('/goals/{goal}/toggle-lock', [GoalController::class, 'toggleLock'])->name('goals.toggle-lock');
    Route::resource('goals', GoalController::class);

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
