<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeCategoryRequest;
use App\Models\IncomeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = IncomeCategory::where('user_id', $request->user()->id)
            ->ordered()
            ->withCount('transactions')
            ->get();

        return view('income-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('income-categories.create');
    }

    public function store(StoreIncomeCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        IncomeCategory::create($data);

        return redirect()->route('income-categories.index')
            ->with('success', 'Income category created successfully.');
    }

    public function edit(IncomeCategory $incomeCategory): View
    {
        return view('income-categories.edit', ['category' => $incomeCategory]);
    }

    public function update(StoreIncomeCategoryRequest $request, IncomeCategory $incomeCategory): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $incomeCategory->update($data);

        return redirect()->route('income-categories.index')
            ->with('success', 'Income category updated successfully.');
    }

    public function toggle(IncomeCategory $incomeCategory): RedirectResponse
    {
        $incomeCategory->update(['is_active' => !$incomeCategory->is_active]);

        $status = $incomeCategory->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Income category {$status} successfully.");
    }

    public function destroy(IncomeCategory $incomeCategory): RedirectResponse
    {
        if ($incomeCategory->transactions()->exists()) {
            return back()->with('error', 'Cannot delete this category because it has transactions. Consider deactivating it instead.');
        }

        $incomeCategory->delete();

        return redirect()->route('income-categories.index')
            ->with('success', 'Income category deleted successfully.');
    }
}
