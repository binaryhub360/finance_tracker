<x-layouts.app :title="'Edit: ' . $recurring->name">
    @section('page-title', 'Edit Recurring Item')

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('recurring.update', $recurring) }}" id="recurring-form" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
                {{-- Card Header --}}
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Edit Recurring Schedule</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Modify subscription limits, renewal dates, and payment settings.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $recurring->status_badge_classes }}">
                        {{ ucfirst($recurring->status) }}
                    </span>
                </div>

                {{-- Type Toggle: Expense vs Income --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Transaction Type <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center justify-center p-3 rounded-2xl border cursor-pointer transition-all duration-150 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/60 has-[:checked]:text-rose-700 border-slate-200 hover:bg-slate-50 text-slate-700">
                            <input type="radio" name="type" value="expense" class="sr-only" {{ old('type', $recurring->type) === 'expense' ? 'checked' : '' }} onchange="toggleCategorySelect(this.value)">
                            <div class="flex items-center gap-2 text-xs font-bold">
                                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25l-3-3m0 0l-3 3m3-3v7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Expense (Subscription / Bill)</span>
                            </div>
                        </label>

                        <label class="relative flex items-center justify-center p-3 rounded-2xl border cursor-pointer transition-all duration-150 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-700 border-slate-200 hover:bg-slate-50 text-slate-700">
                            <input type="radio" name="type" value="income" class="sr-only" {{ old('type', $recurring->type) === 'income' ? 'checked' : '' }} onchange="toggleCategorySelect(this.value)">
                            <div class="flex items-center gap-2 text-xs font-bold">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Income (Retainer / Salary)</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Name & Amount --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Subscription / Item Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $recurring->name) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-semibold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                            placeholder="e.g. AWS Cloud Hosting, Netflix, Office Rent">
                        @error('name') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Amount per Cycle <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400 font-mono">{{ $recurring->currency }}</span>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', (float)$recurring->amount) }}" step="0.01" min="0.01" required
                                class="block w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 font-mono font-bold text-base focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                placeholder="0.00" oninput="calculateMonthlyPreview()">
                        </div>
                        @error('amount') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Frequency & Interval --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="frequency" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Frequency <span class="text-rose-500">*</span>
                        </label>
                        <select name="frequency" id="frequency" required onchange="calculateMonthlyPreview()"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            <option value="daily" {{ old('frequency', $recurring->frequency) === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('frequency', $recurring->frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('frequency', $recurring->frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('frequency', $recurring->frequency) === 'quarterly' ? 'selected' : '' }}>Quarterly (3 Months)</option>
                            <option value="yearly" {{ old('frequency', $recurring->frequency) === 'yearly' ? 'selected' : '' }}>Yearly (Annual)</option>
                        </select>
                        @error('frequency') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="interval" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Every X (Interval) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="interval" id="interval" value="{{ old('interval', $recurring->interval) }}" min="1" max="365" required oninput="calculateMonthlyPreview()"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-mono font-medium focus:outline-none focus:border-emerald-500">
                        @error('interval') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Currency
                        </label>
                        <input type="text" name="currency" id="currency" value="{{ old('currency', $recurring->currency) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-mono font-semibold focus:outline-none focus:border-emerald-500">
                        @error('currency') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Dynamic Monthly Run-rate preview box --}}
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-slate-50 to-emerald-50/30 border border-slate-200/70 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Estimated Monthly Run-Rate:</span>
                    <span class="font-mono font-bold text-sm text-slate-900" id="preview-monthly">{{ $recurring->currency }} 0.00 / month</span>
                </div>

                {{-- Account and Dynamic Category --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Account --}}
                    <div>
                        <label for="account_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Payment / Deposit Account <span class="text-rose-500">*</span>
                        </label>
                        <select name="account_id" id="account_id" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            <option value="">Select Account...</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('account_id', $recurring->account_id) == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} ({{ $acc->type_label }} - Balance: {{ format_currency($acc->balance) }})
                                </option>
                            @endforeach
                        </select>
                        @error('account_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Expense Category Select --}}
                    <div id="expense-category-container">
                        <label for="expense_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Expense Category <span class="text-rose-500">*</span>
                        </label>
                        <select name="expense_category_id" id="expense_category_id"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            <option value="">Select Category...</option>
                            @foreach($expenseCategories as $c)
                                <option value="{{ $c->id }}" {{ old('expense_category_id', $recurring->expense_category_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('expense_category_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Income Category Select --}}
                    <div id="income-category-container" class="hidden">
                        <label for="income_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Income Category <span class="text-rose-500">*</span>
                        </label>
                        <select name="income_category_id" id="income_category_id"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            <option value="">Select Category...</option>
                            @foreach($incomeCategories as $c)
                                <option value="{{ $c->id }}" {{ old('income_category_id', $recurring->income_category_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('income_category_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Dates: Start Date, Next Due Date, End Date --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Start Date <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $recurring->start_date->format('Y-m-d')) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                        @error('start_date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="next_due_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Next Due Date <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="next_due_date" id="next_due_date" value="{{ old('next_due_date', $recurring->next_due_date->format('Y-m-d')) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                        @error('next_due_date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            End Date (Optional)
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $recurring->end_date ? $recurring->end_date->format('Y-m-d') : '') }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500"
                            placeholder="Never expires if empty">
                        @error('end_date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Payment Method & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="payment_method" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Payment Method
                        </label>
                        <select name="payment_method" id="payment_method"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                            <option value="">Select Method (Optional)...</option>
                            <option value="Credit Card" {{ old('payment_method', $recurring->payment_method) === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="Debit Card" {{ old('payment_method', $recurring->payment_method) === 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                            <option value="Bank Transfer" {{ old('payment_method', $recurring->payment_method) === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer / ACH</option>
                            <option value="bKash" {{ old('payment_method', $recurring->payment_method) === 'bKash' ? 'selected' : '' }}>bKash</option>
                            <option value="Nagad" {{ old('payment_method', $recurring->payment_method) === 'Nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="Direct Debit" {{ old('payment_method', $recurring->payment_method) === 'Direct Debit' ? 'selected' : '' }}>Direct Debit / Auto-Pay</option>
                            <option value="Cash" {{ old('payment_method', $recurring->payment_method) === 'Cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                        @error('payment_method') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                            <option value="active" {{ old('status', $recurring->status) === 'active' ? 'selected' : '' }}>Active (Monitoring & Logging)</option>
                            <option value="paused" {{ old('status', $recurring->status) === 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="cancelled" {{ old('status', $recurring->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Auto-log Toggle --}}
                <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 flex items-start gap-3">
                    <input type="checkbox" name="auto_log" id="auto_log" value="1" {{ old('auto_log', $recurring->auto_log) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500 cursor-pointer mt-0.5">
                    <div>
                        <label for="auto_log" class="text-xs font-bold text-slate-800 cursor-pointer block">
                            Auto-Record to Financial Ledger
                        </label>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            When enabled, the system automatically posts the transaction to your selected account ledger on the due date without requiring manual clicks.
                        </p>
                    </div>
                </div>

                {{-- Notes / Contract Details --}}
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Notes / Cancellation Link / Customer ID
                    </label>
                    <textarea name="notes" id="notes" rows="2"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="e.g. Account #9821, renewal terms, cancel at https://vendor.com/billing">{{ old('notes', $recurring->notes) }}</textarea>
                    @error('notes') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Update Recurring Schedule
                    </button>
                    <a href="{{ route('recurring.show', $recurring) }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function toggleCategorySelect(type) {
            const expContainer = document.getElementById('expense-category-container');
            const incContainer = document.getElementById('income-category-container');
            const expSelect = document.getElementById('expense_category_id');
            const incSelect = document.getElementById('income_category_id');

            if (type === 'income') {
                expContainer.classList.add('hidden');
                incContainer.classList.remove('hidden');
                expSelect.required = false;
                incSelect.required = true;
            } else {
                expContainer.classList.remove('hidden');
                incContainer.classList.add('hidden');
                expSelect.required = true;
                incSelect.required = false;
            }
            calculateMonthlyPreview();
        }

        function calculateMonthlyPreview() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const freq = document.getElementById('frequency').value;
            const interval = parseInt(document.getElementById('interval').value) || 1;
            const currency = document.getElementById('currency').value || '{{ $recurring->currency }}';

            let monthly = 0;
            if (freq === 'daily') {
                monthly = amount * (30 / interval);
            } else if (freq === 'weekly') {
                monthly = (amount * 52 / 12) / interval;
            } else if (freq === 'monthly') {
                monthly = amount / interval;
            } else if (freq === 'quarterly') {
                monthly = (amount / 3) / interval;
            } else if (freq === 'yearly') {
                monthly = (amount / 12) / interval;
            }

            document.getElementById('preview-monthly').textContent = currency + ' ' + monthly.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / month';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const checkedRadio = document.querySelector('input[name="type"]:checked');
            if (checkedRadio) {
                toggleCategorySelect(checkedRadio.value);
            }
            calculateMonthlyPreview();
        });
    </script>
    @endpush
</x-layouts.app>
