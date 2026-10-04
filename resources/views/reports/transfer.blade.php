<x-layouts.app :title="'Transfer Report'">
    @section('page-title', 'Transfer Report')
    @section('page-actions')
        <a href="{{ route('reports.export.transfer', request()->query()) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white/80 hover:bg-slate-100 border border-slate-200 transition-all duration-200 shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Export CSV
        </a>
    @endsection

    {{-- Filters --}}
    <div class="mb-6">
        <x-date-filter :action="route('reports.transfer')">
            <div>
                <label for="from_account_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">From Account</label>
                <select name="from_account_id" id="from_account_id" class="block w-full rounded-xl border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('from_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="to_account_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">To Account</label>
                <select name="to_account_id" id="to_account_id" class="block w-full rounded-xl border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('to_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
        </x-date-filter>
    </div>

    {{-- Summary KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <x-card title="Total Transfers Volume" :value="format_currency($totalTransfers)" color="blue" />
        <x-card title="Transfer Operations" :value="number_format($transferCount)" color="blue" />
    </div>

    {{-- Transaction List --}}
    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Internal Transfer Ledger</h3>
        </div>
        @if($transactions->isEmpty())
            <x-empty-state message="No transfers found for the selected filters." />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5">From Account</th>
                            <th class="px-6 py-3.5">To Account</th>
                            <th class="px-6 py-3.5 text-right">Amount</th>
                            <th class="px-6 py-3.5">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($transactions as $txn)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ $txn->date->format(app_date_format()) }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-lg text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ $txn->fromAccount?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $txn->toAccount?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-mono font-bold text-blue-600 text-right whitespace-nowrap">{{ format_currency($txn->amount) }}</td>
                                <td class="px-6 py-3.5 text-slate-500 max-w-xs truncate">{{ $txn->description ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">{{ $transactions->links() }}</div>
            @endif
        @endif
    </div>
</x-layouts.app>
