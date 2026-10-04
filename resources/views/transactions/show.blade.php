<x-layouts.app :title="'Transaction Details'">
    @section('page-title', 'Transaction Details')
    @section('page-actions')
        <div class="flex items-center gap-2.5">
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white/80 hover:bg-slate-100 border border-slate-200 transition-all duration-200">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back
            </a>
            <a href="{{ route('transactions.edit', $transaction) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-sm shadow-emerald-600/20 hover:shadow-md hover:shadow-emerald-600/30 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                </svg>
                Edit Transaction
            </a>
        </div>
    @endsection

    <div class="max-w-2xl">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl overflow-hidden shadow-xl shadow-slate-900/5">
            {{-- Header hero --}}
            <div class="relative px-6 py-8 sm:px-8 border-b border-slate-100 overflow-hidden bg-gradient-to-br from-slate-50/80 via-white to-slate-50/50">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase {{ $transaction->type_bg_color }} {{ $transaction->type_color }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $transaction->type === 'income' ? 'bg-emerald-500' : ($transaction->type === 'expense' ? 'bg-rose-500' : 'bg-blue-500') }}"></span>
                        {{ $transaction->type_label }}
                    </span>
                    <span class="text-xs font-medium text-slate-400">#TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl sm:text-4xl font-extrabold tracking-tight {{ $transaction->type_color }} font-mono">
                        {{ $transaction->type === 'expense' ? '−' : ($transaction->type === 'income' ? '+' : '') }}{{ format_currency($transaction->amount) }}
                    </span>
                </div>
                @if($transaction->description)
                    <p class="text-sm text-slate-600 mt-2 font-medium">{{ $transaction->description }}</p>
                @endif
            </div>

            {{-- Detail List --}}
            <dl class="divide-y divide-slate-100 text-sm">
                <div class="px-6 py-4 sm:px-8 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Date</dt>
                    <dd class="font-medium text-slate-800">{{ $transaction->date->format(app_date_format()) }}</dd>
                </div>

                @if($transaction->type !== 'transfer')
                    <div class="px-6 py-4 sm:px-8 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Account</dt>
                        <dd class="font-medium text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ $transaction->account?->name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="px-6 py-4 sm:px-8 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Category</dt>
                        <dd class="font-medium text-slate-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $transaction->category_name }}
                            </span>
                        </dd>
                    </div>
                @else
                    <div class="px-6 py-4 sm:px-8 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">From Account</dt>
                        <dd class="font-medium text-slate-900 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            {{ $transaction->fromAccount?->name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="px-6 py-4 sm:px-8 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">To Account</dt>
                        <dd class="font-medium text-slate-900 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            {{ $transaction->toAccount?->name ?? 'N/A' }}
                        </dd>
                    </div>
                @endif

                @if($transaction->reference)
                    <div class="px-6 py-4 sm:px-8 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Reference</dt>
                        <dd class="font-mono text-xs font-semibold text-slate-700 bg-slate-100/80 px-2 py-1 rounded-md">{{ $transaction->reference }}</dd>
                    </div>
                @endif

                <div class="px-6 py-4 sm:px-8 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Recorded At</dt>
                    <dd class="text-xs text-slate-500">{{ $transaction->created_at->format('d M Y, h:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-layouts.app>
