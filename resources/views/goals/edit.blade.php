<x-layouts.app :title="'Edit Vault - ' . $goal->name">
    @section('page-title', 'Edit Savings Vault')

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('goals.show', $goal) }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to {{ $goal->name }}
            </a>

            <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Are you sure you want to delete this savings vault? All contribution history for this vault will be deleted.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                    Delete Vault
                </button>
            </form>
        </div>

        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Edit Savings Vault</h2>
                <p class="text-xs text-slate-500 mt-1">Update targets, status, deadlines, or security lock settings.</p>
            </div>

            <form method="POST" action="{{ route('goals.update', $goal) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Name --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Vault Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $goal->name) }}"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-semibold focus:outline-none focus:border-emerald-500 transition">
                        @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Target Amount --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Target Goal Amount ({{ $currency }}) *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono font-bold text-sm">{{ $currency }}</span>
                            <input type="number" step="0.01" min="1" name="target_amount" required value="{{ old('target_amount', $goal->target_amount) }}"
                                class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-emerald-500">
                        </div>
                        @error('target_amount') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Vault Status *</label>
                        <select name="status" required
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                            <option value="active" {{ old('status', $goal->status) === 'active' ? 'selected' : '' }}>Active (Accepting deposits)</option>
                            <option value="completed" {{ old('status', $goal->status) === 'completed' ? 'selected' : '' }}>Completed (Goal reached 🎉)</option>
                            <option value="paused" {{ old('status', $goal->status) === 'paused' ? 'selected' : '' }}>Paused (Contributions on hold)</option>
                            <option value="cancelled" {{ old('status', $goal->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Target Completion Date --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Target Completion Date (Optional)</label>
                        <input type="date" name="target_date" value="{{ old('target_date', $goal->target_date ? $goal->target_date->format('Y-m-d') : '') }}"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                        @error('target_date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Funding Account --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Designated Funding Account (Optional)</label>
                        <select name="account_id"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                            <option value="">Virtual Allocation (No designated physical account)</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('account_id', $goal->account_id) == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} (Balance: {{ format_currency($acc->balance) }})
                                </option>
                            @endforeach
                        </select>
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
                                <input type="radio" name="color" value="{{ $colKey }}" {{ old('color', $goal->color) === $colKey ? 'checked' : '' }} class="sr-only">
                                <span class="w-6 h-6 rounded-full {{ explode(' ', $colClasses)[0] }} shadow-sm group-hover:scale-110 transition"></span>
                                <span class="text-[11px] font-semibold text-slate-700">{{ $colLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Description --}}
                <div class="space-y-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Description & Purpose (Optional)</label>
                        <textarea name="description" rows="2"
                            class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs focus:outline-none focus:border-emerald-500">{{ old('description', $goal->description) }}</textarea>
                    </div>

                    {{-- Vault Lock Feature --}}
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex items-start gap-3">
                        <input type="checkbox" name="is_locked" id="isLocked" value="1" {{ old('is_locked', $goal->is_locked) ? 'checked' : '' }}
                            class="w-4 h-4 mt-0.5 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <label for="isLocked" class="text-xs font-bold text-slate-800 cursor-pointer flex items-center gap-1">
                                🔒 Vault Lock (Early Withdrawal Protection)
                            </label>
                            <p class="text-[11px] text-slate-500 mt-0.5">When checked, prevents early withdrawals until the full target amount is reached.</p>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('goals.show', $goal) }}"
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg transition">
                        Update Vault
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
