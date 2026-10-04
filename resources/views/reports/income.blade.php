<x-layouts.app :title="'Income Report'">
    @section('page-title', 'Income Report')
    @section('page-actions')
        <a href="{{ route('reports.export.income', request()->query()) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white/80 hover:bg-slate-100 border border-slate-200 transition-all duration-200 shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Export CSV
        </a>
    @endsection

    {{-- Filters --}}
    <div class="mb-6">
        <x-date-filter :action="route('reports.income')">
            <div>
                <label for="category_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Category</label>
                <select name="category_id" id="category_id" class="block w-full rounded-xl border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="account_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Account</label>
                <select name="account_id" id="account_id" class="block w-full rounded-xl border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
        </x-date-filter>
    </div>

    {{-- Summary KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <x-card title="Total Inflow Revenue" :value="format_currency($totalIncome)" color="emerald" />
        <x-card title="Transaction Count" :value="number_format($transactionCount)" color="blue" />
    </div>

    {{-- Breakdowns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Category Breakdown --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 tracking-tight mb-4">Revenue by Category</h3>
            <div class="space-y-3">
                @forelse($categoryBreakdown as $item)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-none">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-slate-800">{{ $item->category_name }}</span>
                            <span class="text-[11px] font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $item->count }} txns</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-emerald-600">+{{ format_currency($item->total) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No category breakdown data.</p>
                @endforelse
            </div>
        </div>

        {{-- Account Breakdown --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 tracking-tight mb-4">Revenue by Account</h3>
            <div class="space-y-3">
                @forelse($accountBreakdown as $item)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-none">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-xs font-semibold text-slate-800">{{ $item->account_name }}</span>
                            <span class="text-[11px] font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $item->count }} txns</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-emerald-600">+{{ format_currency($item->total) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No account breakdown data.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Transaction List --}}
    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Income Transaction Ledger</h3>
        </div>
        @if($transactions->isEmpty())
            <x-empty-state message="No income transactions found for the selected filters." />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5">Category</th>
                            <th class="px-6 py-3.5">Account</th>
                            <th class="px-6 py-3.5 text-right">Amount</th>
                            <th class="px-6 py-3.5">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($transactions as $txn)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ $txn->date->format(app_date_format()) }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold text-[11px]">
                                        {{ $txn->incomeCategory?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-slate-700">{{ $txn->account?->name ?? '-' }}</td>
                                <td class="px-6 py-3.5 font-mono font-bold text-emerald-600 text-right whitespace-nowrap">+{{ format_currency($txn->amount) }}</td>
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
