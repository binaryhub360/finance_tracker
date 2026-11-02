<x-layouts.app :title="'Currencies & Exchange Rates'">
    @section('page-title', 'Currencies & Rates')

    <div class="space-y-6">
        {{-- Topbar Actions & Title --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Currencies & Exchange Rates</h1>
                <p class="text-xs text-slate-500 mt-0.5">Real-time foreign exchange feeds, multi-currency ledger balances, and portfolio conversion.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Live Market Sync Button --}}
                <form method="POST" action="{{ route('currencies.sync') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 shadow-xs transition-all duration-200 active:scale-[0.98]"
                        title="Fetch latest forex market rates from Open Exchange Rates">
                        <svg class="w-4 h-4 text-emerald-600 animate-spin-hover" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Sync Live Rates Now
                    </button>
                </form>

                <button type="button" onclick="openAddCurrencyModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Currency
                </button>
            </div>
        </div>

        {{-- Top KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Base Currency --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Base Currency</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        {{ $baseCurrency->symbol ?? '৳' }}
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-slate-900">
                    {{ $baseCurrency->code ?? 'BDT' }}
                </p>
                <p class="text-[11px] text-slate-400">
                    {{ $baseCurrency->name ?? 'Bangladeshi Taka' }} (Primary Ledger)
                </p>
            </div>

            {{-- Active Currencies --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Active Currencies</span>
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                        {{ $activeCurrencies->count() }}
                    </span>
                </div>
                <p class="text-2xl font-bold font-mono text-indigo-600">
                    {{ $activeCurrencies->count() }} of {{ $currencies->count() }}
                </p>
                <p class="text-[11px] text-slate-400">
                    Enabled for accounts and transactions
                </p>
            </div>

            {{-- Forex Sync Status --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-teal-600">Market Sync Status</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <p class="text-base font-bold text-slate-900 truncate">
                    @if($lastSync)
                        {{ \Carbon\Carbon::parse($lastSync)->diffForHumans() }}
                    @else
                        System Baseline
                    @endif
                </p>
                <p class="text-[11px] text-slate-400">
                    Automated daily forex updates
                </p>
            </div>

            {{-- Key Benchmark --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-5 shadow-sm space-y-2">
                @php
                    $usd = $currencies->where('code', 'USD')->first();
                @endphp
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Benchmark (USD)</span>
                    <span class="text-xs font-mono font-bold text-slate-400">$</span>
                </div>
                <p class="text-2xl font-bold font-mono text-slate-900">
                    @if($usd && $baseCurrency && $baseCurrency->code !== 'USD')
                        {{ format_currency($usd->exchange_rate) }}
                    @else
                        1.0000
                    @endif
                </p>
                <p class="text-[11px] text-slate-400">
                    1 USD in {{ $baseCurrency->code ?? 'BDT' }}
                </p>
            </div>
        </div>

        {{-- Live Interactive Currency Converter Widget --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/10 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-700/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold tracking-tight text-white">Live Currency Converter</h3>
                        <p class="text-xs text-slate-400">Convert amounts instantly between any active currencies using live forex rates.</p>
                    </div>
                </div>

                <div id="unitRatePill" class="text-xs font-mono font-semibold px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-300">
                    1 {{ $convertFrom }} = {{ number_format($convertedResult / ($convertAmount ?: 1), 4) }} {{ $convertTo }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 items-center gap-4">
                {{-- Amount Input --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">You Send / Amount</label>
                    <input type="number" id="converterAmount" step="any" min="0.01" value="{{ $convertAmount }}"
                        class="block w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white font-mono text-lg font-bold focus:outline-none focus:border-emerald-500 transition">
                </div>

                {{-- From Currency Select --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">From</label>
                    <select id="converterFrom"
                        class="block w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white font-semibold text-sm focus:outline-none focus:border-emerald-500">
                        @foreach($activeCurrencies as $c)
                            <option value="{{ $c->code }}" {{ $convertFrom === $c->code ? 'selected' : '' }}>
                                {{ $c->code }} - {{ $c->name }} ({{ $c->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Swap Button --}}
                <div class="md:col-span-1 flex items-center justify-center pt-5">
                    <button type="button" id="converterSwap"
                        class="w-10 h-10 rounded-2xl bg-slate-700 hover:bg-slate-600 text-slate-200 flex items-center justify-center transition active:scale-95"
                        title="Swap currencies">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </button>
                </div>

                {{-- To Currency Select --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">To</label>
                    <select id="converterTo"
                        class="block w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white font-semibold text-sm focus:outline-none focus:border-emerald-500">
                        @foreach($activeCurrencies as $c)
                            <option value="{{ $c->code }}" {{ $convertTo === $c->code ? 'selected' : '' }}>
                                {{ $c->code }} - {{ $c->name }} ({{ $c->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Live Conversion Result Output --}}
            <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-xs text-slate-400 font-medium">Converted Value:</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span id="convertedResult" class="text-2xl sm:text-3xl font-extrabold font-mono text-emerald-400">
                            {{ format_currency($convertedResult, $convertTo) }}
                        </span>
                    </div>
                </div>

                <div class="text-xs text-slate-400 font-medium">
                    Calculated in real time based on active exchange matrix
                </div>
            </div>
        </div>

        {{-- Currencies Management Ledger Table --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Exchange Rates Matrix</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Rates relative to 1 {{ $baseCurrency->code ?? 'BDT' }} base currency.</p>
                </div>

                <span class="text-xs text-slate-400 font-mono">
                    {{ $currencies->count() }} currencies tracked
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 font-semibold">Currency</th>
                            <th class="pb-3 font-semibold">Exchange Rate</th>
                            <th class="pb-3 font-semibold">Inverted Rate</th>
                            <th class="pb-3 font-semibold">Role</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Source</th>
                            <th class="pb-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($currencies as $c)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-mono font-bold text-xs border border-slate-200">
                                            {{ $c->symbol }}
                                        </span>
                                        <div>
                                            <span class="font-bold text-slate-900 block">{{ $c->code }}</span>
                                            <span class="text-[11px] text-slate-400 block">{{ $c->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 font-mono text-slate-900 font-bold">
                                    @if($c->is_base)
                                        <span class="text-emerald-700">1.000000 (Base)</span>
                                    @else
                                        1 {{ $c->code }} = {{ number_format((float) $c->exchange_rate, 4) }} {{ $baseCurrency->code ?? 'BDT' }}
                                    @endif
                                </td>
                                <td class="py-3.5 font-mono text-slate-500 text-[11px]">
                                    @if($c->is_base)
                                        -
                                    @elseif((float) $c->exchange_rate > 0)
                                        1 {{ $baseCurrency->code ?? 'BDT' }} = {{ number_format(1 / (float) $c->exchange_rate, 6) }} {{ $c->code }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5">
                                    @if($c->is_base)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            ★ Base Currency
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">Secondary</span>
                                    @endif
                                </td>
                                <td class="py-3.5">
                                    @if($c->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200/60">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-slate-500 text-[11px]">
                                    <span class="capitalize">{{ $c->source }}</span>
                                    @if($c->last_updated_at)
                                        <span class="text-slate-400 block text-[10px]">
                                            {{ $c->last_updated_at->format('M d, H:i') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$c->is_base)
                                            <form method="POST" action="{{ route('currencies.set-base', $c) }}" class="inline" onsubmit="return confirm('Set {{ $c->code }} as the primary base currency? All other currency rates will be re-indexed.')">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 border border-slate-200 transition"
                                                    title="Set as base currency">
                                                    Set as Base
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button"
                                            onclick="openEditRateModal('{{ $c->id }}', '{{ $c->code }}', '{{ addslashes($c->name) }}', '{{ $c->symbol }}', '{{ (float) $c->exchange_rate }}', {{ $c->is_active ? 'true' : 'false' }}, {{ $c->is_base ? 'true' : 'false' }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                                            title="Edit Rate & Settings">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Edit Currency Modal --}}
    <div id="editCurrencyModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        $
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Edit Currency Rate</h3>
                        <p id="modalCurrencyCodeTitle" class="text-xs text-slate-400 font-medium">USD</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditCurrencyModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <form id="editCurrencyForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Currency Name *</label>
                    <input type="text" name="name" id="modalName" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-xs font-medium focus:outline-none focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Symbol *</label>
                        <input type="text" name="symbol" id="modalSymbol" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-xs font-mono font-bold focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Rate in {{ $baseCurrency->code ?? 'BDT' }} *</label>
                        <input type="number" step="0.000001" min="0.000001" name="exchange_rate" id="modalRate" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-xs font-mono font-bold focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="modalIsActive" value="1"
                            class="w-4 h-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs text-slate-700 font-medium">Active (Available for transactions & accounts)</span>
                    </label>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeEditCurrencyModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Currency Modal --}}
    <div id="addCurrencyModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        +
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Add World Currency</h3>
                        <p class="text-xs text-slate-400 font-medium">Track a new fiat currency in your ledger</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddCurrencyModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('currencies.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Currency Code *</label>
                        <input type="text" name="code" required maxlength="3" placeholder="e.g. CHF"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-xs font-mono font-bold uppercase focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Symbol *</label>
                        <input type="text" name="symbol" required maxlength="10" placeholder="e.g. CHF"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-xs font-mono font-bold focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Currency Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Swiss Franc"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-xs font-medium focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Initial Rate in {{ $baseCurrency->code ?? 'BDT' }} *</label>
                    <input type="number" step="0.000001" min="0.000001" name="exchange_rate" required placeholder="138.50"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-xs font-mono font-bold focus:outline-none focus:border-emerald-500">
                    <p class="text-[11px] text-slate-400 mt-1">Can also be auto-updated with live market sync.</p>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAddCurrencyModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition">
                        Add Currency
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Interactive live converter AJAX calculation
        const converterAmount = document.getElementById('converterAmount');
        const converterFrom = document.getElementById('converterFrom');
        const converterTo = document.getElementById('converterTo');
        const converterSwap = document.getElementById('converterSwap');
        const convertedResult = document.getElementById('convertedResult');
        const unitRatePill = document.getElementById('unitRatePill');

        let debounceTimer;

        function runLiveConversion() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const amount = converterAmount.value || 1;
                const from = converterFrom.value;
                const to = converterTo.value;

                fetch(`{{ route('currencies.convert') }}?amount=${amount}&from=${from}&to=${to}`)
                    .then(res => res.json())
                    .then(data => {
                        convertedResult.textContent = data.formatted_result;
                        unitRatePill.textContent = `1 ${data.from} = ${data.unit_rate.toFixed(4)} ${data.to}`;
                    })
                    .catch(err => console.error(err));
            }, 150);
        }

        converterAmount.addEventListener('input', runLiveConversion);
        converterFrom.addEventListener('change', runLiveConversion);
        converterTo.addEventListener('change', runLiveConversion);

        converterSwap.addEventListener('click', () => {
            const temp = converterFrom.value;
            converterFrom.value = converterTo.value;
            converterTo.value = temp;
            runLiveConversion();
        });

        // Edit Modal controls
        function openEditRateModal(id, code, name, symbol, rate, isActive, isBase) {
            const form = document.getElementById('editCurrencyForm');
            form.action = `{{ url('currencies') }}/${id}`;
            document.getElementById('modalCurrencyCodeTitle').textContent = code;
            document.getElementById('modalName').value = name;
            document.getElementById('modalSymbol').value = symbol;
            document.getElementById('modalRate').value = rate;
            document.getElementById('modalRate').disabled = isBase;
            document.getElementById('modalIsActive').checked = isActive;
            document.getElementById('modalIsActive').disabled = isBase;
            document.getElementById('editCurrencyModal').classList.remove('hidden');
        }

        function closeEditCurrencyModal() {
            document.getElementById('editCurrencyModal').classList.add('hidden');
        }

        function openAddCurrencyModal() {
            document.getElementById('addCurrencyModal').classList.remove('hidden');
        }

        function closeAddCurrencyModal() {
            document.getElementById('addCurrencyModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
