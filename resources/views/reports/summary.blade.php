<x-layouts.app :title="'Financial Summary'">
    @section('page-title', 'Financial Summary')

    <div class="mb-6">
        <x-date-filter :action="route('reports.summary')" />
    </div>

    <div class="max-w-3xl space-y-6">
        {{-- Headline KPI cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-card title="Net Cash Flow" :value="format_currency($netCashFlow)" :color="$netCashFlow >= 0 ? 'emerald' : 'rose'" />
            <x-card title="Total Revenue" :value="format_currency($totalIncome)" color="emerald" />
            <x-card title="Total Outlays" :value="format_currency($totalExpense)" color="rose" />
        </div>

        {{-- Ledger Statement Card --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl overflow-hidden shadow-xl shadow-slate-900/5">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/80 via-white to-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Statement of Financial Position</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Calculated reconciliation for the selected timeframe</p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    {{ $from->format(app_date_format()) }} &mdash; {{ $to->format(app_date_format()) }}
                </div>
            </div>

            <div class="divide-y divide-slate-100 text-sm">
                {{-- Opening Balance --}}
                <div class="px-6 py-4 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">01</span>
                        <div>
                            <span class="text-sm font-semibold text-slate-800">Opening Balance</span>
                            <p class="text-xs text-slate-400">Position prior to timeframe start</p>
                        </div>
                    </div>
                    <span class="font-mono text-sm font-bold text-slate-900">{{ format_currency($adjustedOpeningBalance) }}</span>
                </div>

                {{-- Income --}}
                <div class="px-6 py-4 flex justify-between items-center bg-emerald-500/[0.03] hover:bg-emerald-500/[0.06] transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-xs">+</span>
                        <div>
                            <span class="text-sm font-semibold text-emerald-800">Total Income</span>
                            <p class="text-xs text-emerald-600/70">Aggregate credited inflows</p>
                        </div>
                    </div>
                    <span class="font-mono text-sm font-bold text-emerald-600">+{{ format_currency($totalIncome) }}</span>
                </div>

                {{-- Expense --}}
                <div class="px-6 py-4 flex justify-between items-center bg-rose-500/[0.03] hover:bg-rose-500/[0.06] transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-xs">&minus;</span>
                        <div>
                            <span class="text-sm font-semibold text-rose-800">Total Expenses</span>
                            <p class="text-xs text-rose-600/70">Aggregate debited outlays</p>
                        </div>
                    </div>
                    <span class="font-mono text-sm font-bold text-rose-600">&minus;{{ format_currency($totalExpense) }}</span>
                </div>

                {{-- Net Cash Flow --}}
                <div class="px-6 py-4 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">&Delta;</span>
                        <div>
                            <span class="text-sm font-semibold text-slate-800">Net Period Cash Flow</span>
                            <p class="text-xs text-slate-400">Income minus expenses</p>
                        </div>
                    </div>
                    <span class="font-mono text-sm font-bold {{ $netCashFlow >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $netCashFlow >= 0 ? '+' : '' }}{{ format_currency($netCashFlow) }}
                    </span>
                </div>

                {{-- Transfers --}}
                <div class="px-6 py-4 flex justify-between items-center bg-blue-500/[0.02] hover:bg-blue-500/[0.05] transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-xs">&harr;</span>
                        <div>
                            <span class="text-sm font-semibold text-blue-900">Internal Transfers</span>
                            <p class="text-xs text-blue-500/70">{{ $transferCount }} transfer(s) between accounts</p>
                        </div>
                    </div>
                    <span class="font-mono text-sm font-semibold text-blue-600">{{ format_currency($totalTransfers) }}</span>
                </div>

                {{-- Closing Balance --}}
                <div class="px-6 py-5 flex justify-between items-center bg-slate-900 text-white rounded-b-3xl">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Closing Balance</span>
                        <p class="text-xs text-slate-400 mt-0.5">Calculated net available liquid position</p>
                    </div>
                    <span class="font-mono text-xl sm:text-2xl font-extrabold tracking-tight {{ $closingBalance >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ format_currency($closingBalance) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-100/70 border border-slate-200/60 flex items-start gap-3">
            <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <p class="text-xs text-slate-500 leading-relaxed">
                <strong class="font-semibold text-slate-700">Reconciliation Formula:</strong> Closing Balance = Opening Balance + Income &minus; Expense. Internal transfers move balances across accounts without altering net worth.
            </p>
        </div>
    </div>
</x-layouts.app>
