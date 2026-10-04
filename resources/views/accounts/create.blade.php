<x-layouts.app :title="'Add Account'">
    @section('page-title', 'Add Account')

    <div class="max-w-2xl">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
            <div class="mb-6">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Create Financial Account</h2>
                <p class="text-xs text-slate-500 mt-0.5">Add a new bank, mobile wallet, or cash register to track balances.</p>
            </div>

            <form method="POST" action="{{ route('accounts.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Account Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="e.g. City Bank Primary / bKash Business">
                    @error('name') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Account Type <span class="text-rose-500">*</span></label>
                    <select name="type" id="type" required class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash in Hand</option>
                        <option value="bank" {{ old('type', 'bank') == 'bank' ? 'selected' : '' }}>Bank Account</option>
                        <option value="mobile_wallet" {{ old('type') == 'mobile_wallet' ? 'selected' : '' }}>Mobile Wallet (bKash / Nagad / etc.)</option>
                        <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other Asset</option>
                    </select>
                    @error('type') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="opening_balance" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Opening Balance <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                                {{ get_setting('currency_symbol', '$') }}
                            </span>
                            <input type="number" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', '0') }}" step="0.01" min="0" required
                                class="block w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-bold tracking-tight focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono"
                                placeholder="0.00">
                        </div>
                        @error('opening_balance') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="opening_balance_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Opening Balance Date</label>
                        <input type="date" name="opening_balance_date" id="opening_balance_date" value="{{ old('opening_balance_date', date('Y-m-d')) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('opening_balance_date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="Optional details, account numbers, or branches">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                        class="h-4 w-4 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer select-none">
                        Active Account <span class="block text-[11px] font-normal text-slate-400">Enable this account for day-to-day transactions</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Create Account
                    </button>
                    <a href="{{ route('accounts.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
