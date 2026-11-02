<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Goal;
use App\Models\GoalContribution;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $allGoals = Goal::where('user_id', $user->id)
            ->with(['account', 'contributions'])
            ->get();

        $activeGoals = $allGoals->where('status', 'active');
        $completedGoals = $allGoals->where('status', 'completed');
        $pausedGoals = $allGoals->where('status', 'paused');

        $totalVaultBalance = $allGoals->sum(fn ($g) => (float) $g->current_amount);
        $totalTargetAmount = $activeGoals->sum(fn ($g) => (float) $g->target_amount);
        $totalSavedInActive = $activeGoals->sum(fn ($g) => (float) $g->current_amount);

        $overallProgress = $totalTargetAmount > 0
            ? round(min(100, ($totalSavedInActive / $totalTargetAmount) * 100), 1)
            : 0.0;

        // Query with filters
        $query = Goal::where('user_id', $user->id)
            ->with(['account'])
            ->withCount('contributions');

        if ($request->filled('status') && in_array($request->status, ['active', 'completed', 'paused', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('color')) {
            $query->where('color', $request->color);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $goals = $query->orderByRaw("CASE WHEN status = 'active' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            ->orderBy('target_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();

        return view('goals.index', compact(
            'goals',
            'accounts',
            'totalVaultBalance',
            'totalTargetAmount',
            'overallProgress',
            'activeGoals',
            'completedGoals',
            'pausedGoals'
        ));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $accounts = Account::where('user_id', $user->id)->active()->ordered()->get();
        $currency = currency_symbol();

        return view('goals.create', compact('accounts', 'currency'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:1',
            'start_amount' => 'nullable|numeric|min:0',
            'target_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'account_id' => 'nullable|exists:accounts,id',
            'color' => 'required|string|in:emerald,indigo,sky,amber,purple,rose',
            'icon' => 'nullable|string|max:50',
            'is_locked' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
            'deduct_from_account' => 'nullable|boolean',
        ]);

        $goal = Goal::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'target_amount' => $validated['target_amount'],
            'current_amount' => 0.00,
            'currency' => 'BDT',
            'start_date' => $validated['start_date'] ?? now()->toDateString(),
            'target_date' => $validated['target_date'] ?? null,
            'account_id' => $validated['account_id'] ?? null,
            'color' => $validated['color'] ?? 'emerald',
            'icon' => $validated['icon'] ?? 'vault',
            'status' => 'active',
            'is_locked' => $request->boolean('is_locked'),
            'notes' => $validated['notes'] ?? null,
        ]);

        // If an initial deposit was specified
        $startAmount = (float) ($validated['start_amount'] ?? 0);
        if ($startAmount > 0) {
            $goal->addContribution(
                type: 'deposit',
                amount: $startAmount,
                date: now()->toDateString(),
                accountId: $validated['account_id'] ?? null,
                notes: 'Initial seed deposit on creation',
                createTransaction: $request->boolean('deduct_from_account')
            );
        }

        return redirect()->route('goals.show', $goal)
            ->with('success', "Savings Vault '{$goal->name}' created successfully!");
    }

    public function show(Request $request, Goal $goal): View
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $goal->load(['account']);
        $contributions = $goal->contributions()
            ->with(['account', 'transaction'])
            ->paginate(15);

        $accounts = Account::where('user_id', $request->user()->id)->active()->ordered()->get();
        $currency = currency_symbol();

        return view('goals.show', compact('goal', 'contributions', 'accounts', 'currency'));
    }

    public function edit(Request $request, Goal $goal): View
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $accounts = Account::where('user_id', $request->user()->id)->active()->ordered()->get();
        $currency = currency_symbol();

        return view('goals.edit', compact('goal', 'accounts', 'currency'));
    }

    public function update(Request $request, Goal $goal): RedirectResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:1',
            'target_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'account_id' => 'nullable|exists:accounts,id',
            'color' => 'required|string|in:emerald,indigo,sky,amber,purple,rose',
            'icon' => 'nullable|string|max:50',
            'status' => 'required|string|in:active,completed,paused,cancelled',
            'is_locked' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $goal->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'target_amount' => $validated['target_amount'],
            'start_date' => $validated['start_date'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'account_id' => $validated['account_id'] ?? null,
            'color' => $validated['color'] ?? 'emerald',
            'icon' => $validated['icon'] ?? 'vault',
            'status' => $validated['status'],
            'is_locked' => $request->boolean('is_locked'),
            'notes' => $validated['notes'] ?? null,
        ]);

        $goal->recalculateCurrentAmount();

        return redirect()->route('goals.show', $goal)
            ->with('success', "Vault '{$goal->name}' updated successfully!");
    }

    public function destroy(Request $request, Goal $goal): RedirectResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $name = $goal->name;
        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', "Savings Vault '{$name}' removed.");
    }

    public function deposit(Request $request, Goal $goal): RedirectResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'account_id' => 'nullable|exists:accounts,id',
            'deduct_from_account' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $goal->addContribution(
            type: 'deposit',
            amount: (float) $validated['amount'],
            date: $validated['date'],
            accountId: $validated['account_id'] ?? null,
            notes: $validated['notes'] ?? null,
            createTransaction: $request->boolean('deduct_from_account')
        );

        $msg = "Added " . format_currency($validated['amount']) . " to '{$goal->name}'!";
        if ($goal->fresh()->is_completed) {
            $msg .= " 🎉 Congratulations! Target goal reached!";
        }

        return redirect()->back()->with('success', $msg);
    }

    public function withdraw(Request $request, Goal $goal): RedirectResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($goal->is_locked && !$goal->is_completed) {
            return redirect()->back()->with('error', "Vault '{$goal->name}' is locked against early withdrawals. Unlock the vault in settings if emergency funds are needed.");
        }

        $currentMax = (float) $goal->current_amount;

        $validated = $request->validate([
            'amount' => "required|numeric|min:0.01|max:{$currentMax}",
            'date' => 'required|date',
            'account_id' => 'nullable|exists:accounts,id',
            'credit_to_account' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $goal->addContribution(
            type: 'withdraw',
            amount: (float) $validated['amount'],
            date: $validated['date'],
            accountId: $validated['account_id'] ?? null,
            notes: $validated['notes'] ?? null,
            createTransaction: $request->boolean('credit_to_account')
        );

        return redirect()->back()->with('success', "Withdrew " . format_currency($validated['amount']) . " from '{$goal->name}'.");
    }

    public function toggleLock(Request $request, Goal $goal): RedirectResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $goal->is_locked = !$goal->is_locked;
        $goal->save();

        $statusText = $goal->is_locked ? "locked 🔒" : "unlocked 🔓";
        return redirect()->back()->with('success', "Vault '{$goal->name}' is now {$statusText}.");
    }
}
