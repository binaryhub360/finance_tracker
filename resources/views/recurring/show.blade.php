<x-layouts.app :title="$recurring->name">
    @section('page-title', $recurring->name)

    <div class="space-y-6">
        {{-- Top Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('recurring.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ $recurring->name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $recurring->status_badge_classes }}">
                            {{ ucfirst($recurring->status) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $recurring->type === 'expense' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $recurring->type }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Recurring commitment created on {{ $recurring->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Log Occurrence Button --}}
                @if($recurring->status === 'active')
                    <form method="POST" action="{{ route('recurring.log', $recurring) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Log Payment Now
                        </button>
                    </form>

                    {{-- Skip Cycle --}}
                    <form method="POST" action="{{ route('recurring.skip', $recurring) }}" onsubmit="return confirm('Skip next cycle ({{ $recurring->next_due_date->format('M d, Y') }}) without debiting account?');" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200/80 hover:bg-slate-50 shadow-sm transition">
                            Skip Cycle
                        </button>
                    </form>
                @endif

                {{-- Pause / Resume --}}
                <form method="POST" action="{{ route('recurring.toggle-pause', $recurring) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold {{ $recurring->status === 'active' ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }} transition">
                        {{ $recurring->status === 'active' ? 'Pause Subscription' : 'Resume Subscription' }}
                    </button>
                </form>

                {{-- Edit --}}
                <a href="{{ route('recurring.edit', $recurring) }}" class="p-2 text-slate-600 hover:text-slate-900 bg-white border border-slate-200/80 rounded-xl hover:bg-slate-50 transition-colors" title="Edit">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </a>

                {{-- Delete --}}
                <form method="POST" action="{{ route('recurring.destroy', $recurring) }}" onsubmit="return confirm('Are you sure you want to delete this recurring schedule? Previously recorded transactions will remain in your ledger.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 bg-white border border-rose-200/80 rounded-xl hover:bg-rose-50 transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Financial Metrics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Per Cycle Amount</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">
                    {{ format_currency($recurring->amount) }}
                </p>
                <div class="mt-2 text-[11px] text-slate-400 font-medium">{{ $recurring->frequency_label }}</div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Monthly Run-Rate</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">
                    {{ format_currency($recurring->monthly_equivalent_amount) }}
                </p>
                <div class="mt-2 text-[11px] text-slate-400">Normalized 30-day budget impact</div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Annualized Cost</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">
                    {{ format_currency($recurring->yearly_equivalent_amount) }}
                </p>
                <div class="mt-2 text-[11px] text-slate-400">12-month projected commitment</div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Total Recorded</span>
                <p class="text-2xl font-bold font-mono text-emerald-600 mt-1">
                    {{ format_currency($recurring->transactions()->sum('amount')) }}
                </p>
                <div class="mt-2 text-[11px] text-emerald-600/80 font-medium">
                    Across {{ $recurring->transactions()->count() }} logged {{ Str::plural('cycle', $recurring->transactions()->count()) }}
                </div>
            </div>
        </div>

        {{-- Subscription Details Card --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
            <h3 class="text-base font-bold text-slate-900 tracking-tight pb-3 border-b border-slate-100">Schedule & Billing Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
                <div>
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Payment Account</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-semibold bg-slate-100 text-slate-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $recurring->account?->name ?? 'Unassigned' }}
                    </span>
                    @if($recurring->account)
                        <span class="text-slate-400 ml-1">({{ format_currency($recurring->account->balance) }})</span>
                    @endif
                </div>

                <div>
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Category</span>
                    <span class="font-semibold text-slate-800">{{ $recurring->category_name }}</span>
                </div>

                <div>
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Payment Method</span>
                    <span class="font-semibold text-slate-800">{{ $recurring->payment_method ?: 'Not specified' }}</span>
                </div>

                <div>
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Next Renewal Date</span>
                    <span class="inline-flex items-center gap-1.5 font-bold font-mono text-sm {{ $recurring->is_overdue ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $recurring->next_due_date ? $recurring->next_due_date->format('M d, Y') : 'N/A' }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ml-1 {{ $recurring->due_badge_classes }}">
                        {{ $recurring->due_badge_label }}
                    </span>
                </div>

                <div>
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Start Date & Duration</span>
                    <span class="font-medium text-slate-700">
                        From {{ $recurring->start_date->format('M d, Y') }}
                        @if($recurring->end_date)
                            until {{ $recurring->end_date->format('M d, Y') }}
                        @else
                            (Ongoing)
                        @endif
                    </span>
                </div>

                <div>
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Automation Status</span>
                    @if($recurring->auto_log)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-emerald-700 bg-emerald-50 font-semibold border border-emerald-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Auto-Logging Enabled
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-slate-600 bg-slate-100 font-medium">
                            Manual Confirmation Required
                        </span>
                    @endif
                </div>
            </div>

            @if($recurring->notes)
                <div class="pt-4 border-t border-slate-100">
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Subscription Notes & Links</span>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 whitespace-pre-line font-mono">
                        {{ $recurring->notes }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Payment History Ledger --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Occurrence & Ledger History</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Transactions recorded and reconciled into your account ledger from this recurring schedule.</p>
                </div>
                @if($recurring->status === 'active')
                    <form method="POST" action="{{ route('recurring.log', $recurring) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            + Log New Occurrence
                        </button>
                    </form>
                @endif
            </div>

            @if($transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <th class="pb-3">Date</th>
                                <th class="pb-3">Description</th>
                                <th class="pb-3">Account</th>
                                <th class="pb-3">Reference</th>
                                <th class="pb-3 text-right">Amount</th>
                                <th class="pb-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($transactions as $txn)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 font-medium text-slate-800">{{ $txn->date->format('M d, Y') }}</td>
                                    <td class="py-3 text-slate-700 max-w-xs truncate">{{ $txn->description }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg font-semibold bg-slate-100 text-slate-800 text-[11px]">
                                            {{ $txn->account?->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3 font-mono text-slate-500">{{ $txn->reference ?: '-' }}</td>
                                    <td class="py-3 text-right font-mono font-bold {{ $txn->type === 'expense' ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $txn->type === 'expense' ? '-' : '+' }}{{ format_currency($txn->amount) }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <a href="{{ route('transactions.show', $txn) }}" class="p-1 text-slate-400 hover:text-blue-600 transition" title="View Transaction in Ledger">
                                            <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="pt-3 border-t border-slate-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-xs text-slate-400">No payment occurrences logged yet for this schedule.</p>
                    @if($recurring->status === 'active')
                        <form method="POST" action="{{ route('recurring.log', $recurring) }}" class="inline">
                            @csrf
                            <button type="submit" class="mt-2 text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                Log first payment occurrence now
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
