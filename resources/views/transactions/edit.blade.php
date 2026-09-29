<x-layouts.app :title="'Edit Transaction'">
    @section('page-title', 'Edit Transaction')

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Transaction Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type <span class="text-red-500">*</span></label>
                    <div class="flex gap-3">
                        @foreach(['income' => 'Income', 'expense' => 'Expense', 'transfer' => 'Transfer'] as $value => $label)
                            <label class="flex-1">
                                <input type="radio" name="type" value="{{ $value }}" {{ old('type', $transaction->type) == $value ? 'checked' : '' }}
                                    class="sr-only peer" onchange="toggleTransactionFields()">
                                <div class="cursor-pointer text-center py-2.5 px-4 rounded-lg border-2 text-sm font-medium transition
                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700
                                    border-gray-200 text-gray-500 hover:border-gray-300">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" id="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount', $transaction->amount) }}" step="0.01" min="0.01" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Income/Expense Account --}}
                <div id="field-account" class="hidden">
                    <label for="account_id" class="block text-sm font-medium text-gray-700 mb-1">Account <span class="text-red-500">*</span></label>
                    <select name="account_id" id="account_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }} ({{ $account->type_label }})</option>
                        @endforeach
                    </select>
                    @error('account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Income Category --}}
                <div id="field-income-category" class="hidden">
                    <label for="income_category_id" class="block text-sm font-medium text-gray-700 mb-1">Income Category <span class="text-red-500">*</span></label>
                    <select name="income_category_id" id="income_category_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Select Category</option>
                        @foreach($incomeCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('income_category_id', $transaction->income_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('income_category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Expense Category --}}
                <div id="field-expense-category" class="hidden">
                    <label for="expense_category_id" class="block text-sm font-medium text-gray-700 mb-1">Expense Category <span class="text-red-500">*</span></label>
                    <select name="expense_category_id" id="expense_category_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Select Category</option>
                        @foreach($expenseCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('expense_category_id', $transaction->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('expense_category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Transfer Accounts --}}
                <div id="field-transfer" class="hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="from_account_id" class="block text-sm font-medium text-gray-700 mb-1">From Account <span class="text-red-500">*</span></label>
                            <select name="from_account_id" id="from_account_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('from_account_id', $transaction->from_account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            @error('from_account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="to_account_id" class="block text-sm font-medium text-gray-700 mb-1">To Account <span class="text-red-500">*</span></label>
                            <select name="to_account_id" id="to_account_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('to_account_id', $transaction->to_account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            @error('to_account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description', $transaction->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference', $transaction->reference) }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('reference') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">Update Transaction</button>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
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
            account.classList.add('hidden'); incCat.classList.add('hidden'); expCat.classList.add('hidden'); transfer.classList.add('hidden');
            if (type === 'income') { account.classList.remove('hidden'); incCat.classList.remove('hidden'); }
            else if (type === 'expense') { account.classList.remove('hidden'); expCat.classList.remove('hidden'); }
            else if (type === 'transfer') { transfer.classList.remove('hidden'); }
        }
        document.addEventListener('DOMContentLoaded', toggleTransactionFields);
    </script>
    @endpush
</x-layouts.app>
