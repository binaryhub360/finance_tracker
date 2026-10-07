<x-layouts.app :title="'Set Budget Target'">
    @section('page-title', 'Set Spending Budget')

    <div class="max-w-xl">
        <form method="POST" action="{{ route('budgets.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Budget Limit Details</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Define category spending ceiling and alert thresholds.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                    </span>
                </div>

                <input type="hidden" name="month" value="{{ $month }}">

                {{-- Category selector --}}
                <div>
                    <label for="expense_category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Expense Category <span class="text-rose-500">*</span>
                    </label>
                    <select name="expense_category_id" id="expense_category_id" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-rec="{{ $recommendations[$cat->id] ?? 0 }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_category_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror

                    {{-- Dynamic AI / Past Spending Suggestion Pill --}}
                    <div id="suggestion-box" class="mt-2.5 hidden p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-slate-600">
                            <span class="text-emerald-600 font-bold">💡 Historical Average:</span>
                            <span>Past 3-month avg spending is <strong id="rec-amount" class="font-mono text-slate-900">{{ currency_symbol() }}0.00</strong></span>
                        </div>
                        <button type="button" id="use-suggestion-btn" class="font-bold text-emerald-600 hover:text-emerald-700 underline text-xs">
                            Use this
                        </button>
                    </div>
                </div>

                {{-- Amount --}}
                <div>
                    <label for="amount" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Monthly Spending Limit <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400 font-mono">{{ currency_symbol() }}</span>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                            class="block w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 font-mono font-bold text-base focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                            placeholder="0.00">
                    </div>
                    @error('amount') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Alert Threshold Slider / Input --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="alert_threshold" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Warning Alert Threshold
                        </label>
                        <span class="text-xs font-mono font-bold text-slate-700" id="threshold-val">{{ old('alert_threshold', 80) }}%</span>
                    </div>
                    <input type="range" name="alert_threshold" id="alert_threshold" min="50" max="100" step="5" value="{{ old('alert_threshold', 80) }}"
                        class="w-full accent-emerald-600 cursor-pointer">
                    <p class="text-[11px] text-slate-400">Trigger warning pills and notifications when spending reaches this percentage.</p>
                    @error('alert_threshold') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Notify overrun toggle --}}
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="notify_overrun" id="notify_overrun" value="1" {{ old('notify_overrun', '1') ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500 cursor-pointer">
                    <label for="notify_overrun" class="text-xs font-semibold text-slate-700 cursor-pointer">
                        Highlight and flag immediately when spending exceeds this target
                    </label>
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Budget Notes / Guidelines
                    </label>
                    <textarea name="notes" id="notes" rows="2"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="e.g. Keep dining out under 3 times this month...">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Save Budget Target
                    </button>
                    <a href="{{ route('budgets.index', ['month' => $month]) }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const catSelect = document.getElementById('expense_category_id');
            const suggestionBox = document.getElementById('suggestion-box');
            const recAmountSpan = document.getElementById('rec-amount');
            const useBtn = document.getElementById('use-suggestion-btn');
            const amountInput = document.getElementById('amount');
            const slider = document.getElementById('alert_threshold');
            const sliderVal = document.getElementById('threshold-val');

            const currencySym = '{{ currency_symbol() }}';

            function updateSuggestion() {
                const opt = catSelect.selectedOptions[0];
                if (!opt || !opt.value) {
                    suggestionBox.classList.add('hidden');
                    return;
                }
                const rec = parseFloat(opt.getAttribute('data-rec')) || 0;
                if (rec > 0) {
                    recAmountSpan.textContent = currencySym + rec.toFixed(2);
                    useBtn.onclick = function() {
                        amountInput.value = rec.toFixed(2);
                    };
                    suggestionBox.classList.remove('hidden');
                } else {
                    suggestionBox.classList.add('hidden');
                }
            }

            catSelect.addEventListener('change', updateSuggestion);
            updateSuggestion();

            slider.addEventListener('input', function () {
                sliderVal.textContent = slider.value + '%';
            });
        });
    </script>
    @endpush
</x-layouts.app>
