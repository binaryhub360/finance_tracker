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
        $taxCategories = \App\Services\TaxService::CATEGORIES;
        return view('expense-categories.create', compact('taxCategories'));
    }

    public function store(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_tax_deductible_default'] = $request->boolean('is_tax_deductible_default');
        $data['default_tax_category'] = $data['is_tax_deductible_default'] ? ($data['default_tax_category'] ?? null) : null;
        $data['default_deduction_percentage'] = $data['is_tax_deductible_default'] ? ($data['default_deduction_percentage'] ?? 100.00) : 100.00;

        ExpenseCategory::create($data);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory): View
    {
        $taxCategories = \App\Services\TaxService::CATEGORIES;
        return view('expense-categories.edit', ['category' => $expenseCategory, 'taxCategories' => $taxCategories]);
    }

    public function update(StoreExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_tax_deductible_default'] = $request->boolean('is_tax_deductible_default');
        $data['default_tax_category'] = $data['is_tax_deductible_default'] ? ($data['default_tax_category'] ?? null) : null;
        $data['default_deduction_percentage'] = $data['is_tax_deductible_default'] ? ($data['default_deduction_percentage'] ?? 100.00) : 100.00;

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
