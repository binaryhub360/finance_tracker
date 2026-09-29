<x-layouts.app :title="'Dashboard'">
    @section('page-title', 'Dashboard')

    {{-- Date filter --}}
    <div class="mb-6">
        <x-date-filter :action="route('dashboard')" />
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card title="Total Balance" :value="format_currency($totalBalance)" color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                </svg>
            </x-slot:icon>
        </x-card>

        <x-card title="Total Income" :value="format_currency($totalIncome)" color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
            </x-slot:icon>
        </x-card>

        <x-card title="Total Expense" :value="format_currency($totalExpense)" color="red">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" />
                </svg>
            </x-slot:icon>
        </x-card>

        <x-card title="Net Cash Flow" :value="format_currency($netCashFlow)" color="{{ $netCashFlow >= 0 ? 'emerald' : 'red' }}">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
            </x-slot:icon>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Account Balances --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Account Balances</h3>
            @if($accounts->isEmpty())
                <p class="text-sm text-gray-500">No accounts yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($accounts as $account)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $account->name }}</p>
                                <p class="text-xs text-gray-500">{{ $account->type_label }}</p>
                            </div>
                            <span class="text-sm font-semibold {{ $account->balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ format_currency($account->balance) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Expense Breakdown --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Expense Breakdown</h3>
            @if($expenseBreakdown->isEmpty())
                <p class="text-sm text-gray-500">No expenses in this period.</p>
            @else
                <canvas id="expenseChart" height="200"></canvas>
            @endif
        </div>

        {{-- Monthly Cash Flow --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Cash Flow</h3>
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Recent Transactions</h3>
            <a href="{{ route('transactions.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">View all &rarr;</a>
        </div>
        @if($recentTransactions->isEmpty())
            <x-empty-state message="No transactions yet." :action="route('transactions.create')" actionText="Add Transaction" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentTransactions as $txn)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $txn->date->format(app_date_format()) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $txn->type_bg_color }}">
                                        {{ $txn->type_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $txn->category_name }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $txn->account_display }}</td>
                                <td class="px-5 py-3 text-sm font-medium whitespace-nowrap text-right {{ $txn->type_color }}">
                                    {{ $txn->type === 'expense' ? '-' : '' }}{{ format_currency($txn->amount) }}
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
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Expense',
                            data: @json($monthlyData['expense']),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value >= 1000 ? (value / 1000) + 'k' : value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Expense Breakdown Chart
        @if($expenseBreakdown->isNotEmpty())
        const expenseCtx = document.getElementById('expenseChart');
        if (expenseCtx) {
            const colors = [
                '#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6',
                '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16'
            ];
            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($expenseBreakdown->pluck('category_name')),
                    datasets: [{
                        data: @json($expenseBreakdown->pluck('total')),
                        backgroundColor: colors.slice(0, {{ $expenseBreakdown->count() }}),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, padding: 10, font: { size: 11 } }
                        }
                    }
                }
            });
        }
        @endif
    </script>
    @endpush
</x-layouts.app>
