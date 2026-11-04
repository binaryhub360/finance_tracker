<x-layouts.app :title="'Cash Flow Forecasting & Runway Analytics'">
    @section('page-title', 'Cash Flow & Runway')

    <div class="space-y-8" x-data="{
        scenario: '{{ $scenario }}',
        horizon: {{ $horizon }},
        lookback: {{ $lookback }},
        setScenario(s) {
            this.scenario = s;
            this.reload();
        },
        setHorizon(h) {
            this.horizon = h;
            this.reload();
        },
        reload() {
            window.location.href = '{{ route('cash-flow.index') }}?scenario=' + this.scenario + '&horizon=' + this.horizon + '&lookback=' + this.lookback;
        }
    }">
        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-tr from-teal-500/15 via-emerald-500/10 to-transparent text-emerald-600 border border-emerald-500/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Cash Flow & Runway Analytics</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-teal-50 text-teal-700 border border-teal-200">
                            Predictive Engine
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Simulate future liquidity, stress-test receivable collection risks, and monitor burn runway</p>
                </div>
            </div>

            {{-- Action & Controls Toolbar --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Horizon Switcher --}}
                <div class="inline-flex items-center bg-white border border-slate-200/80 rounded-2xl p-1 shadow-xs">
                    @foreach([30 => '30D', 60 => '60D', 90 => '90D', 180 => '180D'] as $hDays => $hLabel)
                        <button type="button" @click="setHorizon({{ $hDays }})"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $horizon === $hDays ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            {{ $hLabel }}
                        </button>
                    @endforeach
                </div>

                {{-- Scenario Switcher --}}
                <div class="inline-flex items-center bg-white border border-slate-200/80 rounded-2xl p-1 shadow-xs">
                    <button type="button" @click="setScenario('conservative')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $scenario === 'conservative' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                        title="Stress test: 75% invoice collection & +10% recurring expense buffer">
                        Conservative
                    </button>
                    <button type="button" @click="setScenario('baseline')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $scenario === 'baseline' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                        title="Standard confirmed receivables and subscriptions">
                        Baseline
                    </button>
                    <button type="button" @click="setScenario('optimistic')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $scenario === 'optimistic' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                        title="Optimistic: 100% prompt payments with revenue growth">
                        Optimistic
                    </button>
                </div>

                {{-- Export CSV --}}
                <a href="{{ route('cash-flow.export.csv', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>

        {{-- Runway Health Alert / Banner --}}
        @if ($forecast['summary']['dips_below_zero'])
            <div class="rounded-2xl bg-rose-50 border border-rose-200/80 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-rose-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-rose-900">Projected Liquidity Deficit Detected</h4>
                        <p class="text-xs text-rose-700 mt-0.5">
                            Under current <strong>{{ ucfirst($scenario) }}</strong> assumptions, your cash balance is projected to dip below zero on 
                            <strong class="font-mono text-rose-950">{{ \Carbon\Carbon::parse($forecast['summary']['first_negative_date'])->format('M d, Y') }}</strong>, 
                            reaching a trough of <strong>{{ number_format($forecast['summary']['lowest_balance'], 2) }} {{ $baseCurrency->code ?? 'BDT' }}</strong>.
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('invoices.create') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-all">
                        Create Invoice
                    </a>
                </div>
            </div>
        @elseif ($burnRate['status'] === 'positive')
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-emerald-950">Self-Sustaining Operations (Cash Flow Positive)</h4>
                        <p class="text-xs text-emerald-700 mt-0.5">
                            Average monthly historical income ({{ number_format($burnRate['avg_monthly_income'], 2) }} {{ $baseCurrency->code ?? 'BDT' }}) exceeds monthly burn by 
                            <strong>{{ number_format(abs($burnRate['avg_monthly_net_burn']), 2) }} {{ $baseCurrency->code ?? 'BDT' }}/mo</strong>. Your cash runway is indefinitely self-sustaining.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- 4 Primary KPI Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Current Liquid Cash --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Current Liquid Cash</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1 font-mono">
                            {{ number_format($burnRate['current_cash'], 2) }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-1">Across all active liquid accounts</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
            </div>

            {{-- Estimated Runway --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cash Runway</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">
                            @if ($burnRate['runway_months'] !== null)
                                {{ $burnRate['runway_months'] }} <span class="text-xs font-bold text-slate-500">Mos</span>
                            @else
                                <span class="text-emerald-600 text-lg">Self-Sustaining</span>
                            @endif
                        </h3>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $burnRate['status_badge'] }}">
                                {{ $burnRate['status_label'] }}
                            </span>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center border border-blue-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
            </div>

            {{-- Monthly Net Burn Rate --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Monthly Net Burn Rate</p>
                        <h3 class="text-2xl font-black font-mono mt-1 {{ $burnRate['avg_monthly_net_burn'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $burnRate['avg_monthly_net_burn'] > 0 ? '-' : '+' }}{{ number_format(abs($burnRate['avg_monthly_net_burn']), 2) }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-1">Out: {{ number_format($burnRate['avg_monthly_expense'], 0) }} | In: {{ number_format($burnRate['avg_monthly_income'], 0) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center border border-amber-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>
            </div>

            {{-- Projected Ending Balance --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Projected ({{ $horizon }}D Horizon)</p>
                        <h3 class="text-2xl font-black font-mono mt-1 {{ $forecast['summary']['ending_balance'] >= $burnRate['current_cash'] ? 'text-emerald-600' : 'text-slate-900' }}">
                            {{ number_format($forecast['summary']['ending_balance'], 2) }}
                        </h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-bold {{ $forecast['summary']['net_change'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $forecast['summary']['net_change'] >= 0 ? '▲ +' : '▼ ' }}{{ number_format($forecast['summary']['net_change'], 2) }}
                                ({{ $forecast['summary']['percentage_change'] }}%)
                            </span>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center border border-purple-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-indigo-500"></div>
            </div>
        </div>

        {{-- Scenario Stress-Test Comparison Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Conservative Card --}}
            <div class="rounded-2xl p-4.5 border transition-all cursor-pointer {{ $scenario === 'conservative' ? 'bg-amber-50/60 border-amber-300 ring-2 ring-amber-500/20 shadow-sm' : 'bg-white border-slate-200/80 hover:border-slate-300' }}"
                @click="setScenario('conservative')">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Conservative (Stress Test)
                    </span>
                    @if ($scenario === 'conservative')
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-200/60 text-amber-900">Active</span>
                    @endif
                </div>
                <h4 class="text-xl font-black text-slate-900 font-mono">{{ number_format($comparison['conservative']['ending_balance'], 2) }}</h4>
                <p class="text-[11px] text-slate-500 mt-1">75% receivables collected + 10% unexpected expense overhead buffer</p>
            </div>

            {{-- Baseline Card --}}
            <div class="rounded-2xl p-4.5 border transition-all cursor-pointer {{ $scenario === 'baseline' ? 'bg-slate-50 border-slate-900 ring-2 ring-slate-900/10 shadow-sm' : 'bg-white border-slate-200/80 hover:border-slate-300' }}"
                @click="setScenario('baseline')">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                        Baseline (Scheduled)
                    </span>
                    @if ($scenario === 'baseline')
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-900 text-white">Active</span>
                    @endif
                </div>
                <h4 class="text-xl font-black text-slate-900 font-mono">{{ number_format($comparison['baseline']['ending_balance'], 2) }}</h4>
                <p class="text-[11px] text-slate-500 mt-1">Standard 100% scheduled invoice payments and recurring bills</p>
            </div>

            {{-- Optimistic Card --}}
            <div class="rounded-2xl p-4.5 border transition-all cursor-pointer {{ $scenario === 'optimistic' ? 'bg-emerald-50/60 border-emerald-300 ring-2 ring-emerald-500/20 shadow-sm' : 'bg-white border-slate-200/80 hover:border-slate-300' }}"
                @click="setScenario('optimistic')">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Optimistic (Growth)
                    </span>
                    @if ($scenario === 'optimistic')
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-200/60 text-emerald-900">Active</span>
                    @endif
                </div>
                <h4 class="text-xl font-black text-slate-900 font-mono">{{ number_format($comparison['optimistic']['ending_balance'], 2) }}</h4>
                <p class="text-[11px] text-slate-500 mt-1">100% prompt invoice collection with +5% revenue upside factor</p>
            </div>
        </div>

        {{-- Chart.js Forecast Timeline Area Chart --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight">Projected Cash Balance Trajectory</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Chronological {{ $horizon }}-day balance simulation under {{ ucfirst($scenario) }} conditions</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-bold text-slate-600">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span>Projected Balance</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-1 bg-rose-400 border border-rose-400 border-dashed"></span>
                        <span class="text-rose-600">Zero Line</span>
                    </div>
                </div>
            </div>

            <div class="relative h-72 sm:h-80 w-full">
                <canvas id="cashFlowForecastChart"></canvas>
            </div>
        </div>

        {{-- Two-Column Grid: Detailed Schedule & Upcoming Events Feed --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Column 1: Weekly Breakdown Table (2 spans) --}}
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-slate-900 tracking-tight">Period Schedule Breakdown</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Weekly net cash movements and ending balances</p>
                    </div>
                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-700">
                        {{ count($forecast['periods']) }} Weeks
                    </span>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <th class="py-3 px-4">Period</th>
                                <th class="py-3 px-4 text-right">Starting</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Inflows</th>
                                <th class="py-3 px-4 text-right text-rose-700">Outflows</th>
                                <th class="py-3 px-4 text-right">Net Change</th>
                                <th class="py-3 px-4 text-right font-black">Ending Cash</th>
                                <th class="py-3 px-4 text-center">Safety</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($forecast['periods'] as $p)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ $p['label'] }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $p['start_date'] }} – {{ $p['end_date'] }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-600 whitespace-nowrap">
                                        {{ number_format($p['starting_balance'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600 whitespace-nowrap">
                                        {{ $p['inflows'] > 0 ? '+' . number_format($p['inflows'], 2) : '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-rose-600 whitespace-nowrap">
                                        {{ $p['outflows'] > 0 ? '-' . number_format($p['outflows'], 2) : '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono whitespace-nowrap {{ $p['net_change'] >= 0 ? 'text-emerald-700 font-bold' : 'text-rose-700 font-bold' }}">
                                        {{ $p['net_change'] >= 0 ? '+' : '' }}{{ number_format($p['net_change'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-black whitespace-nowrap {{ $p['ending_balance'] < 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                        {{ number_format($p['ending_balance'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if ($p['status'] === 'safe')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Safe
                                            </span>
                                        @elseif ($p['status'] === 'tight')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Tight
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Deficit
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">No projection periods generated.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Column 2: Scheduled Cash Flow Events Feed (1 span) --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-black text-slate-900 tracking-tight">Discrete Event Feed</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Next upcoming receivables & bills</p>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-slate-100 text-slate-600">
                        {{ count($forecast['upcoming_events']) }}
                    </span>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-[480px] pr-1">
                    @forelse($forecast['upcoming_events'] as $evt)
                        <div class="flex items-center justify-between p-3 rounded-2xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $evt['type'] === 'inflow' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border border-rose-500/20' }}">
                                    @if ($evt['type'] === 'inflow')
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-slate-800 truncate" title="{{ $evt['title'] }}">{{ $evt['title'] }}</h5>
                                    <p class="text-[10px] text-slate-400 truncate">{{ \Carbon\Carbon::parse($evt['date'])->format('M d, Y') }} &bull; {{ $evt['subtitle'] }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-mono text-xs font-black {{ $evt['type'] === 'inflow' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $evt['type'] === 'inflow' ? '+' : '-' }}{{ number_format($evt['amount'], 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No discrete scheduled invoices or subscriptions recorded.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('cashFlowForecastChart').getContext('2d');
            
            const labels = @json($forecast['chart_data']['labels']);
            const balances = @json($forecast['chart_data']['balances']);
            const zeroLine = new Array(labels.length).fill(0);

            // Create gradient for balance area
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Projected Balance',
                            data: balances,
                            borderColor: '#10b981',
                            borderWidth: 2.5,
                            pointRadius: labels.length > 30 ? 0 : 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            fill: true,
                            backgroundColor: gradient,
                            tension: 0.3,
                        },
                        {
                            label: 'Zero Liquidity Line',
                            data: zeroLine,
                            borderColor: '#f43f5e',
                            borderWidth: 1.5,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            padding: 12,
                            cornerRadius: 12,
                            boxPadding: 4,
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 1) return null;
                                    const val = Number(context.raw).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    return 'Balance: ' + val + ' {{ $baseCurrency->code ?? 'BDT' }}';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                },
                                color: '#94a3b8',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 10,
                            }
                        },
                        y: {
                            grid: {
                                color: '#f1f5f9',
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                },
                                color: '#94a3b8',
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>
