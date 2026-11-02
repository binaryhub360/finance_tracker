<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\RecurringTransaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecurringTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $today = Carbon::today();
        $sevenDaysAhead = $today->copy()->addDays(7);

        // Fetch all user items to compute KPI cards
        $allItems = RecurringTransaction::where('user_id', $user->id)->get();

        $activeItems = $allItems->where('status', 'active');
        $activeExpenses = $activeItems->where('type', 'expense');
        $activeIncomes = $activeItems->where('type', 'income');

        $monthlyExpenses = $activeExpenses->sum(fn ($i) => $i->monthly_equivalent_amount);
        $yearlyExpenses = round($monthlyExpenses * 12, 2);
        $monthlyIncome = $activeIncomes->sum(fn ($i) => $i->monthly_equivalent_amount);
        $netMonthly = round($monthlyIncome - $monthlyExpenses, 2);

        $dueThisWeekItems = $activeItems->filter(function ($i) use ($today, $sevenDaysAhead) {
            if (!$i->next_due_date) return false;
            $due = Carbon::parse($i->next_due_date)->startOfDay();
            return $due->gte($today) && $due->lte($sevenDaysAhead);
        });
        $dueThisWeekCount = $dueThisWeekItems->count();
        $dueThisWeekAmount = $dueThisWeekItems->sum('amount');

        $overdueItems = $activeItems->filter(function ($i) use ($today) {
            if (!$i->next_due_date) return false;
            return Carbon::parse($i->next_due_date)->startOfDay()->lt($today);
        });
        $overdueCount = $overdueItems->count();
        $overdueAmount = $overdueItems->sum('amount');

        $activeCount = $activeItems->count();
        $pausedCount = $allItems->where('status', 'paused')->count();
        $cancelledCount = $allItems->where('status', 'cancelled')->count();

        // Build filtered query for the listing table/cards
        $query = RecurringTransaction::where('user_id', $user->id)
            ->with(['account', 'expenseCategory', 'incomeCategory']);

        if ($request->filled('type') && in_array($request->type, ['expense', 'income'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'paused', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('due')) {
            if ($request->due === 'overdue') {
                $query->where('status', 'active')->where('next_due_date', '<', $today->format('Y-m-d'));
            } elseif ($request->due === 'today') {
                $query->where('status', 'active')->where('next_due_date', $today->format('Y-m-d'));
            } elseif ($request->due === 'this_week') {
                $query->where('status', 'active')
                    ->whereBetween('next_due_date', [$today->format('Y-m-d'), $sevenDaysAhead->format('Y-m-d')]);
            } elseif ($request->due === 'this_month') {
                $query->where('status', 'active')
                    ->whereBetween('next_due_date', [$today->copy()->startOfMonth()->format('Y-m-d'), $today->copy()->endOfMonth()->format('Y-m-d')]);
            }
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        $recurring = $query->orderBy('next_due_date', 'asc')->paginate(12)->withQueryString();

        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->active()->ordered()->get();
        $incomeCategories = IncomeCategory::where('user_id', $user->id)->active()->ordered()->get();

        return view('recurring.index', compact(
            'recurring',
            'accounts',
            'expenseCategories',
            'incomeCategories',
            'monthlyExpenses',
            'yearlyExpenses',
            'monthlyIncome',
            'netMonthly',
            'dueThisWeekCount',
            'dueThisWeekAmount',
            'overdueCount',
            'overdueAmount',
            'activeCount',
            'pausedCount',
            'cancelledCount'
        ));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->active()->ordered()->get();
        $incomeCategories = IncomeCategory::where('user_id', $user->id)->active()->ordered()->get();
        $currency = currency_symbol();

        return view('recurring.create', compact('accounts', 'expenseCategories', 'incomeCategories', 'currency'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:expense,income',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|max:10',
            'account_id' => 'required|exists:accounts,id',
            'expense_category_id' => 'nullable|required_if:type,expense|exists:expense_categories,id',
            'income_category_id' => 'nullable|required_if:type,income|exists:income_categories,id',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'interval' => 'required|integer|min:1|max:365',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'next_due_date' => 'nullable|date',
            'status' => 'required|in:active,paused',
            'auto_log' => 'nullable|boolean',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify account belongs to user
        Account::where('user_id', $user->id)->findOrFail($validated['account_id']);

        $nextDue = $validated['next_due_date'] ?: $validated['start_date'];

        $recurring = RecurringTransaction::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?: currency_symbol(),
            'account_id' => $validated['account_id'],
            'expense_category_id' => $validated['type'] === 'expense' ? $validated['expense_category_id'] : null,
            'income_category_id' => $validated['type'] === 'income' ? $validated['income_category_id'] : null,
            'frequency' => $validated['frequency'],
            'interval' => $validated['interval'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'next_due_date' => $nextDue,
            'status' => $validated['status'],
            'auto_log' => $request->boolean('auto_log', false),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('recurring.show', $recurring)
            ->with('success', "Recurring {$recurring->type} '{$recurring->name}' created successfully.");
    }

    public function show(Request $request, RecurringTransaction $recurring): View
    {
        abort_if($recurring->user_id !== $request->user()->id, 403);

        $recurring->load(['account', 'expenseCategory', 'incomeCategory']);
        $transactions = $recurring->transactions()->latest('date')->latest('id')->paginate(10);

        return view('recurring.show', compact('recurring', 'transactions'));
    }

    public function edit(Request $request, RecurringTransaction $recurring): View
    {
        abort_if($recurring->user_id !== $request->user()->id, 403);

        $user = $request->user();
        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->active()->ordered()->get();
        $incomeCategories = IncomeCategory::where('user_id', $user->id)->active()->ordered()->get();

        return view('recurring.edit', compact('recurring', 'accounts', 'expenseCategories', 'incomeCategories'));
    }

    public function update(Request $request, RecurringTransaction $recurring): RedirectResponse
    {
        abort_if($recurring->user_id !== $request->user()->id, 403);

        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:expense,income',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|max:10',
            'account_id' => 'required|exists:accounts,id',
            'expense_category_id' => 'nullable|required_if:type,expense|exists:expense_categories,id',
            'income_category_id' => 'nullable|required_if:type,income|exists:income_categories,id',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'interval' => 'required|integer|min:1|max:365',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'next_due_date' => 'required|date',
            'status' => 'required|in:active,paused,cancelled',
            'auto_log' => 'nullable|boolean',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        Account::where('user_id', $user->id)->findOrFail($validated['account_id']);

        $recurring->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?: $recurring->currency,
            'account_id' => $validated['account_id'],
            'expense_category_id' => $validated['type'] === 'expense' ? $validated['expense_category_id'] : null,
            'income_category_id' => $validated['type'] === 'income' ? $validated['income_category_id'] : null,
            'frequency' => $validated['frequency'],
            'interval' => $validated['interval'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'next_due_date' => $validated['next_due_date'],
            'status' => $validated['status'],
            'auto_log' => $request->boolean('auto_log', false),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('recurring.show', $recurring)
            ->with('success', 'Recurring transaction updated successfully.');
    }

    public function destroy(Request $request, RecurringTransaction $recurring): RedirectResponse
    {
        abort_if($recurring->user_id !== $request->user()->id, 403);

        $name = $recurring->name;
        $recurring->delete();

        return redirect()->route('recurring.index')
            ->with('success', "Recurring subscription '{$name}' deleted.");
    }

    public function logOccurrence(Request $request, RecurringTransaction $recurring): RedirectResponse
    {
        abort_if($recurring->user_id !== $request->user()->id, 403);

        $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = $request->input('date');
        $transaction = $recurring->logOccurrence($date);

        return back()->with('success', "Logged payment for '{$recurring->name}' (" . format_currency($transaction->amount) . ") and reconciled into {$recurring->account?->name}. Next due: {$recurring->next_due_date->format('M d, Y')}");
    }

    public function skip(Request $request, RecurringTransaction $recurring): RedirectResponse
    {
        abort_if($recurring->user_id !== $request->user()->id, 403);

        $oldDue = $recurring->next_due_date->format('M d, Y');
        $recurring->skipNextDueDate();
        $newDue = $recurring->next_due_date->format('M d, Y');

        return back()->with('success', "Skipped cycle ({$oldDue}). Next renewal updated to {$newDue}.");
    }

    public function togglePause(Request $request, RecurringTransaction $recurring): RedirectResponse
    {
        abort_if($recurring->user_id !== $request->user()->id, 403);

        $newStatus = $recurring->status === 'active' ? 'paused' : 'active';
        $recurring->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'resumed' : 'paused';

        return back()->with('success', "'{$recurring->name}' has been {$statusLabel}.");
    }

    public function processDue(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = Carbon::today()->format('Y-m-d');

        $dueItems = RecurringTransaction::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('next_due_date', '<=', $today)
            ->get();

        if ($dueItems->isEmpty()) {
            return back()->with('info', 'No recurring transactions are currently due.');
        }

        $count = 0;
        foreach ($dueItems as $item) {
            $item->logOccurrence();
            $count++;
        }

        return back()->with('success', "Successfully processed and logged {$count} due recurring transaction(s).");
    }
}
