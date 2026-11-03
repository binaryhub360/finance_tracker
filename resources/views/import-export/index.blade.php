<x-layouts.app :title="'Data Import & Export Hub'">
    @section('page-title', 'Import & Export Hub')

    <div class="space-y-8" x-data="{ activeTab: 'import' }">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-500/15 via-blue-500/10 to-transparent text-indigo-600 border border-indigo-500/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Data Import & Export Hub</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Bulk CSV & bank statement importer, multi-dataset export, and rollback auditing</p>
                </div>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex items-center bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-1 shadow-xs">
                <button type="button" @click="activeTab = 'import'"
                    :class="activeTab === 'import' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <span>Import Statement / CSV</span>
                </button>
                <button type="button" @click="activeTab = 'export'"
                    :class="activeTab === 'export' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export Center</span>
                </button>
                <button type="button" @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Import History</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-800 font-bold font-mono">
                        {{ $totalBatches }}
                    </span>
                </button>
            </div>
        </div>

        {{-- TAB 1: SMART CSV & BANK STATEMENT IMPORTER --}}
        <div x-show="activeTab === 'import'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Upload Form (2 cols) --}}
                <div class="lg:col-span-2 bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Upload Statement or CSV</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Upload bank exports, credit card statements, or generic spreadsheets. We'll map columns in the next step.</p>
                    </div>

                    <form method="POST" action="{{ route('import-export.preview') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Drag & Drop Upload Zone --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">CSV or Text File <span class="text-rose-500">*</span></label>
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-indigo-500 rounded-3xl p-8 text-center transition-all bg-slate-50/50 hover:bg-indigo-500/[0.02] cursor-pointer group"
                                onclick="document.getElementById('csv_file').click()">
                                <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required class="hidden"
                                    onchange="handleFileSelect(this)">
                                
                                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                                <div class="font-bold text-slate-800 text-sm" id="file-label">Click to select or drag and drop statement file</div>
                                <p class="text-xs text-slate-400 mt-1" id="file-sub">Supported formats: .CSV, .TXT (max 10MB)</p>
                            </div>
                            @error('csv_file') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Target Account & Defaults --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="account_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Target Account <span class="text-rose-500">*</span></label>
                                <select name="account_id" id="account_id" required
                                    class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }} &bull; {{ $acc->currency ?? 'BDT' }} ({{ format_currency($acc->balance, $acc->currency) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="default_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Transaction Type Detection <span class="text-rose-500">*</span></label>
                                <select name="default_type" id="default_type" required
                                    class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                    <option value="auto">Auto-detect from +/- signs or Debit/Credit</option>
                                    <option value="expense">Treat all entries as Expenses</option>
                                    <option value="income">Treat all entries as Income</option>
                                </select>
                            </div>
                        </div>

                        {{-- Duplicate Prevention Checkbox --}}
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-indigo-500/[0.04] border border-indigo-500/15">
                            <input type="checkbox" name="skip_duplicates" id="skip_duplicates" value="1" checked
                                class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500/20 mt-0.5 cursor-pointer">
                            <label for="skip_duplicates" class="text-xs font-bold text-slate-800 cursor-pointer select-none">
                                Smart Duplicate Prevention
                                <span class="block text-[11px] font-normal text-slate-500 mt-0.5">Automatically skip records that match an existing transaction with identical date, amount, and reference/description.</span>
                            </label>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 shadow-md shadow-indigo-600/20 hover:shadow-lg transition-all duration-200 active:scale-[0.98]">
                                Continue to Column Mapping &rarr;
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Sidebar: Ready-Made Templates & Instructions --}}
                <div class="space-y-6">
                    {{-- Templates Card --}}
                    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-xl shadow-slate-900/5">
                        <div class="flex items-center gap-2.5 mb-3">
                            <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200/60">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </span>
                            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Sample Templates</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Download starter templates formatted with standard headers to speed up your imports.</p>

                        <div class="space-y-2.5">
                            <a href="{{ route('import-export.template', 'standard') }}"
                                class="flex items-center justify-between p-3 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition group">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition">Multi-Type Standard CSV</div>
                                    <div class="text-[10px] text-slate-400">Date, Type, Category, Amount, Description</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>

                            <a href="{{ route('import-export.template', 'bank_statement') }}"
                                class="flex items-center justify-between p-3 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition group">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition">Bank Statement CSV</div>
                                    <div class="text-[10px] text-slate-400">Date, Description, Debit, Credit, Ref</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Tips Card --}}
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-xl shadow-slate-900/10 text-xs">
                        <div class="font-bold text-amber-300 text-sm mb-2 flex items-center gap-1.5">
                            <span>💡 Import Safety Guarantee</span>
                        </div>
                        <p class="text-slate-300 leading-relaxed mb-3">
                            Every import is tracked in an isolated batch. If you ever import the wrong file or pick the wrong account, you can <strong>roll back the entire batch with 1 click</strong> from the Import History tab.
                        </p>
                        <div class="pt-3 border-t border-white/10 flex justify-between items-center text-[11px] text-slate-400">
                            <span>Total Imported:</span>
                            <span class="font-bold text-white font-mono">{{ number_format($totalImported) }} txns</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: EXPORT CENTER --}}
        <div x-show="activeTab === 'export'" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
                <div class="mb-6">
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Export Center</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Download comprehensive spreadsheets or a complete JSON snapshot of your data.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    {{-- 1. All Transactions --}}
                    <div class="p-5 rounded-3xl bg-slate-50/70 border border-slate-200/80 hover:border-emerald-300 transition-all group flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="p-2.5 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100/80 text-emerald-800">CSV</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Full Transaction Ledger</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">Complete history including native currencies, exchange rates, and tax deduction schedules.</p>
                        </div>
                        <a href="{{ route('import-export.export', 'transactions') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Export Transactions
                        </a>
                    </div>

                    {{-- 2. Accounts & Balances --}}
                    <div class="p-5 rounded-3xl bg-slate-50/70 border border-slate-200/80 hover:border-blue-300 transition-all group flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="p-2.5 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100/80 text-blue-800">CSV</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Accounts & Net Worth</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">Account balances in native currencies with real-time base currency valuations.</p>
                        </div>
                        <a href="{{ route('import-export.export', 'accounts') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Export Accounts
                        </a>
                    </div>

                    {{-- 3. Invoices & Billing --}}
                    <div class="p-5 rounded-3xl bg-slate-50/70 border border-slate-200/80 hover:border-amber-300 transition-all group flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="p-2.5 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/60">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-100/80 text-amber-800">CSV</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Invoices & Receivables</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">Client billing ledger, payment records, issue & due dates, and outstanding statuses.</p>
                        </div>
                        <a href="{{ route('import-export.export', 'invoices') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                            <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Export Invoices
                        </a>
                    </div>

                    {{-- 4. Budgets & Spending --}}
                    <div class="p-5 rounded-3xl bg-slate-50/70 border border-slate-200/80 hover:border-purple-300 transition-all group flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="p-2.5 rounded-2xl bg-purple-50 text-purple-600 border border-purple-200/60">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-purple-100/80 text-purple-800">CSV</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Budgets & Limits</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">Monthly spending targets, recorded consumption, and remaining headroom.</p>
                        </div>
                        <a href="{{ route('import-export.export', 'budgets') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                            <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Export Budgets
                        </a>
                    </div>

                    {{-- 5. Savings Vaults & Goals --}}
                    <div class="p-5 rounded-3xl bg-slate-50/70 border border-slate-200/80 hover:border-teal-300 transition-all group flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="p-2.5 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200/60">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-teal-100/80 text-teal-800">CSV</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Savings Vaults & Goals</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">Financial goals, allocated savings pools, locked amounts, and target dates.</p>
                        </div>
                        <a href="{{ route('import-export.export', 'goals') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                            <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Export Goals
                        </a>
                    </div>

                    {{-- 6. Full JSON Backup --}}
                    <div class="p-5 rounded-3xl bg-slate-900 text-white border border-slate-800 hover:border-indigo-500 transition-all group flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="p-2.5 rounded-2xl bg-white/10 text-indigo-400 border border-white/15">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">JSON Archive</span>
                            </div>
                            <h3 class="text-sm font-bold text-white mb-1">Complete System Backup</h3>
                            <p class="text-xs text-slate-400 leading-relaxed mb-4">Full archive of all accounts, transactions, invoices, and targets for disaster recovery.</p>
                        </div>
                        <a href="{{ route('import-export.export', 'backup_json') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Download Full Backup
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: IMPORT HISTORY & AUDIT LOG --}}
        <div x-show="activeTab === 'history'" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Import Batches Audit Log</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Track and rollback statement imports whenever necessary</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400">{{ $batches->total() }} Total Batches</span>
                </div>

                @if($batches->isEmpty())
                    <div class="py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-semibold text-slate-600">No import batches recorded yet.</p>
                        <p class="text-xs text-slate-400 mt-1">Uploaded statements will be listed here with batch rollback capabilities.</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 sm:-mx-8">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 sm:px-8 py-3">Import Date</th>
                                    <th class="px-4 py-3">File Name</th>
                                    <th class="px-4 py-3">Target Account</th>
                                    <th class="px-4 py-3 text-center">Total Rows</th>
                                    <th class="px-4 py-3 text-center">Imported</th>
                                    <th class="px-4 py-3 text-center">Duplicates Skipped</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-6 sm:px-8 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach($batches as $b)
                                    @php $badge = $b->status_badge; @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-6 sm:px-8 py-3.5 whitespace-nowrap text-slate-600 font-medium font-mono">
                                            {{ $b->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap font-bold text-slate-900">
                                            {{ $b->filename }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-slate-600 font-medium">
                                            {{ $b->account?->name ?? 'Unspecified' }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-700">
                                            {{ $b->total_rows }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-extrabold text-emerald-600">
                                            {{ $b->imported_count }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-bold text-amber-600">
                                            {{ $b->skipped_count }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $badge['class'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 sm:px-8 py-3.5 whitespace-nowrap text-right">
                                            @if($b->status === 'completed' && $b->imported_count > 0)
                                                <form method="POST" action="{{ route('import-export.rollback', $b) }}" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to rollback this import? This will delete all {{ $b->imported_count }} transactions created in this batch.')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 transition active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                                        </svg>
                                                        Rollback Batch
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-slate-400 text-xs italic">No actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        {{ $batches->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('file-label').textContent = file.name;
                document.getElementById('file-sub').textContent = (file.size / 1024).toFixed(1) + ' KB — Ready to upload';
            }
        }
    </script>
    @endpush
</x-layouts.app>
