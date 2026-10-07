<x-layouts.app :title="'Budgets & Spending Limits'">
    @section('page-title', 'Budgets & Spending Targets')

    <div class="space-y-6">
        {{-- Month Navigator & Primary Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('budgets.index', ['month' => $prevMonth]) }}"
                    class="p-2 text-slate-500 hover:text-slate-800 bg-white/80 hover:bg-slate-100 border border-slate-200/80 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                
                <div class="flex items-center gap-2">
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $monthLabel }}</span>
                    <form method="GET" action="{{ route('budgets.index') }}" class="inline">
                        <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
                            class="text-xs text-slate-400 bg-transparent border-0 hover:text-slate-700 focus:ring-0 cursor-pointer p-0 w-6 h-6">
                    </form>
                </div>

                <a href="{{ route('budgets.index', ['month' => $nextMonth]) }}"
                    class="p-2 text-slate-500 hover:text-slate-800 bg-white/80 hover:bg-slate-100 border border-slate-200/80 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if($hasPreviousBudgets && $unbudgetedCategories->isNotEmpty())
                    <form method="POST" action="{{ route('budgets.copy-previous') }}" class="inline">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200/80 hover:bg-slate-50 shadow-sm transition-all duration-200"
                            title="Duplicate budget limits from previous month">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v2.25A2.25 2.25 0 0113.5 21.75h-7.5A2.25 2.25 0 013.75 19.5V8.25A2.25 2.25 0 016 6h2.25m4.5 0h6A2.25 2.25 0 0121 8.25v11.25A2.25 2.25 0 0118.75 21.75h-6A2.25 2.25 0 0110.5 19.5V8.25A2.25 2.25 0 0112.75 6z" />
                            </svg>
                            Copy Last Month
                        </button>
                    </form>
                @endif

                <a href="{{ route('budgets.create', ['month' => $month]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Budget Target
                </a>
            </div>
        </div>

        {{-- Alerts Section if any over budget --}}
        @if($overBudgetCount > 0)
            <div class="p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 flex items-start gap-3 text-rose-900 shadow-sm animate-in fade-in">
                <div class="p-1.5 rounded-xl bg-rose-100 text-rose-600 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-rose-800">Budget Overrun Alert</h4>
                    <p class="text-xs text-rose-700 mt-0.5">
                        You have exceeded your spending limit in <strong>{{ $overBudgetCount }}</strong> {{ Str::plural('category', $overBudgetCount) }} this month by a total of <strong>{{ currency_symbol() }}{{ number_format($overallOverrun, 2) }}</strong>.
                    </p>
                </div>
            </div>
        @endif

        {{-- KPI Overview Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Budgeted --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Budgeted</span>
                    <span class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-slate-900">
                    {{ currency_symbol() }}{{ number_format($totalBudgeted, 2) }}
                </p>
                <p class="text-[11px] text-slate-400">Across {{ $budgets->count() }} active targets</p>
            </div>

            {{-- Total Spent --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Actual Spent</span>
                    <span class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-xs font-mono font-bold">
                        {{ $overallPercentage }}%
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-slate-900">
                    {{ currency_symbol() }}{{ number_format($totalSpent, 2) }}
                </p>
                {{-- Overall mini progress bar --}}
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full {{ $totalSpent > $totalBudgeted ? 'bg-rose-500' : ($overallPercentage >= 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ min(100, $overallPercentage) }}%"></div>
                </div>
            </div>

            {{-- Remaining Safe Balance --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider {{ $totalSpent > $totalBudgeted ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ $totalSpent > $totalBudgeted ? 'Net Overrun' : 'Safe Remaining' }}
                    </span>
                    <span class="w-8 h-8 rounded-xl {{ $totalSpent > $totalBudgeted ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono {{ $totalSpent > $totalBudgeted ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ currency_symbol() }}{{ number_format($totalSpent > $totalBudgeted ? $overallOverrun : $overallRemaining, 2) }}
                </p>
                <p class="text-[11px] text-slate-400">
                    {{ $totalSpent > $totalBudgeted ? 'Budget exceeded' : 'Available for remainder of month' }}
                </p>
            </div>

            {{-- Health Breakdown --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Health Status</span>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $onTrackCount }} On Track
                    </span>
                    @if($warningCount > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                            {{ $warningCount }} Near
                        </span>
                    @endif
                    @if($overBudgetCount > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                            {{ $overBudgetCount }} Over
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-400">Based on alert threshold</p>
            </div>
        </div>

        {{-- Unbudgeted Spending Banner if any --}}
        @if($unbudgetedSpending > 0)
            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70 flex items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-2.5 text-amber-900">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>You have <strong>{{ currency_symbol() }}{{ number_format($unbudgetedSpending, 2) }}</strong> in expenses this month without an assigned budget target.</span>
                </div>
                <a href="{{ route('budgets.create', ['month' => $month]) }}" class="font-bold text-amber-700 hover:text-amber-800 underline shrink-0">
                    Create Budget &rarr;
                </a>
            </div>
        @endif

        {{-- Budget Cards Grid / List --}}
        @if($budgets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($budgets as $budget)
                    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200 space-y-4">
                        {{-- Top Category & Status Header --}}
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-700 font-bold text-sm">
                                    {{ substr($budget->category ? $budget->category->name : 'C', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 tracking-tight">
                                        {{ $budget->category ? $budget->category->name : 'Unassigned Category' }}
                                    </h3>
                                    <p class="text-[11px] text-slate-400">
                                        Alert when &gt; {{ $budget->alert_threshold }}%
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $budget->status_badge_classes }}">
                                    {{ $budget->status_label }}
                                </span>
                                
                                {{-- Actions dropdown or buttons --}}
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('budgets.edit', $budget) }}"
                                        class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Budget">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('Remove budget for this category?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Budget">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Financial Progress Bar --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="font-bold text-slate-900">
                                    {{ currency_symbol() }}{{ number_format($budget->spent_amount, 2) }}
                                    <span class="font-normal text-slate-400">spent of {{ currency_symbol() }}{{ number_format($budget->amount, 2) }}</span>
                                </span>
                                <span class="font-bold {{ $budget->is_over_budget ? 'text-rose-600' : ($budget->is_near_limit ? 'text-amber-600' : 'text-slate-600') }}">
                                    {{ $budget->spent_percentage }}%
                                </span>
                            </div>

                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200/50">
                                <div class="h-2 rounded-full {{ $budget->progress_bar_classes }} transition-all duration-500"
                                    style="width: {{ $budget->progress_width }}%"></div>
                            </div>
                        </div>

                        {{-- Remaining or Overrun Footer --}}
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                            <span class="text-slate-500">
                                @if($budget->is_over_budget)
                                    <span class="text-rose-600 font-semibold font-mono">Over budget by +{{ currency_symbol() }}{{ number_format($budget->overrun_amount, 2) }}</span>
                                @else
                                    <span class="text-emerald-600 font-semibold font-mono">{{ currency_symbol() }}{{ number_format($budget->remaining_amount, 2) }}</span> remaining
                                @endif
                            </span>

                            @if($budget->notes)
                                <span class="text-[11px] text-slate-400 truncate max-w-[160px]" title="{{ $budget->notes }}">
                                    {{ $budget->notes }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-12 text-center shadow-xl shadow-slate-900/5 max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">No Budgets Set for {{ $monthLabel }}</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Setting monthly spending limits per category helps prevent overspending and gives you full control over your cash flow.
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 pt-2">
                    <a href="{{ route('budgets.create', ['month' => $month]) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition-all">
                        Create First Budget
                    </a>
                    @if($hasPreviousBudgets)
                        <form method="POST" action="{{ route('budgets.copy-previous') }}" class="inline">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                                Copy Last Month's Budgets
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
