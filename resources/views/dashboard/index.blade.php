<x-layouts.app :title="'Dashboard'">
    @section('page-title', 'Financial Dashboard')
    @section('page-actions')
        <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-emerald-500/25 transition-all duration-200 hover:shadow-lg hover:shadow-emerald-500/35 hover:-translate-y-0.5 active:translate-y-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.25" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>New Transaction</span>
        </a>
    @endsection

    {{-- Date Filter Bar --}}
    <div class="mb-8">
        <x-date-filter :action="route('dashboard')" />
    </div>

    {{-- Top Summary KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <x-card title="Total Balance" :value="format_currency($totalBalance)" color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                </svg>
            </x-slot:icon>
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Across {{ $accounts->count() }} active account(s)</span>
                <span class="inline-flex items-center gap-1 text-blue-600 font-semibold">Live &bull;</span>
            </div>
        </x-card>

        <x-card title="Total Income" :value="format_currency($totalIncome)" color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
            </x-slot:icon>
            <div class="flex items-center text-xs font-semibold text-emerald-600">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" /></svg>
                <span>In selected period</span>
            </div>
        </x-card>

        <x-card title="Total Expense" :value="format_currency($totalExpense)" color="red">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" />
                </svg>
            </x-slot:icon>
            <div class="flex items-center text-xs font-semibold text-rose-600">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5l15 15m0 0V8.25m0 11.25H8.25" /></svg>
                <span>In selected period</span>
            </div>
        </x-card>

        <x-card title="Net Cash Flow" :value="format_currency($netCashFlow)" color="{{ $netCashFlow >= 0 ? 'emerald' : 'red' }}">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
            </x-slot:icon>
            <div class="flex items-center text-xs font-semibold {{ $netCashFlow >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                <span>{{ $netCashFlow >= 0 ? '+ Net Surplus' : '- Net Deficit' }}</span>
            </div>
        </x-card>
    </div>

    {{-- Main Visual Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Account Balances Card --}}
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 shadow-sm card-hover flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Account Balances</h3>
                    </div>
                    <a href="{{ route('accounts.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">Manage &rarr;</a>
                </div>

                @if($accounts->isEmpty())
                    <div class="py-10 text-center text-sm text-slate-400">
                        <p>No accounts created yet.</p>
                        <a href="{{ route('accounts.create') }}" class="mt-2 inline-block text-xs font-semibold text-emerald-600 hover:underline">+ Add Account</a>
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach($accounts as $account)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-slate-100/80 transition-colors border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100/80 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                        {{ substr($account->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $account->name }}</p>
                                        <p class="text-[11px] font-medium text-slate-400 capitalize">
                                            {{ $account->type_label }}
                                            @if($account->currency)
                                                <span class="text-emerald-600 font-mono font-bold">• {{ $account->currency }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold font-tabular {{ $account->balance >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                                        {{ format_currency($account->balance, $account->currency) }}
                                    </span>
                                    @if($account->currency && $account->currency !== (get_setting('currency_code', 'BDT')))
                                        <div class="text-[10px] text-slate-400 font-normal">≈ {{ format_currency($account->base_balance) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Combined Assets</span>
                <span class="font-bold text-slate-900 font-tabular text-sm">{{ format_currency($totalBalance) }}</span>
            </div>
        </div>

        {{-- Expense Breakdown Card --}}
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 shadow-sm card-hover flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Expense Breakdown</h3>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">By Category</span>
                </div>

                @if($expenseBreakdown->isEmpty())
                    <div class="py-16 text-center text-sm text-slate-400">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                        <p>No expenses recorded in this period.</p>
                    </div>
                @else
                    <div class="relative h-56 flex items-center justify-center">
                        <canvas id="expenseChart"></canvas>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('reports.expense') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">Detailed Expense Report &rarr;</a>
            </div>
        </div>

        {{-- Monthly Cash Flow Card --}}
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 shadow-sm card-hover flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Cash Flow Trend</h3>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">Last 6 Months</span>
                </div>

                <div class="h-56">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('reports.summary') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">View Financial Summary &rarr;</a>
            </div>
        </div>
    </div>

    {{-- Active Budgets Progress Section (if any configured for this month) --}}
    @if(isset($activeBudgets) && $activeBudgets->count() > 0)
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 shadow-sm card-hover space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Active Monthly Budgets</h3>
                        <p class="text-[11px] text-slate-400">Current spending vs targets for {{ \Carbon\Carbon::now()->format('F Y') }}</p>
                    </div>
                </div>
                <a href="{{ route('budgets.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                    Manage Budgets &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeBudgets as $b)
                    <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/60 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900 truncate max-w-[140px]">{{ $b->category ? $b->category->name : 'Category' }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $b->status_badge_classes }}">
                                {{ $b->spent_percentage }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-200/80 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $b->progress_bar_classes }}" style="width: {{ $b->progress_width }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-mono text-slate-500">
                            <span>{{ currency_symbol() }}{{ number_format($b->spent_amount, 2) }}</span>
                            <span>Limit: {{ currency_symbol() }}{{ number_format($b->amount, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Upcoming Subscriptions & Bills Widget (if any due soon) --}}
    @if(isset($upcomingRecurring) && $upcomingRecurring->count() > 0)
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 shadow-sm card-hover space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Upcoming Renewals & Bills</h3>
                        <p class="text-[11px] text-slate-400">Recurring commitments due within the next 7 days</p>
                    </div>
                </div>
                <a href="{{ route('recurring.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                    View All Subscriptions &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($upcomingRecurring as $item)
                    <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/60 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-xs text-slate-900 truncate">{{ $item->name }}</span>
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $item->due_badge_classes }}">
                                    {{ $item->due_badge_label }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 font-mono">
                                {{ format_currency($item->amount) }} &bull; {{ $item->account?->name ?? 'Unassigned' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('recurring.log', $item) }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-100 hover:bg-emerald-200 px-3 py-1.5 rounded-xl transition">
                                Log
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Savings Goals & Vaults Widget (if any configured) --}}
    @if(isset($savingsGoals) && $savingsGoals->count() > 0)
        <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 shadow-sm card-hover space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Savings Vaults & Goals</h3>
                        <p class="text-xs text-slate-400">Ring-fenced reserves and target milestones</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono font-bold text-emerald-600 hidden sm:inline-block">
                        Total Saved: {{ format_currency($totalVaultSavings ?? 0) }}
                    </span>
                    <a href="{{ route('goals.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                        Manage Vaults →
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($savingsGoals as $goal)
                    @php $theme = $goal->color_theme; @endphp
                    <a href="{{ route('goals.show', $goal) }}"
                        class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60 hover:border-slate-300 hover:shadow-xs transition block group">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-bold text-slate-900 truncate group-hover:text-emerald-700 transition">{{ $goal->name }}</span>
                            <span class="font-mono text-[11px] font-bold text-emerald-600">{{ $goal->progress_percentage }}%</span>
                        </div>

                        <div class="w-full bg-slate-200/80 rounded-full h-2 overflow-hidden mb-2">
                            <div class="h-full rounded-full bg-gradient-to-r {{ $theme['gradient'] }} transition-all duration-500"
                                style="width: {{ $goal->progress_percentage }}%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] font-mono text-slate-500">
                            <span class="font-bold text-slate-800">{{ format_currency($goal->current_amount) }}</span>
                            <span>of {{ format_currency($goal->target_amount) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Recent Transactions Elevated Card --}}
    <div class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden card-hover">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900 tracking-tight">Recent Transactions</h3>
                <p class="text-xs text-slate-400">Latest financial activities across your portfolio</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition">
                <span>View Full Ledger</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
            </a>
        </div>

        @if($recentTransactions->isEmpty())
            <x-empty-state message="No transactions recorded yet." :action="route('transactions.create')" actionText="Add First Transaction" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                        <tr class="bg-slate-50/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-3.5 text-left">Date</th>
                            <th class="px-6 py-3.5 text-left">Type</th>
                            <th class="px-6 py-3.5 text-left">Category</th>
                            <th class="px-6 py-3.5 text-left">Account</th>
                            <th class="px-6 py-3.5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentTransactions as $txn)
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        // Set Chart.js global typography
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        // Monthly Cash Flow Chart
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($monthlyData['labels']),
                    datasets: [
                        {
                            label: 'Income',
                            data: @json($monthlyData['income']),
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                            barPercentage: 0.65,
                            categoryPercentage: 0.75,
                        },
                        {
                            label: 'Expense',
                            data: @json($monthlyData['expense']),
                            backgroundColor: '#f43f5e',
                            borderRadius: 6,
                            barPercentage: 0.65,
                            categoryPercentage: 0.75,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                borderRadius: 3,
                                useBorderRadius: true,
                                padding: 15,
                                font: { size: 11, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: '700' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: true,
                            boxPadding: 4,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10, weight: '500' } }
                        },
                        y: {
                            grid: { color: 'rgba(226, 232, 240, 0.6)' },
                            border: { dash: [4, 4], display: false },
                            ticks: {
                                font: { size: 10, weight: '500' },
                                callback: function(value) {
                                    return value >= 1000 ? (value / 1000) + 'k' : value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Expense Breakdown Donut Chart
        @if($expenseBreakdown->isNotEmpty())
        const expenseCtx = document.getElementById('expenseChart');
        if (expenseCtx) {
            const colors = [
                '#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899',
                '#f43f5e', '#f97316', '#eab308', '#84cc16', '#64748b'
            ];
            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($expenseBreakdown->pluck('category_name')),
                    datasets: [{
                        data: @json($expenseBreakdown->pluck('total')),
                        backgroundColor: colors.slice(0, {{ $expenseBreakdown->count() }}),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 8,
                                boxHeight: 8,
                                borderRadius: 4,
                                useBorderRadius: true,
                                padding: 10,
                                font: { size: 11, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: '700' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 10,
                        }
                    }
                }
            });
        }
        @endif
    </script>
    @endpush
</x-layouts.app>
