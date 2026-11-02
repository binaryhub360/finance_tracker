<x-layouts.app :title="'Create Savings Vault'">
    @section('page-title', 'Create Savings Vault')

    @php
        $preset = request('preset');
        $defaultName = match($preset) {
            'emergency' => 'Emergency Fund (6 Months)',
            'tax' => 'Annual Tax & VAT Reserve',
            'tech' => 'MacBook & Workspace Upgrade',
            'vacation' => 'Holiday & Travel Vault',
            default => old('name', ''),
        };
        $defaultColor = match($preset) {
            'emergency' => 'emerald',
            'tax' => 'indigo',
            'tech' => 'sky',
            'vacation' => 'amber',
            default => old('color', 'emerald'),
        };
        $defaultDesc = match($preset) {
            'emergency' => 'Liquid reserve for unexpected life, medical, or business emergencies.',
            'tax' => 'Dedicated set-aside for quarterly and year-end tax compliance.',
            'tech' => 'Equipment refresh budget for high-performance productivity tools.',
            'vacation' => 'Dedicated bucket for relaxing holidays, flights, and accommodations.',
            default => old('description', ''),
        };
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Back Navigation & Header --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('goals.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to Savings Vaults
            </a>
        </div>

        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Create a New Savings Vault</h2>
                <p class="text-xs text-slate-500 mt-1">Set a clear milestone, ring-fence money safely, and track your pace towards completion.</p>
            </div>

            <form method="POST" action="{{ route('goals.store') }}" class="space-y-6">
                @csrf

                {{-- Basic Vault Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Name --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Vault Name *</label>
                        <input type="text" name="name" id="vaultNameInput" required value="{{ $defaultName }}"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-semibold focus:outline-none focus:border-emerald-500 transition"
                            placeholder="e.g. 6-Month Emergency Cushion, Q4 Tax Reserve, New Electric Car">
                        @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Target Amount --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Target Goal Amount ({{ $currency }}) *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono font-bold text-sm">{{ $currency }}</span>
                            <input type="number" step="0.01" min="1" name="target_amount" id="targetAmountInput" required value="{{ old('target_amount') }}"
                                class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-emerald-500"
                                placeholder="50000.00">
                        </div>
                        @error('target_amount') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Initial Seed Deposit --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Initial Seed Deposit (Optional)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono font-bold text-sm">{{ $currency }}</span>
                            <input type="number" step="0.01" min="0" name="start_amount" value="{{ old('start_amount', '0.00') }}"
                                class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-emerald-500"
                                placeholder="0.00">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Starting balance already allocated to this goal.</p>
                        @error('start_amount') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Target Completion Date --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Target Completion Date (Optional)</label>
                        <input type="date" name="target_date" value="{{ old('target_date') }}"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                        <p class="text-[11px] text-slate-400 mt-1">Helps calculate recommended monthly saving pace.</p>
                        @error('target_date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Funding Account --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Designated Funding Account (Optional)</label>
                        <select name="account_id"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                            <option value="">Virtual Allocation (No designated physical account)</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} (Balance: {{ format_currency($acc->balance) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Where funds for this vault are primarily held.</p>
                        @error('account_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Color Theme Selector --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-semibold text-slate-700">Vault Accent Theme *</label>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                        @foreach([
                            'emerald' => ['Emerald Mint', 'bg-emerald-500 text-emerald-700 border-emerald-300'],
                            'indigo' => ['Royal Indigo', 'bg-indigo-500 text-indigo-700 border-indigo-300'],
                            'sky' => ['Cyan Sky', 'bg-sky-500 text-sky-700 border-sky-300'],
                            'amber' => ['Golden Amber', 'bg-amber-500 text-amber-700 border-amber-300'],
                            'purple' => ['Vivid Purple', 'bg-purple-500 text-purple-700 border-purple-300'],
                            'rose' => ['Ruby Rose', 'bg-rose-500 text-rose-700 border-rose-300'],
                        ] as $colKey => [$colLabel, $colClasses])
                            <label class="relative flex flex-col items-center gap-1.5 p-3 rounded-2xl border border-slate-200 hover:border-slate-300 cursor-pointer transition text-center group has-[:checked]:border-slate-900 has-[:checked]:bg-slate-50">
                                <input type="radio" name="color" value="{{ $colKey }}" {{ $defaultColor === $colKey ? 'checked' : '' }} class="sr-only">
                                <span class="w-6 h-6 rounded-full {{ explode(' ', $colClasses)[0] }} shadow-sm group-hover:scale-110 transition"></span>
                                <span class="text-[11px] font-semibold text-slate-700">{{ $colLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('color') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Description & Notes --}}
                <div class="space-y-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Description & Purpose (Optional)</label>
                        <textarea name="description" rows="2"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs focus:outline-none focus:border-emerald-500"
                            placeholder="Why are you saving for this goal? Any milestones or motivations?">{{ $defaultDesc }}</textarea>
                    </div>

                    {{-- Advanced Vault Security & Ledger Controls --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80">
                        {{-- Vault Lock Feature --}}
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="is_locked" id="isLocked" value="1" {{ old('is_locked') ? 'checked' : '' }}
                                class="w-4 h-4 mt-0.5 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <label for="isLocked" class="text-xs font-bold text-slate-800 cursor-pointer flex items-center gap-1">
                                    🔒 Enable Vault Lock
                                </label>
                                <p class="text-[11px] text-slate-500 mt-0.5">Prevents impulsive withdrawals until the target goal is completed.</p>
                            </div>
                        </div>

                        {{-- Deduct Initial Seed Portion --}}
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="deduct_from_account" id="deductAcc" value="1" checked
                                class="w-4 h-4 mt-0.5 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <label for="deductAcc" class="text-xs font-bold text-slate-800 cursor-pointer">
                                    Record Seed Deposit as Expense/Transfer
                                </label>
                                <p class="text-[11px] text-slate-500 mt-0.5">If seed amount > 0, deducts from the selected funding account balance.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('goals.index') }}"
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg transition">
                        Create Vault
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
