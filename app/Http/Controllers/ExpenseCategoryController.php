<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ExpenseCategory::where('user_id', $request->user()->id)
            ->ordered()
            ->withCount('transactions')
            ->get();

        return view('expense-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('expense-categories.create');
    }

    public function store(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        ExpenseCategory::create($data);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory): View
    {
        return view('expense-categories.edit', ['category' => $expenseCategory]);
    }

    public function update(StoreExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $expenseCategory->update($data);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function toggle(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->update(['is_active' => !$expenseCategory->is_active]);

        $status = $expenseCategory->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Expense category {$status} successfully.");
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        if ($expenseCategory->transactions()->exists()) {
            return back()->with('error', 'Cannot delete this category because it has transactions. Consider deactivating it instead.');
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }
}
