<x-layouts.app :title="'Reports'">
    @section('page-title', 'Reports')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('reports.income') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:border-emerald-300 hover:shadow-sm transition group">
            <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-emerald-700">Income Report</h3>
            <p class="mt-1 text-xs text-gray-500">View income by category, account, and date range</p>
        </a>

        <a href="{{ route('reports.expense') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:border-red-300 hover:shadow-sm transition group">
            <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-red-50 text-red-600 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" /></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-red-700">Expense Report</h3>
            <p class="mt-1 text-xs text-gray-500">View expenses by category, account, and date range</p>
        </a>

        <a href="{{ route('reports.transfer') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:border-blue-300 hover:shadow-sm transition group">
            <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50 text-blue-600 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-700">Transfer Report</h3>
            <p class="mt-1 text-xs text-gray-500">View transfers between accounts</p>
        </a>

        <a href="{{ route('reports.summary') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:border-amber-300 hover:shadow-sm transition group">
            <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-amber-50 text-amber-600 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-amber-700">Financial Summary</h3>
            <p class="mt-1 text-xs text-gray-500">Overall financial position and cash flow</p>
        </a>
    </div>
</x-layouts.app>
