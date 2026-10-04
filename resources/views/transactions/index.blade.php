<x-layouts.app :title="'Transactions'">
    @section('page-title', 'Transaction Ledger')
    @section('page-actions')
        <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-emerald-500/25 transition-all duration-200 hover:shadow-lg hover:shadow-emerald-500/35 hover:-translate-y-0.5 active:translate-y-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.25" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>New Transaction</span>
        </a>
    @endsection

    {{-- Filter Card --}}
    <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-5 mb-8 shadow-sm">
        <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap items-end gap-3.5">
            <div>
                <label for="type" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Type</label>
                <select name="type" id="type" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs cursor-pointer">
                    <option value="">All Types</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>
            <div>
                <label for="account_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Account</label>
                <select name="account_id" id="account_id" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs cursor-pointer">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs">
            </div>
            <div>
                <label for="date_to" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keyword Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Description, note, reference..."
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-emerald-500/20 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['type', 'account_id', 'date_from', 'date_to', 'search']))
                    <a href="{{ route('transactions.index') }}" class="text-xs font-medium text-slate-400 hover:text-slate-600 transition px-2">Reset</a>
                @endif
            </div>
        </form>
    </div>

    @if($transactions->isEmpty())
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-8 shadow-sm">
            <x-empty-state message="No transactions match your criteria." :action="route('transactions.create')" actionText="Add Transaction" />
        </div>
    @else
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden card-hover">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                        <tr class="bg-slate-50/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Type</th>
                            <th class="px-6 py-4 text-left">Category</th>
                            <th class="px-6 py-4 text-left">Account</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                            <th class="px-6 py-4 text-left">Description</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transactions as $txn)
                            <tr class="hover:bg-emerald-50/20 transition-colors group">
                                <td class="px-6 py-4 text-xs font-medium text-slate-500 whitespace-nowrap">
                                    {{ $txn->date->format(app_date_format()) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeBadge = match($txn->type) {
                                            'income' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                                            'expense' => 'bg-rose-50 text-rose-700 border-rose-200/60',
                                            'transfer' => 'bg-blue-50 text-blue-700 border-blue-200/60',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $typeBadge }}">
                                        {{ $txn->type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $txn->category_name }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $txn->account_display }}
                                </td>
                                <td class="px-6 py-4 text-sm font-extrabold whitespace-nowrap text-right font-tabular {{ $txn->type === 'income' ? 'text-emerald-600' : ($txn->type === 'expense' ? 'text-rose-600' : 'text-blue-600') }}">
                                    {{ $txn->type === 'expense' ? '-' : ($txn->type === 'income' ? '+' : '') }}{{ format_currency($txn->amount) }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate">
                                    {{ $txn->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('transactions.show', $txn) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View details">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </a>
                                        <a href="{{ route('transactions.edit', $txn) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('transactions.destroy', $txn) }}" class="inline" onsubmit="return confirm('Delete this transaction?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    @endif
</x-layouts.app>
