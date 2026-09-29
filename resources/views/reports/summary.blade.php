<x-layouts.app :title="'Financial Summary'">
    @section('page-title', 'Financial Summary')

    <div class="mb-6">
        <x-date-filter :action="route('reports.summary')" />
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Financial Summary</h3>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $from->format(app_date_format()) }} — {{ $to->format(app_date_format()) }}
                </p>
            </div>

            <div class="divide-y divide-gray-200">
                <div class="px-6 py-4 flex justify-between items-center">
                    <span class="text-sm text-gray-600">Opening Balance</span>
                    <span class="text-sm font-semibold text-gray-900">{{ format_currency($adjustedOpeningBalance) }}</span>
                </div>

                <div class="px-6 py-4 flex justify-between items-center bg-emerald-50/50">
                    <span class="text-sm text-emerald-700 font-medium">+ Total Income</span>
                    <span class="text-sm font-semibold text-emerald-600">{{ format_currency($totalIncome) }}</span>
                </div>

                <div class="px-6 py-4 flex justify-between items-center bg-red-50/50">
                    <span class="text-sm text-red-700 font-medium">− Total Expense</span>
                    <span class="text-sm font-semibold text-red-600">{{ format_currency($totalExpense) }}</span>
                </div>

                <div class="px-6 py-4 flex justify-between items-center">
                    <span class="text-sm text-gray-600">Net Cash Flow</span>
                    <span class="text-sm font-semibold {{ $netCashFlow >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ format_currency($netCashFlow) }}
                    </span>
                </div>

                <div class="px-6 py-4 flex justify-between items-center bg-blue-50/50">
                    <div>
                        <span class="text-sm text-blue-700 font-medium">↔ Total Transfers</span>
                        <span class="text-xs text-blue-500 ml-1">({{ $transferCount }} transfers)</span>
                    </div>
                    <span class="text-sm font-semibold text-blue-600">{{ format_currency($totalTransfers) }}</span>
                </div>

                <div class="px-6 py-5 flex justify-between items-center bg-gray-50">
                    <span class="text-base font-semibold text-gray-900">Closing Balance</span>
                    <span class="text-lg font-bold {{ $closingBalance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        {{ format_currency($closingBalance) }}
                    </span>
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                <p class="text-xs text-gray-400">
                    Formula: Closing Balance = Opening Balance + Income − Expense.
                    Transfers do not affect the overall balance.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
