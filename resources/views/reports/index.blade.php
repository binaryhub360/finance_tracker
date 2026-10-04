<x-layouts.app :title="'Reports'">
    @section('page-title', 'Financial Reports & Analytics')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('reports.income') }}" class="relative overflow-hidden bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 card-hover group block shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25 mb-4 group-hover:scale-105 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition">Income Analysis</h3>
            <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">Breakdown by category, account, and custom period with CSV export</p>
            <div class="mt-4 flex items-center gap-1 text-xs font-bold text-emerald-600">
                <span>View report</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </div>
        </a>

        <a href="{{ route('reports.expense') }}" class="relative overflow-hidden bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 card-hover group block shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-rose-500 to-red-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 mb-4 group-hover:scale-105 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" /></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 group-hover:text-rose-700 transition">Expense Analysis</h3>
            <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">Deep-dive into spending habits by category and account with CSV export</p>
            <div class="mt-4 flex items-center gap-1 text-xs font-bold text-rose-600">
                <span>View report</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </div>
        </a>

        <a href="{{ route('reports.transfer') }}" class="relative overflow-hidden bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 card-hover group block shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/25 mb-4 group-hover:scale-105 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-700 transition">Transfer Ledger</h3>
            <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">Audit internal funds movement between accounts and wallets</p>
            <div class="mt-4 flex items-center gap-1 text-xs font-bold text-blue-600">
                <span>View report</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </div>
        </a>

        <a href="{{ route('reports.summary') }}" class="relative overflow-hidden bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 card-hover group block shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/25 mb-4 group-hover:scale-105 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 group-hover:text-amber-700 transition">Financial Statement</h3>
            <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">Comprehensive opening vs closing balance, net cash flow, and total assets</p>
            <div class="mt-4 flex items-center gap-1 text-xs font-bold text-amber-600">
                <span>View report</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </div>
        </a>
    </div>
</x-layouts.app>
