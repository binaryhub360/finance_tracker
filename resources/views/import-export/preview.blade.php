<x-layouts.app :title="'Map CSV Columns — Import & Export Hub'">
    @section('page-title', 'Map CSV Columns')

    <div class="max-w-5xl mx-auto space-y-8" x-data="{ amountMode: '{{ isset($guessedMappings['debit']) && isset($guessedMappings['credit']) ? 'separate' : 'single' }}' }">
        {{-- Header Summary Bar --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-200/60">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Step 2: Map Columns to Transaction Fields</h1>
                        <p class="text-xs text-slate-500 mt-0.5">We matched probable columns below. Review and adjust if necessary.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <div class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200">
                        <span class="text-slate-400">File:</span>
                        <strong class="text-slate-800 font-mono">{{ $options['filename'] }}</strong>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800">
                        <span>Account:</span>
                        <strong>{{ $account->name }} ({{ $account->currency ?? 'BDT' }})</strong>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200">
                        <strong class="font-mono text-slate-900">{{ number_format($totalRows) }}</strong> rows detected
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('import-export.process') }}" class="mt-6 space-y-6">
                @csrf
                <input type="hidden" name="import_token" value="{{ $options['import_token'] }}">
                <input type="hidden" name="account_id" value="{{ $account->id }}">
                <input type="hidden" name="filename" value="{{ $options['filename'] }}">
                <input type="hidden" name="default_type" value="{{ $options['default_type'] }}">
                <input type="hidden" name="skip_duplicates" value="{{ $options['skip_duplicates'] ? '1' : '0' }}">

                {{-- Column Mappings Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Date Column --}}
                    <div>
                        <label for="map_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Transaction Date <span class="text-rose-500">*</span>
                        </label>
                        <select name="mappings[date]" id="map_date" required
                            class="block w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Select Date Column</option>
                            @foreach($headers as $idx => $header)
                                <option value="{{ $idx }}" {{ $guessedMappings['date'] === $idx ? 'selected' : '' }}>
                                    Column {{ $idx + 1 }}: {{ $header }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Format Hint --}}
                    <div>
                        <label for="date_format" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Date Format</label>
                        <select name="date_format" id="date_format"
                            class="block w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            <option value="auto">Auto-Detect Format (Recommended)</option>
                            <option value="Y-m-d">YYYY-MM-DD (e.g. 2026-11-01)</option>
                            <option value="d/m/Y">DD/MM/YYYY (e.g. 01/11/2026)</option>
                            <option value="m/d/Y">MM/DD/YYYY (e.g. 11/01/2026)</option>
                            <option value="d-m-Y">DD-MM-YYYY (e.g. 01-11-2026)</option>
                            <option value="d.m.Y">DD.MM.YYYY (e.g. 01.11.2026)</option>
                        </select>
                    </div>

                    {{-- Description / Narrative --}}
                    <div>
                        <label for="map_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Description / Payee <span class="text-rose-500">*</span></label>
                        <select name="mappings[description]" id="map_description" required
                            class="block w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Select Description Column</option>
                            @foreach($headers as $idx => $header)
                                <option value="{{ $idx }}" {{ $guessedMappings['description'] === $idx ? 'selected' : '' }}>
                                    Column {{ $idx + 1 }}: {{ $header }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Amount Mode Selector --}}
                    <div class="md:col-span-2 lg:col-span-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Amount Column Configuration</label>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="radio" name="amt_mode" value="single" x-model="amountMode" class="text-indigo-600">
                                    Single Amount Column
                                </label>
                                <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer ml-3">
                                    <input type="radio" name="amt_mode" value="separate" x-model="amountMode" class="text-indigo-600">
                                    Separate Debit & Credit Columns (Bank Format)
                                </label>
                            </div>
                        </div>

                        {{-- Single Amount Column --}}
                        <div x-show="amountMode === 'single'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="map_amount" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Amount Column</label>
                                <select name="mappings[amount]" id="map_amount"
                                    class="block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-semibold focus:outline-none focus:border-indigo-500">
                                    <option value="">Select Amount Column</option>
                                    @foreach($headers as $idx => $header)
                                        <option value="{{ $idx }}" {{ $guessedMappings['amount'] === $idx ? 'selected' : '' }}>
                                            Column {{ $idx + 1 }}: {{ $header }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-xs text-slate-500 flex items-center">
                                <span>Positive values are treated as income; negative values (e.g. <code>-150</code>) as expenses.</span>
                            </div>
                        </div>

                        {{-- Separate Debit / Credit Columns --}}
                        <div x-show="amountMode === 'separate'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="map_debit" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Debit / Outflow Column (Expense)</label>
                                <select name="mappings[debit]" id="map_debit"
                                    class="block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-semibold focus:outline-none focus:border-indigo-500">
                                    <option value="">Select Debit Column</option>
                                    @foreach($headers as $idx => $header)
                                        <option value="{{ $idx }}" {{ $guessedMappings['debit'] === $idx ? 'selected' : '' }}>
                                            Column {{ $idx + 1 }}: {{ $header }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="map_credit" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Credit / Inflow Column (Income)</label>
                                <select name="mappings[credit]" id="map_credit"
                                    class="block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-semibold focus:outline-none focus:border-indigo-500">
                                    <option value="">Select Credit Column</option>
                                    @foreach($headers as $idx => $header)
                                        <option value="{{ $idx }}" {{ $guessedMappings['credit'] === $idx ? 'selected' : '' }}>
                                            Column {{ $idx + 1 }}: {{ $header }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Reference Number --}}
                    <div>
                        <label for="map_reference" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Reference / Cheque #</label>
                        <select name="mappings[reference]" id="map_reference"
                            class="block w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-indigo-500">
                            <option value="">(None / Generate Automatically)</option>
                            @foreach($headers as $idx => $header)
                                <option value="{{ $idx }}" {{ $guessedMappings['reference'] === $idx ? 'selected' : '' }}>
                                    Column {{ $idx + 1 }}: {{ $header }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category Column --}}
                    <div>
                        <label for="map_category" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Category Column</label>
                        <select name="mappings[category]" id="map_category"
                            class="block w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-indigo-500">
                            <option value="">(None / Use General Category)</option>
                            @foreach($headers as $idx => $header)
                                <option value="{{ $idx }}" {{ $guessedMappings['category'] === $idx ? 'selected' : '' }}>
                                    Column {{ $idx + 1 }}: {{ $header }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type Column --}}
                    <div>
                        <label for="map_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Type (Income/Expense)</label>
                        <select name="mappings[type]" id="map_type"
                            class="block w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-indigo-500">
                            <option value="">(Auto-detect)</option>
                            @foreach($headers as $idx => $header)
                                <option value="{{ $idx }}" {{ $guessedMappings['type'] === $idx ? 'selected' : '' }}>
                                    Column {{ $idx + 1 }}: {{ $header }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Live Sample Data Preview Table --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Sample Data Preview (First {{ count($previewRows) }} rows)</h3>
                        <span class="text-[11px] text-slate-400">Delimiter: <strong class="font-mono text-slate-700">"{{ $delimiter }}"</strong></span>
                    </div>

                    <div class="border border-slate-200 rounded-2xl overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-100/80 text-[11px] font-bold text-slate-600 uppercase border-b border-slate-200">
                                <tr>
                                    @foreach($headers as $idx => $h)
                                        <th class="px-3.5 py-2.5 whitespace-nowrap">
                                            <div class="text-[10px] text-indigo-600 font-mono">Col {{ $idx + 1 }}</div>
                                            <div>{{ $h }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                                @foreach($previewRows as $row)
                                    <tr class="hover:bg-slate-50/60">
                                        @foreach($headers as $idx => $h)
                                            <td class="px-3.5 py-2 whitespace-nowrap text-slate-700">
                                                {{ $row[$idx] ?? '' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('import-export.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 shadow-md shadow-indigo-600/20 hover:shadow-lg transition-all duration-200 active:scale-[0.98]">
                        Confirm & Import {{ number_format($totalRows) }} Transactions &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
