<x-layouts.app :title="'Tax Deductions & Tax Season Export'">
    @section('page-title', 'Tax Deductions & Export')

    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.75l-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5v16.5l1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5v-16.5zM9.75 9h.008v.008H9.75V9zm4.5 6h.008v.008H14.25V15z" />
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Tax Deductions & Tax Season Export</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $dates['label'] }} &bull; Standard: {{ $taxSettings['country'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons & Year Selector --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Fiscal Year Dropdown / Tabs --}}
                <div class="flex items-center bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-1 shadow-xs">
                    @foreach($availableYears as $year)
                        <a href="{{ route('taxes.index', ['year' => $year]) }}"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $dates['year'] === $year ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            FY {{ $year }}
                        </a>
                    @endforeach
                </div>

                {{-- Tax Settings Modal Button --}}
                <button type="button" onclick="document.getElementById('tax-settings-modal').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white/80 border border-slate-200/80 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Tax Profile
                </button>

                {{-- Export CSV --}}
                <a href="{{ route('taxes.export.csv', ['year' => $dates['year']]) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white/80 border border-slate-200/80 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export CSV
                </a>

                {{-- Accountant Statement Pack --}}
                <a href="{{ route('taxes.report', ['year' => $dates['year']]) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:to-orange-500 shadow-md shadow-amber-600/20 transition-all active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.077-.4-2.222-.4-3.418 0-4.97 3.358-9 7.5-9s7.5 4.03 7.5 9c0 1.196-.16 2.341-.4 3.418M6.72 13.829C7.45 15.655 9.475 17 12 17c2.525 0 4.55-1.345 5.28-3.171M6.72 13.829A11.953 11.953 0 0112 13.5c1.884 0 3.655.437 5.28 1.218M3 20.25h18" />
                    </svg>
                    Accountant Pack (Print / PDF)
                </a>
            </div>
        </div>

        {{-- 4 KPI Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            {{-- Total Deductions Claimed --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-xl shadow-slate-900/5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-br from-amber-500/10 to-orange-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Deductions Claimed</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.75l-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5v16.5l1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5v-16.5zM9.75 9h.008v.008H9.75V9zm4.5 6h.008v.008H14.25V15z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-tabular">
                    {{ format_currency($taxSummary['total_deductible']) }}
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-500 font-medium">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold text-[11px] border border-amber-200/60">
                        {{ $taxSummary['deductible_count'] }} transactions
                    </span>
                    <span>in FY {{ $dates['year'] }}</span>
                </div>
            </div>

            {{-- Estimated Tax Shield / Savings --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-xl shadow-slate-900/5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-br from-emerald-500/10 to-teal-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Est. Tax Shield (Savings)</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight font-tabular">
                    {{ format_currency($taxSummary['estimated_tax_savings']) }}
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-500 font-medium">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px] border border-emerald-200/60">
                        {{ $taxSummary['tax_rate'] }}% Tax Bracket
                    </span>
                    <span>Taxes saved</span>
                </div>
            </div>

            {{-- Deductibility Ratio --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-xl shadow-slate-900/5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-br from-indigo-500/10 to-blue-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Deductibility Ratio</span>
                    <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200/60">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-indigo-600 tracking-tight font-tabular">
                    {{ $taxSummary['deductible_ratio'] }}%
                </div>
                <div class="mt-2.5">
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $taxSummary['deductible_ratio']) }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-400 font-medium mt-1">
                        <span>{{ format_currency($taxSummary['total_deductible']) }} claimed</span>
                        <span>{{ format_currency($taxSummary['total_expense']) }} total</span>
                    </div>
                </div>
            </div>

            {{-- Net Estimated Taxable Income --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-xl shadow-slate-900/5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-br from-purple-500/10 to-pink-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Est. Taxable Net Income</span>
                    <span class="p-2 rounded-xl bg-purple-50 text-purple-600 border border-purple-200/60">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-tabular">
                    {{ format_currency($taxSummary['taxable_income']) }}
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-500 font-medium">
                    <span>Income: {{ format_currency($taxSummary['total_income']) }}</span>
                    <span>&bull;</span>
                    <span class="text-amber-600 font-semibold">{{ $taxSummary['unreviewed_count'] }} to review</span>
                </div>
            </div>
        </div>

        {{-- Schedule of Deductions Breakdown & Monthly Timeline --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left 2 Cols: Schedule of Deductions by Tax Category --}}
            <div class="lg:col-span-2 bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Schedule of Deductions</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Grouped by statutory deduction schedules for CPA filing</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400 font-mono">{{ $categoryBreakdown->count() }} Schedules Active</span>
                </div>

                @if($categoryBreakdown->isEmpty())
                    <div class="py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-40 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14.25l6-6m4.5-3.75l-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5v16.5l1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5 1.5 1.5 1.5-1.5v-16.5zM9.75 9h.008v.008H9.75V9zm4.5 6h.008v.008H14.25V15z" />
                        </svg>
                        <p class="text-sm font-semibold text-slate-600">No tax-deductible expenses claimed yet for FY {{ $dates['year'] }}.</p>
                        <p class="text-xs text-slate-400 mt-1">Review your outlays below or record a transaction with "Claim as Tax Deductible" enabled.</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 sm:-mx-8">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 sm:px-8 py-3">Tax Category & Schedule</th>
                                    <th class="px-4 py-3 text-center">Txns</th>
                                    <th class="px-4 py-3 text-right">Gross Outlay</th>
                                    <th class="px-4 py-3 text-right">Avg Rate</th>
                                    <th class="px-4 py-3 text-right">Claimed Deductible</th>
                                    <th class="px-6 sm:px-8 py-3 text-right">% Share</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach($categoryBreakdown as $row)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-6 sm:px-8 py-3.5">
                                            <div class="font-bold text-slate-800">{{ $row->name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $row->tax_schedule }}</div>
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-600">
                                            {{ $row->count }}
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-medium text-slate-500 font-tabular">
                                            {{ format_currency($row->total_gross) }}
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-bold text-slate-700">
                                            {{ $row->avg_percentage }}%
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-black text-amber-700 font-tabular">
                                            {{ format_currency($row->total_deductible) }}
                                        </td>
                                        <td class="px-6 sm:px-8 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="font-extrabold text-slate-800 font-tabular">{{ $row->percentage_of_total }}%</span>
                                                <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $row->percentage_of_total }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Right Col: Tax Profile & Monthly Accrual Card --}}
            <div class="space-y-6">
                {{-- Tax Profile Summary Card --}}
                <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl shadow-slate-900/10">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            CPA Filing Profile
                        </span>
                        <span class="text-xs text-slate-400">{{ $dates['label'] }}</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-center py-1.5 border-b border-white/10">
                            <span class="text-slate-400">Entity / Taxpayer:</span>
                            <span class="font-bold text-white">{{ $taxSettings['entity_name'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-white/10">
                            <span class="text-slate-400">Tax ID / TIN / EIN:</span>
                            <span class="font-mono font-bold text-amber-300">{{ $taxSettings['tax_id'] ?: 'Not Specified' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-white/10">
                            <span class="text-slate-400">Estimated Tax Bracket:</span>
                            <span class="font-bold text-emerald-400">{{ $taxSettings['tax_rate'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-white/10">
                            <span class="text-slate-400">Fiscal Year Start:</span>
                            <span class="font-bold text-white">{{ \Carbon\Carbon::createFromDate(2026, $taxSettings['start_month'], 1)->format('F 1') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-slate-400">Tax Standard:</span>
                            <span class="font-bold text-white">{{ $taxSettings['country'] }}</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-white/10 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Estimated Tax Shield</div>
                            <div class="text-xl font-black text-amber-300 font-tabular">{{ format_currency($taxSummary['estimated_tax_savings']) }}</div>
                        </div>
                        <button type="button" onclick="document.getElementById('tax-settings-modal').classList.remove('hidden')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 transition text-white border border-white/20">
                            Edit Profile
                        </button>
                    </div>
                </div>

                {{-- Monthly Timeline Card --}}
                <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-xl shadow-slate-900/5">
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight mb-1">Monthly Deduction Timeline</h3>
                    <p class="text-xs text-slate-500 mb-4">Deductible outlays accrued across the fiscal year</p>

                    <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                        @php
                            $maxMonthly = max(1, collect($monthlyBreakdown)->max('deductible_amount'));
                        @endphp
                        @foreach($monthlyBreakdown as $m)
                            <div>
                                <div class="flex justify-between items-center text-xs mb-1">
                                    <span class="font-semibold text-slate-700">{{ $m['label'] }}</span>
                                    <span class="font-bold text-amber-800 font-tabular">{{ format_currency($m['deductible_amount']) }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-1.5 rounded-full" style="width: {{ round(($m['deductible_amount'] / $maxMonthly) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Deductible Ledger & Unreviewed Outlays Tabs --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5" x-data="{ tab: 'ledger' }">
            {{-- Tabs & Filters Bar --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl border border-slate-200/60 w-fit">
                    <button type="button" @click="tab = 'ledger'"
                        :class="tab === 'ledger' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                        <span>🏷️ Claimed Deductions</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold">
                            {{ $deductibleTransactions->total() }}
                        </span>
                    </button>
                    <button type="button" @click="tab = 'review'"
                        :class="tab === 'review' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                        <span>🔍 Fast Review (Unclaimed)</span>
                        @if($unreviewedExpenses->total() > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800 font-bold">
                                {{ $unreviewedExpenses->total() }}
                            </span>
                        @endif
                    </button>
                </div>

                {{-- Ledger Search & Filter Form --}}
                <form method="GET" action="{{ route('taxes.index') }}" class="flex flex-wrap items-center gap-2" x-show="tab === 'ledger'">
                    <input type="hidden" name="year" value="{{ $dates['year'] }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description/notes..."
                            class="px-3.5 py-2 pl-9 rounded-xl border border-slate-200 bg-white/70 text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 w-48 sm:w-56">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <select name="tax_category" onchange="this.form.submit()"
                        class="px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500">
                        <option value="">All Tax Categories</option>
                        @foreach($taxCategories as $code => $cat)
                            <option value="{{ $code }}" {{ request('tax_category') == $code ? 'selected' : '' }}>
                                {{ $cat['name'] }}
                            </option>
                        @endforeach
                    </select>

                    @if(request('search') || request('tax_category'))
                        <a href="{{ route('taxes.index', ['year' => $dates['year']]) }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition" title="Clear filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </a>
                    @endif
                </form>
            </div>

            {{-- TAB 1: Claimed Deductions Ledger --}}
            <div x-show="tab === 'ledger'">
                @if($deductibleTransactions->isEmpty())
                    <div class="py-12 text-center text-slate-400">
                        <p class="text-sm font-semibold text-slate-600">No deductible transactions match your filters.</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 sm:-mx-8">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 sm:px-8 py-3">Date</th>
                                    <th class="px-4 py-3">Tax Category</th>
                                    <th class="px-4 py-3">Account / Payee</th>
                                    <th class="px-4 py-3 text-right">Native Outlay</th>
                                    <th class="px-4 py-3 text-center">Deduction %</th>
                                    <th class="px-4 py-3 text-right">Claimed Deductible</th>
                                    <th class="px-4 py-3">Justification & Notes</th>
                                    <th class="px-6 sm:px-8 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach($deductibleTransactions as $txn)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-6 sm:px-8 py-3.5 whitespace-nowrap text-slate-600 font-medium font-mono">
                                            {{ $txn->date->format(app_date_format()) }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">{{ $txn->tax_category_label ?? 'General Deduction' }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $txn->expenseCategory?->name ?? 'Expense' }}</div>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-slate-600 font-medium">
                                            {{ $txn->account?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-right font-semibold text-slate-800 font-tabular">
                                            <div>{{ format_currency($txn->amount, $txn->currency) }}</div>
                                            @if($txn->currency && $txn->currency !== (get_setting('currency_code', 'BDT')))
                                                <div class="text-[10px] text-slate-400 font-normal">≈ {{ format_currency($txn->base_amount) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                {{ (float) $txn->deduction_percentage }}%
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-right font-black text-amber-800 font-tabular">
                                            {{ format_currency($txn->tax_deductible_amount) }}
                                        </td>
                                        <td class="px-4 py-3.5 max-w-xs truncate text-slate-500">
                                            <span class="font-medium text-slate-700">{{ $txn->tax_notes ?? $txn->description ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 sm:px-8 py-3.5 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('transactions.edit', $txn) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Tax Fields">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                                </a>
                                                <form method="POST" action="{{ route('taxes.toggle', $txn) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Unclaim / Remove Deduction" onclick="return confirm('Remove tax deductible status for this transaction?')">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        {{ $deductibleTransactions->links() }}
                    </div>
                @endif
            </div>

            {{-- TAB 2: Fast Review (Unclaimed Expenses) --}}
            <div x-show="tab === 'review'">
                <div class="mb-4 p-4 rounded-2xl bg-amber-50/60 border border-amber-200/60 text-xs text-amber-900 flex items-center justify-between">
                    <div>
                        <span class="font-bold">Fast Review Workspace:</span> Click <strong>"Claim Deduction"</strong> on any business outlay to immediately assign statutory deduction status and increase your tax savings.
                    </div>
                </div>

                @if($unreviewedExpenses->isEmpty())
                    <div class="py-12 text-center text-slate-400">
                        <p class="text-sm font-semibold text-emerald-600">🎉 Great job! All expenses in this fiscal year have been reviewed.</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 sm:-mx-8">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 sm:px-8 py-3">Date</th>
                                    <th class="px-4 py-3">Category</th>
                                    <th class="px-4 py-3">Account</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3 text-right">Amount</th>
                                    <th class="px-6 sm:px-8 py-3 text-right">Quick Claim Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach($unreviewedExpenses as $txn)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-6 sm:px-8 py-3.5 whitespace-nowrap text-slate-600 font-medium font-mono">
                                            {{ $txn->date->format(app_date_format()) }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap font-bold text-slate-800">
                                            {{ $txn->expenseCategory?->name ?? 'Uncategorized' }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-slate-600 font-medium">
                                            {{ $txn->account?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3.5 max-w-xs truncate text-slate-600">
                                            {{ $txn->description ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-right font-extrabold text-slate-900 font-tabular">
                                            {{ format_currency($txn->amount, $txn->currency) }}
                                        </td>
                                        <td class="px-6 sm:px-8 py-3.5 whitespace-nowrap text-right">
                                            <form method="POST" action="{{ route('taxes.toggle', $txn) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 shadow-sm transition active:scale-[0.98]">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                                    Claim Deduction
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        {{ $unreviewedExpenses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tax Profile & Settings Modal --}}
    <div id="tax-settings-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-bold text-slate-900">Tax Profile & Fiscal Year</h3>
                </div>
                <button type="button" onclick="document.getElementById('tax-settings-modal').classList.add('hidden')" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('taxes.settings.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="tax_entity_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Legal Entity / Taxpayer Name</label>
                    <input type="text" name="tax_entity_name" id="tax_entity_name" value="{{ $taxSettings['entity_name'] }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                </div>

                <div>
                    <label for="tax_id_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Tax ID / TIN / EIN / VAT Number</label>
                    <input type="text" name="tax_id_number" id="tax_id_number" value="{{ $taxSettings['tax_id'] }}" placeholder="e.g. 12-3456789 or TIN-8829471"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tax_fiscal_year_start_month" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Fiscal Year Starts</label>
                        <select name="tax_fiscal_year_start_month" id="tax_fiscal_year_start_month"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500">
                            <option value="1" {{ $taxSettings['start_month'] == 1 ? 'selected' : '' }}>January (Calendar Year)</option>
                            <option value="4" {{ $taxSettings['start_month'] == 4 ? 'selected' : '' }}>April (UK, India, etc.)</option>
                            <option value="7" {{ $taxSettings['start_month'] == 7 ? 'selected' : '' }}>July (Bangladesh, Australia)</option>
                            <option value="10" {{ $taxSettings['start_month'] == 10 ? 'selected' : '' }}>October (US Federal)</option>
                        </select>
                    </div>

                    <div>
                        <label for="tax_estimated_rate" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Est. Tax Bracket (%)</label>
                        <div class="relative">
                            <input type="number" name="tax_estimated_rate" id="tax_estimated_rate" step="0.5" min="0" max="100" value="{{ $taxSettings['tax_rate'] }}" required
                                class="block w-full px-3.5 py-2.5 pr-8 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-bold font-mono focus:outline-none focus:border-amber-500">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 font-bold text-xs">%</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="tax_country_standard" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Reporting Standard</label>
                    <select name="tax_country_standard" id="tax_country_standard"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500">
                        <option value="GLOBAL" {{ $taxSettings['country'] == 'GLOBAL' ? 'selected' : '' }}>Global Standard (General Business)</option>
                        <option value="US_IRS" {{ $taxSettings['country'] == 'US_IRS' ? 'selected' : '' }}>United States (IRS Schedule C / 1099)</option>
                        <option value="BD_NBR" {{ $taxSettings['country'] == 'BD_NBR' ? 'selected' : '' }}>Bangladesh (NBR Income Tax Act)</option>
                        <option value="UK_HMRC" {{ $taxSettings['country'] == 'UK_HMRC' ? 'selected' : '' }}>United Kingdom (HMRC Self-Assessment)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('tax-settings-modal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 shadow-md shadow-amber-600/20 transition active:scale-[0.98]">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
