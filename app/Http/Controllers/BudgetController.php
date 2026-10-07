<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        try {
            $currentMonthCarbon = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            $currentMonthCarbon = Carbon::now()->startOfMonth();
            $month = $currentMonthCarbon->format('Y-m');
        }

        $startDate = $currentMonthCarbon->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $currentMonthCarbon->copy()->endOfMonth()->format('Y-m-d');

        $prevMonth = $currentMonthCarbon->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonthCarbon->copy()->addMonth()->format('Y-m');
        $monthLabel = $currentMonthCarbon->format('F Y');

        $budgets = Budget::where('user_id', $user->id)
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->with('category')
            ->get();

        // Calculate KPI summaries
        $totalBudgeted = (float) $budgets->sum('amount');
        $totalSpent = 0;
        $onTrackCount = 0;
        $warningCount = 0;
        $overBudgetCount = 0;

        foreach ($budgets as $b) {
            $spent = $b->spent_amount;
            $totalSpent += $spent;
            if ($b->is_over_budget) {
                $overBudgetCount++;
            } elseif ($b->is_near_limit) {
                $warningCount++;
            } else {
                $onTrackCount++;
            }
        }

        $overallRemaining = max(0, round($totalBudgeted - $totalSpent, 2));
        $overallOverrun = max(0, round($totalSpent - $totalBudgeted, 2));
        $overallPercentage = $totalBudgeted > 0 ? min(100, round(($totalSpent / $totalBudgeted) * 100, 1)) : 0;

        // Check if there are previous month budgets available to copy
        $prevStartDate = $currentMonthCarbon->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $prevEndDate = $currentMonthCarbon->copy()->subMonth()->endOfMonth()->format('Y-m-d');
        $hasPreviousBudgets = Budget::where('user_id', $user->id)
            ->where('start_date', $prevStartDate)
            ->where('end_date', $prevEndDate)
            ->exists();

        // Find expense categories not yet budgeted for this month
        $budgetedCategoryIds = $budgets->pluck('expense_category_id')->toArray();
        $unbudgetedCategories = ExpenseCategory::where('user_id', $user->id)
            ->active()
            ->whereNotIn('id', $budgetedCategoryIds)
            ->ordered()
            ->get();

        // Also check if there's spending in unbudgeted categories
        $unbudgetedSpending = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->where(function ($q) use ($budgetedCategoryIds) {
                $q->whereNull('expense_category_id')
                    ->orWhereNotIn('expense_category_id', $budgetedCategoryIds);
            })
            ->sum('amount');

        return view('budgets.index', compact(
            'budgets',
            'month',
            'monthLabel',
            'prevMonth',
            'nextMonth',
            'totalBudgeted',
            'totalSpent',
            'overallRemaining',
            'overallOverrun',
            'overallPercentage',
            'onTrackCount',
            'warningCount',
            'overBudgetCount',
            'hasPreviousBudgets',
            'unbudgetedCategories',
            'unbudgetedSpending'
        ));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        try {
            $monthCarbon = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            $monthCarbon = Carbon::now()->startOfMonth();
            $month = $monthCarbon->format('Y-m');
        }

        $startDate = $monthCarbon->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $monthCarbon->copy()->endOfMonth()->format('Y-m-d');

        $budgetedCategoryIds = Budget::where('user_id', $user->id)
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->pluck('expense_category_id')
            ->toArray();

        $categories = ExpenseCategory::where('user_id', $user->id)
            ->active()
            ->whereNotIn('id', $budgetedCategoryIds)
            ->ordered()
            ->get();

        // Pre-compute 3-month average spending per category to offer smart budgeting recommendations
        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d');
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');

        $recommendations = [];
        foreach ($categories as $cat) {
            $pastSpent = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->where('expense_category_id', $cat->id)
                ->whereBetween('date', [$threeMonthsAgo, $lastMonthEnd])
                ->sum('amount');
            $recommendations[$cat->id] = round($pastSpent / 3, 2);
        }

        return view('budgets.create', compact('categories', 'month', 'recommendations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|date_format:Y-m',
            'alert_threshold' => 'required|integer|min:1|max:100',
            'notify_overrun' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $monthCarbon = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $startDate = $monthCarbon->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $monthCarbon->copy()->endOfMonth()->format('Y-m-d');

        // Check if category belongs to user
        $category = ExpenseCategory::where('user_id', $user->id)->findOrFail($validated['expense_category_id']);

        // Check uniqueness
        $exists = Budget::where('user_id', $user->id)
            ->where('expense_category_id', $category->id)
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'A budget for this category and month already exists.');
        }

        Budget::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => $validated['amount'],
            'period' => 'monthly',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'alert_threshold' => $validated['alert_threshold'],
            'notify_overrun' => $request->boolean('notify_overrun', true),
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('budgets.index', ['month' => $validated['month']])
            ->with('success', "Budget for {$category->name} set to " . currency_symbol() . number_format($validated['amount'], 2));
    }

    public function edit(Request $request, Budget $budget): View
    {
        abort_if($budget->user_id !== $request->user()->id, 403);
        $budget->load('category');

        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'alert_threshold' => 'required|integer|min:1|max:100',
            'notify_overrun' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $budget->update([
            'amount' => $validated['amount'],
            'alert_threshold' => $validated['alert_threshold'],
            'notify_overrun' => $request->boolean('notify_overrun', true),
            'notes' => $validated['notes'],
        ]);

        $month = $budget->start_date->format('Y-m');

        return redirect()->route('budgets.index', ['month' => $month])
            ->with('success', 'Budget target updated successfully.');
    }

    public function destroy(Request $request, Budget $budget): RedirectResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403);

        $month = $budget->start_date->format('Y-m');
        $catName = $budget->category ? $budget->category->name : 'Category';
        $budget->delete();

        return redirect()->route('budgets.index', ['month' => $month])
            ->with('success', "Budget for {$catName} deleted.");
    }

    public function copyPrevious(Request $request): RedirectResponse
    {
        $user = $request->user();
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        $currentMonthCarbon = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $currStart = $currentMonthCarbon->copy()->startOfMonth()->format('Y-m-d');
        $currEnd = $currentMonthCarbon->copy()->endOfMonth()->format('Y-m-d');

        $prevMonthCarbon = $currentMonthCarbon->copy()->subMonth();
        $prevStart = $prevMonthCarbon->copy()->startOfMonth()->format('Y-m-d');
        $prevEnd = $prevMonthCarbon->copy()->endOfMonth()->format('Y-m-d');

        $prevBudgets = Budget::where('user_id', $user->id)
            ->where('start_date', $prevStart)
            ->where('end_date', $prevEnd)
            ->get();

        if ($prevBudgets->isEmpty()) {
            return back()->with('error', 'No budgets found in the previous month to copy.');
        }

        $copiedCount = 0;
        foreach ($prevBudgets as $prev) {
            $exists = Budget::where('user_id', $user->id)
                ->where('expense_category_id', $prev->expense_category_id)
                ->where('start_date', $currStart)
                ->where('end_date', $currEnd)
                ->exists();

            if (!$exists) {
                Budget::create([
                    'user_id' => $user->id,
                    'expense_category_id' => $prev->expense_category_id,
                    'amount' => $prev->amount,
                    'period' => 'monthly',
                    'start_date' => $currStart,
                    'end_date' => $currEnd,
                    'alert_threshold' => $prev->alert_threshold,
                    'notify_overrun' => $prev->notify_overrun,
                    'notes' => $prev->notes,
                ]);
                $copiedCount++;
            }
        }

        return redirect()->route('budgets.index', ['month' => $month])
            ->with('success', "Copied {$copiedCount} budget target(s) from " . $prevMonthCarbon->format('F Y') . ".");
    }
}
