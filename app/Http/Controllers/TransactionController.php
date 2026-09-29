<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Transaction::forUser($user->id)
            ->with(['account', 'incomeCategory', 'expenseCategory', 'fromAccount', 'toAccount']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by account
        if ($request->filled('account_id')) {
            $accountId = $request->account_id;
            $query->where(function ($q) use ($accountId) {
                $q->where('account_id', $accountId)
                    ->orWhere('from_account_id', $accountId)
                    ->orWhere('to_account_id', $accountId);
            });
        }

        // Filter by category
        if ($request->filled('category_id') && $request->filled('category_type')) {
            if ($request->category_type === 'income') {
                $query->where('income_category_id', $request->category_id);
            } else {
                $query->where('expense_category_id', $request->category_id);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('date')->latest('id')->paginate(20)->withQueryString();

        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $incomeCategories = IncomeCategory::where('user_id', $user->id)->active()->ordered()->get();
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->active()->ordered()->get();

        return view('transactions.index', compact(
            'transactions',
            'accounts',
            'incomeCategories',
            'expenseCategories',
        ));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $incomeCategories = IncomeCategory::where('user_id', $user->id)->active()->ordered()->get();
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->active()->ordered()->get();

        return view('transactions.create', compact('accounts', 'incomeCategories', 'expenseCategories'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        // Clear irrelevant fields based on type
        $data = $this->sanitizeByType($data);

        Transaction::create($data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['account', 'incomeCategory', 'expenseCategory', 'fromAccount', 'toAccount']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        $user = $request->user();
        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $incomeCategories = IncomeCategory::where('user_id', $user->id)->active()->ordered()->get();
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->active()->ordered()->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'incomeCategories', 'expenseCategories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validated();

        // Clear irrelevant fields based on type
        $data = $this->sanitizeByType($data);

        $transaction->update($data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Clear fields that don't belong to the transaction type.
     */
    private function sanitizeByType(array $data): array
    {
        $type = $data['type'];

        if ($type === 'income') {
            $data['expense_category_id'] = null;
            $data['from_account_id'] = null;
            $data['to_account_id'] = null;
        } elseif ($type === 'expense') {
            $data['income_category_id'] = null;
            $data['from_account_id'] = null;
            $data['to_account_id'] = null;
        } elseif ($type === 'transfer') {
            $data['account_id'] = null;
            $data['income_category_id'] = null;
            $data['expense_category_id'] = null;
        }

        return $data;
    }
}
