<x-layouts.app :title="'Edit Transaction'">
    @section('page-title', 'Edit Transaction')

    <div class="max-w-2xl">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Edit Transaction</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Update details for this financial record.</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $transaction->type_bg_color }} {{ $transaction->type_color }}">
                    {{ $transaction->type_label }}
                </span>
            </div>

            <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Transaction Type Segmented Toggle --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Transaction Type <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2.5 p-1 bg-slate-100/80 rounded-2xl border border-slate-200/60">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="income" {{ old('type', $transaction->type) == 'income' ? 'checked' : '' }}
                                class="sr-only peer" onchange="toggleTransactionFields()">
                            <div class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold transition-all duration-200 text-slate-600 peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm peer-checked:shadow-slate-900/10 peer-checked:ring-1 peer-checked:ring-emerald-500/20 hover:text-slate-900">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Income
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="expense" {{ old('type', $transaction->type) == 'expense' ? 'checked' : '' }}
                                class="sr-only peer" onchange="toggleTransactionFields()">
                            <div class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold transition-all duration-200 text-slate-600 peer-checked:bg-white peer-checked:text-rose-700 peer-checked:shadow-sm peer-checked:shadow-slate-900/10 peer-checked:ring-1 peer-checked:ring-rose-500/20 hover:text-slate-900">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Expense
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="transfer" {{ old('type', $transaction->type) == 'transfer' ? 'checked' : '' }}
                                class="sr-only peer" onchange="toggleTransactionFields()">
                            <div class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold transition-all duration-200 text-slate-600 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm peer-checked:shadow-slate-900/10 peer-checked:ring-1 peer-checked:ring-blue-500/20 hover:text-slate-900">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Transfer
                            </div>
                        </label>
                    </div>
                    @error('type') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Date & Amount --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" id="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="amount" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Amount <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                                {{ currency_symbol() }}
                            </span>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $transaction->amount) }}" step="0.01" min="0.01" required
                                class="block w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-bold tracking-tight focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono">
                        </div>
                        @error('amount') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Income/Expense Account --}}
                <div id="field-account" class="hidden">
                    <label for="account_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Account <span class="text-rose-500">*</span></label>
                    <select name="account_id" id="account_id" class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} &bull; {{ $account->type_label }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Income Category --}}
                <div id="field-income-category" class="hidden">
                    <label for="income_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Income Category <span class="text-rose-500">*</span></label>
                    <select name="income_category_id" id="income_category_id" class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        <option value="">Select Category</option>
                        @foreach($incomeCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('income_category_id', $transaction->income_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('income_category_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Expense Category --}}
                <div id="field-expense-category" class="hidden">
                    <label for="expense_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Expense Category <span class="text-rose-500">*</span></label>
                    <select name="expense_category_id" id="expense_category_id" class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        <option value="">Select Category</option>
                        @foreach($expenseCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('expense_category_id', $transaction->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('expense_category_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Transfer Accounts --}}
                <div id="field-transfer" class="hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="from_account_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">From Account <span class="text-rose-500">*</span></label>
                            <select name="from_account_id" id="from_account_id" class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                <option value="">Select Source Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('from_account_id', $transaction->from_account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            @error('from_account_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="to_account_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">To Account <span class="text-rose-500">*</span></label>
                            <select name="to_account_id" id="to_account_id" class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                <option value="">Select Destination Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('to_account_id', $transaction->to_account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            @error('to_account_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Description & Reference --}}
                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="e.g. Monthly cloud server subscription">{{ old('description', $transaction->description) }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="reference" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Reference Number</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference', $transaction->reference) }}"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="e.g. INV-2026-0042">
                    @error('reference') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Update Transaction
                    </button>
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleTransactionFields() {
            const type = document.querySelector('input[name="type"]:checked')?.value;
            const account = document.getElementById('field-account');
            const incCat = document.getElementById('field-income-category');
            const expCat = document.getElementById('field-expense-category');
            const transfer = document.getElementById('field-transfer');

            account.classList.add('hidden');
            incCat.classList.add('hidden');
            expCat.classList.add('hidden');
            transfer.classList.add('hidden');

            if (type === 'income') {
                account.classList.remove('hidden');
                incCat.classList.remove('hidden');
            } else if (type === 'expense') {
                account.classList.remove('hidden');
                expCat.classList.remove('hidden');
            } else if (type === 'transfer') {
                transfer.classList.remove('hidden');
            }
        }
        document.addEventListener('DOMContentLoaded', toggleTransactionFields);
    </script>
    @endpush
</x-layouts.app>
