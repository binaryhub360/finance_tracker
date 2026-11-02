<x-layouts.app :title="'Subscriptions & Recurring Transactions'">
    @section('page-title', 'Subscriptions & Recurring')

    <div class="space-y-6">
        {{-- Topbar Actions & Title --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Subscriptions & Recurring</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track recurring subscriptions, SaaS, bills, retainers, and scheduled commitments.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if(($overdueCount ?? 0) > 0 || ($dueThisWeekCount ?? 0) > 0)
                    <form method="POST" action="{{ route('recurring.process-due') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 shadow-sm transition-all duration-200"
                            title="Process all active items due on or before today">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            Process Due Now
                        </button>
                    </form>
                @endif

                <a href="{{ route('recurring.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Recurring Item
                </a>
            </div>
        </div>

        {{-- Overdue Alert Banner if any overdue --}}
        @if(($overdueCount ?? 0) > 0)
            <div class="p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 flex items-start justify-between gap-3 text-rose-900 shadow-sm animate-in fade-in">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-xl bg-rose-100 text-rose-600 shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-rose-800">Overdue Recurring Items</h4>
                        <p class="text-xs text-rose-700 mt-0.5">
                            You have <strong>{{ $overdueCount }}</strong> recurring {{ Str::plural('commitment', $overdueCount) }} totaling <strong>{{ format_currency($overdueAmount ?? 0) }}</strong> whose renewal date has passed and needs logging.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('recurring.process-due') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-rose-700 hover:text-rose-900 bg-rose-100 hover:bg-rose-200 px-3 py-1.5 rounded-xl transition">
                        Log All Overdue
                    </button>
                </form>
            </div>
        @endif

        {{-- KPI Overview Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Monthly Recurring Outflow (Burn) --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Monthly Subscriptions</span>
                    <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25l-3-3m0 0l-3 3m3-3v7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-slate-900">
                    {{ format_currency($monthlyExpenses ?? 0) }}
                </p>
                <p class="text-[11px] text-slate-400">
                    Annual commitment: ~{{ format_currency($yearlyExpenses ?? 0) }}
                </p>
            </div>

            {{-- Monthly Recurring Income --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Recurring Inflow</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-emerald-600">
                    {{ format_currency($monthlyIncome ?? 0) }}
                </p>
                <p class="text-[11px] text-slate-400">
                    Retainers, dividends, and recurring revenue
                </p>
            </div>

            {{-- Due in Next 7 Days --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-600">Due Next 7 Days</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-mono font-bold">
                        {{ $dueThisWeekCount ?? 0 }}
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-slate-900">
                    {{ format_currency($dueThisWeekAmount ?? 0) }}
                </p>
                <p class="text-[11px] text-amber-600/80 font-medium">
                    {{ $dueThisWeekCount ?? 0 }} renewals requiring liquidity
                </p>
            </div>

            {{-- Portfolio Counts --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2.5">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Commitment Health</span>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $activeCount ?? 0 }} Active
                    </span>
                    @if(($pausedCount ?? 0) > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                            {{ $pausedCount }} Paused
                        </span>
                    @endif
                    @if(($overdueCount ?? 0) > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/60 animate-pulse">
                            {{ $overdueCount }} Overdue
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-400">Net monthly impact: {{ format_currency($netMonthly ?? 0) }}</p>
            </div>
        </div>

        {{-- Filter Tabs and Search Form --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-3">
            <form method="GET" action="{{ route('recurring.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                {{-- Type Tabs --}}
                <div class="flex items-center gap-1 overflow-x-auto pb-1 lg:pb-0">
                    <a href="{{ route('recurring.index', array_merge(request()->except('type', 'page'))) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ !request('type') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        All ({{ $recurring->total() }})
                    </a>
                    <a href="{{ route('recurring.index', array_merge(request()->except('page'), ['type' => 'expense'])) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('type') === 'expense' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Subscriptions & Expenses
                    </a>
                    <a href="{{ route('recurring.index', array_merge(request()->except('page'), ['type' => 'income'])) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('type') === 'income' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Retainers & Incomes
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Status Filter --}}
                    <select name="status" onchange="this.form.submit()"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    {{-- Due Timing Filter --}}
                    <select name="due" onchange="this.form.submit()"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">Any Due Date</option>
                        <option value="overdue" {{ request('due') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="today" {{ request('due') === 'today' ? 'selected' : '' }}>Due Today</option>
                        <option value="this_week" {{ request('due') === 'this_week' ? 'selected' : '' }}>Due This Week</option>
                        <option value="this_month" {{ request('due') === 'this_month' ? 'selected' : '' }}>Due This Month</option>
                    </select>

                    {{-- Account Filter --}}
                    <select name="account_id" onchange="this.form.submit()"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">All Accounts</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscriptions..."
                            class="pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500 w-44 sm:w-56">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>

                    @if(request()->anyFilled(['type', 'status', 'due', 'account_id', 'search']))
                        <a href="{{ route('recurring.index') }}" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg" title="Clear filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Recurring Items Grid --}}
        @if($recurring->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recurring as $item)
                    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-4 group">
                        <div class="space-y-3">
                            {{-- Header: Avatar, Name, Status Pill --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-2xl {{ $item->type === 'expense' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($item->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('recurring.show', $item) }}" class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition truncate block">
                                            {{ $item->name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                                            <span>{{ $item->category_name }}</span>
                                            <span>&bull;</span>
                                            <span class="truncate">{{ $item->account?->name ?? 'Unassigned' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $item->status_badge_classes }}">
                                    {{ $item->status }}
                                </span>
                            </div>

                            {{-- Financial & Frequency Overview --}}
                            <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Commitment</span>
                                    <span class="text-base font-extrabold font-mono text-slate-900">
                                        {{ format_currency($item->amount) }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-white border border-slate-200/60 text-slate-700">
                                        {{ $item->frequency_label }}
                                    </span>
                                    @if($item->frequency !== 'monthly' || $item->interval > 1)
                                        <p class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                            ~{{ format_currency($item->monthly_equivalent_amount) }}/mo
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Renewal Timing & Auto-log pill --}}
                            <div class="flex items-center justify-between text-xs pt-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold {{ $item->due_badge_classes }}">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $item->due_badge_label }}
                                </span>

                                @if($item->auto_log)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md" title="Automatically records transaction when due">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        Auto-log
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400" title="Requires manual payment log">Manual</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action Controls Footer --}}
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5">
                                {{-- 1-Click Log Occurrence --}}
                                @if($item->status === 'active')
                                    <form method="POST" action="{{ route('recurring.log', $item) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition active:scale-[0.98]"
                                            title="Record payment and advance next renewal date">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                            Log
                                        </button>
                                    </form>

                                    {{-- Skip Next Due Date --}}
                                    <form method="POST" action="{{ route('recurring.skip', $item) }}" onsubmit="return confirm('Skip next cycle ({{ $item->next_due_date->format('M d, Y') }}) without debiting account?');" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" title="Skip this cycle">
                                            Skip
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="flex items-center gap-1">
                                {{-- Pause / Resume Toggle --}}
                                <form method="POST" action="{{ route('recurring.toggle-pause', $item) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition"
                                        title="{{ $item->status === 'active' ? 'Pause subscription' : 'Resume subscription' }}">
                                        @if($item->status === 'active')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                {{-- Edit --}}
                                <a href="{{ route('recurring.edit', $item) }}" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>

                                {{-- Show / Dossier --}}
                                <a href="{{ route('recurring.show', $item) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="View details and payment history">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($recurring->hasPages())
                <div class="pt-4">
                    {{ $recurring->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-12 text-center shadow-xl shadow-slate-900/5 max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">No Subscriptions or Recurring Transactions Found</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Add regular expenses (like Netflix, AWS, office rent) or recurring income (like client retainers) to automate your ledger and forecast monthly burn rate.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('recurring.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition-all">
                        + Add First Subscription
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
