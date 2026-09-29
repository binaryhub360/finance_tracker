<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = Account::where('user_id', $request->user()->id)
            ->ordered()
            ->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Account::create($data);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function edit(Account $account): View
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $account->update($data);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function toggle(Account $account): RedirectResponse
    {
        $account->update(['is_active' => !$account->is_active]);

        $status = $account->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Account {$status} successfully.");
    }

    public function destroy(Account $account): RedirectResponse
    {
        // Check if account has any transactions
        $hasTransactions = $account->transactions()->exists()
            || $account->outgoingTransfers()->exists()
            || $account->incomingTransfers()->exists();

        if ($hasTransactions) {
            return back()->with('error', 'Cannot delete this account because it has transactions. Consider deactivating it instead.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
