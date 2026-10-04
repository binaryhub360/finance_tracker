<x-layouts.app :title="'Accounts'">
    @section('page-title', 'Financial Accounts')
    @section('page-actions')
        <a href="{{ route('accounts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-emerald-500/25 transition-all duration-200 hover:shadow-lg hover:shadow-emerald-500/35 hover:-translate-y-0.5 active:translate-y-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.25" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Account</span>
        </a>
    @endsection

    @if($accounts->isEmpty())
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-8 shadow-sm">
            <x-empty-state message="No accounts configured yet. Create an account (Cash, Bank, Mobile Wallet) to start tracking." :action="route('accounts.create')" actionText="Add First Account" />
        </div>
    @else
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden card-hover">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                        <tr class="bg-slate-50/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4 text-left">Account Name</th>
                            <th class="px-6 py-4 text-left">Type</th>
                            <th class="px-6 py-4 text-right">Opening Balance</th>
                            <th class="px-6 py-4 text-right">Current Balance</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($accounts as $account)
                            <tr class="hover:bg-emerald-50/20 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 flex items-center justify-center text-white font-bold text-xs shadow-xs">
                                            {{ substr($account->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-slate-900">{{ $account->name }}</span>
                                            @if($account->notes)
                                                <p class="text-xs text-slate-400 mt-0.5 max-w-xs truncate">{{ $account->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $account->type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-500 text-right font-tabular whitespace-nowrap">
                                    {{ format_currency($account->opening_balance) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-extrabold text-right font-tabular whitespace-nowrap {{ $account->balance >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                                    {{ format_currency($account->balance) }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <form method="POST" action="{{ route('accounts.toggle', $account) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all {{ $account->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}" title="Click to toggle status">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $account->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('accounts.edit', $account) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Edit account">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('accounts.destroy', $account) }}" class="inline" onsubmit="return confirm('Delete this account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete account">
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
        </div>
    @endif
</x-layouts.app>
