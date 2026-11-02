<x-layouts.app :title="'Savings Vaults & Goals'">
    @section('page-title', 'Savings Vaults & Goals')

    <div class="space-y-6">
        {{-- Topbar Actions & Title --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Savings Vaults & Goals</h1>
                <p class="text-xs text-slate-500 mt-0.5">Ring-fence your money for emergency funds, capital expenses, tax reserves, and life milestones.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('goals.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Savings Vault
                </a>
            </div>
        </div>

        {{-- KPI Overview Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Vault Savings --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Total Saved in Vaults</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-emerald-600">
                    {{ format_currency($totalVaultBalance ?? 0) }}
                </p>
                <p class="text-[11px] text-slate-400">
                    Allocated across all financial targets
                </p>
            </div>

            {{-- Total Active Target --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Active Target Sum</span>
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-slate-900">
                    {{ format_currency($totalTargetAmount ?? 0) }}
                </p>
                <p class="text-[11px] text-slate-400">
                    Goal destination for active vaults
                </p>
            </div>

            {{-- Overall Progress --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Portfolio Progress</span>
                    <span class="text-xs font-bold font-mono text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200/60">
                        {{ $overallProgress ?? 0 }}%
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500"
                        style="width: {{ min(100, $overallProgress ?? 0) }}%"></div>
                </div>
                <p class="text-[11px] text-slate-400">
                    {{ format_currency(max(0, ($totalTargetAmount ?? 0) - ($totalVaultBalance ?? 0))) }} remaining to complete
                </p>
            </div>

            {{-- Vault Health & Counts --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2.5">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Vaults Status</span>
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                        {{ ($activeGoals ?? collect())->count() }} Active
                    </span>
                    @if(($completedGoals ?? collect())->count() > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            {{ ($completedGoals ?? collect())->count() }} Reached 🎉
                        </span>
                    @endif
                    @if(($pausedGoals ?? collect())->count() > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                            {{ ($pausedGoals ?? collect())->count() }} Paused
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-400">Secure ring-fenced allocations</p>
            </div>
        </div>

        {{-- Filter Tabs and Search Form --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-3">
            <form method="GET" action="{{ route('goals.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                {{-- Status Filter Tabs --}}
                <div class="flex items-center gap-1 overflow-x-auto pb-1 lg:pb-0">
                    <a href="{{ route('goals.index', array_merge(request()->except('status', 'page'))) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        All Vaults ({{ $goals->total() }})
                    </a>
                    <a href="{{ route('goals.index', array_merge(request()->except('page'), ['status' => 'active'])) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'active' ? 'bg-teal-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Active
                    </a>
                    <a href="{{ route('goals.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'completed' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Completed
                    </a>
                    <a href="{{ route('goals.index', array_merge(request()->except('page'), ['status' => 'paused'])) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'paused' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        Paused
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Search Input --}}
                    <div class="relative min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search vaults..."
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl text-xs border border-slate-200 bg-white/60 focus:bg-white focus:outline-none focus:border-emerald-500 transition">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>

                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                        Filter
                    </button>

                    @if(request()->hasAny(['status', 'search', 'color']))
                        <a href="{{ route('goals.index') }}" class="px-2.5 py-1.5 rounded-xl text-xs text-rose-600 hover:bg-rose-50 transition" title="Clear Filters">
                            ✕ Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Vaults Card Grid --}}
        @if($goals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($goals as $goal)
                    @php
                        $theme = $goal->color_theme;
                        $statusBadge = $goal->status_badge;
                    @endphp
                    <div class="group relative bg-white/90 backdrop-blur-xl border border-slate-200/80 hover:border-slate-300 rounded-3xl p-6 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                        <div>
                            {{-- Header: Icon + Title + Badges --}}
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr {{ $theme['gradient'] }} text-white flex items-center justify-center font-bold text-base shadow-md {{ $theme['glow'] }} shrink-0">
                                        @if($goal->is_locked)
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('goals.show', $goal) }}" class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition truncate block">
                                            {{ $goal->name }}
                                        </a>
                                        @if($goal->account)
                                            <p class="text-[11px] text-slate-400 truncate flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                                Linked: {{ $goal->account->name }}
                                            </p>
                                        @else
                                            <p class="text-[11px] text-slate-400">Virtual Allocation Vault</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Lock / Status Pill --}}
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($goal->is_locked)
                                        <span class="p-1 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60 text-xs" title="Vault Locked Against Early Withdrawal">
                                            🔒
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $statusBadge['classes'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </div>
                            </div>

                            @if($goal->description)
                                <p class="text-xs text-slate-500 mb-4 line-clamp-2 leading-relaxed">{{ $goal->description }}</p>
                            @endif

                            {{-- Financial Balance & Target --}}
                            <div class="space-y-1.5 my-4">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-2xl font-extrabold font-mono text-slate-900">
                                        {{ format_currency($goal->current_amount) }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-400 font-mono">
                                        of {{ format_currency($goal->target_amount) }}
                                    </span>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5">
                                    <div class="h-full rounded-full bg-gradient-to-r {{ $theme['gradient'] }} transition-all duration-700 shadow-xs"
                                        style="width: {{ $goal->progress_percentage }}%"></div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                    <span class="font-bold text-slate-700">{{ $goal->progress_percentage }}% reached</span>
                                    <span>
                                        @if($goal->is_completed)
                                            <strong class="text-emerald-600 font-bold">Target Achieved 🎉</strong>
                                        @else
                                            {{ format_currency($goal->remaining_amount) }} remaining
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Pace & Deadline Badges --}}
                            <div class="p-3 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-1.5 my-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400">Target Date:</span>
                                    @if($goal->target_date)
                                        <span class="font-semibold {{ $goal->is_overdue ? 'text-rose-600' : 'text-slate-700' }}">
                                            {{ $goal->target_date->format('M d, Y') }}
                                            @if(!$goal->is_completed)
                                                @if($goal->is_overdue)
                                                    (Passed)
                                                @elseif($goal->days_remaining === 0)
                                                    (Today!)
                                                @else
                                                    ({{ $goal->days_remaining }}d left)
                                                @endif
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">No set deadline</span>
                                    @endif
                                </div>

                                @if(!$goal->is_completed && $goal->recommended_monthly_contribution)
                                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-200/50">
                                        <span class="text-slate-400">Suggested Pace:</span>
                                        <span class="font-bold font-mono text-emerald-700">
                                            ~{{ format_currency($goal->recommended_monthly_contribution) }} / mo
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Card Action Footer --}}
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2 mt-2">
                            <a href="{{ route('goals.show', $goal) }}"
                                class="inline-flex items-center justify-center flex-1 py-2 px-3 rounded-xl text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200/80 transition text-center">
                                View Vault Dossier
                            </a>

                            {{-- Quick Deposit Trigger --}}
                            <button type="button"
                                onclick="openQuickDepositModal('{{ $goal->id }}', '{{ addslashes($goal->name) }}', '{{ $goal->account_id }}')"
                                class="p-2 rounded-xl text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/70 transition"
                                title="Quick Deposit Funds">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>

                            <a href="{{ route('goals.edit', $goal) }}"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                title="Edit Vault">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $goals->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm">
                <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/20">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">No Savings Vaults Yet</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Create targeted savings vaults to allocate funds toward financial safety nets, tax provisions, dream vacations, or new equipment.
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                    <a href="{{ route('goals.create') }}"
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition">
                        Create Your First Vault
                    </a>
                </div>

                {{-- Fast-Track Ideas --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3">Popular Starting Goals</p>
                    <div class="flex flex-wrap items-center justify-center gap-2 text-xs">
                        <a href="{{ route('goals.create') }}?preset=emergency" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200/60 font-medium">
                            🛡️ 6-Month Emergency Fund
                        </a>
                        <a href="{{ route('goals.create') }}?preset=tax" class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition border border-indigo-200/60 font-medium">
                            💼 Annual Tax Reserve
                        </a>
                        <a href="{{ route('goals.create') }}?preset=tech" class="px-3 py-1.5 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 transition border border-sky-200/60 font-medium">
                            💻 Equipment & Tech Upgrade
                        </a>
                        <a href="{{ route('goals.create') }}?preset=vacation" class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 transition border border-amber-200/60 font-medium">
                            ✈️ Holiday & Travel
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Quick Deposit Modal --}}
    <div id="quickDepositModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Add Funds to Vault</h3>
                        <p id="modalVaultName" class="text-xs text-slate-400 font-medium">Goal Name</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickDepositModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <form id="quickDepositForm" method="POST" action="" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deposit Amount ({{ currency_symbol() }}) *</label>
                    <input type="number" step="0.01" min="0.01" name="amount" required autofocus
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-emerald-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Source Account (Optional)</label>
                    <select name="account_id" id="modalAccountId"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                        <option value="">No linked account (Virtual allocation)</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ format_currency($acc->balance) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="deduct_from_account" value="1" checked
                            class="w-4 h-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs text-slate-700 font-medium">Record as Transaction in Account Ledger</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1 pl-6">Keeps your bank/cash ledger in exact balance with vault deposits.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes (Optional)</label>
                    <input type="text" name="notes" placeholder="e.g. Monthly salary savings portion"
                        class="block w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs focus:outline-none focus:border-emerald-500">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeQuickDepositModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition">
                        Confirm Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openQuickDepositModal(goalId, goalName, defaultAccountId) {
            const form = document.getElementById('quickDepositForm');
            form.action = "{{ url('goals') }}/" + goalId + "/deposit";
            document.getElementById('modalVaultName').textContent = goalName;
            if (defaultAccountId) {
                document.getElementById('modalAccountId').value = defaultAccountId;
            }
            document.getElementById('quickDepositModal').classList.remove('hidden');
        }

        function closeQuickDepositModal() {
            document.getElementById('quickDepositModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
