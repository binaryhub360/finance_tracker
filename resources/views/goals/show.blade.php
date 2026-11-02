<x-layouts.app :title="'Savings Vault - ' . $goal->name">
    @section('page-title', $goal->name)

    @php
        $theme = $goal->color_theme;
        $statusBadge = $goal->status_badge;
    @endphp

    <div class="space-y-6">
        {{-- Back Link & Controls --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('goals.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to All Vaults
            </a>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Quick Lock / Unlock Action --}}
                <form method="POST" action="{{ route('goals.toggle-lock', $goal) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold {{ $goal->is_locked ? 'bg-amber-50 text-amber-700 border border-amber-200/80 hover:bg-amber-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition">
                        <span>{{ $goal->is_locked ? '🔒 Locked' : '🔓 Unlocked' }}</span>
                        <span class="text-[10px] text-slate-400">({{ $goal->is_locked ? 'Click to Unlock' : 'Click to Lock' }})</span>
                    </button>
                </form>

                <a href="{{ route('goals.edit', $goal) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Vault
                </a>
            </div>
        </div>

        {{-- Hero Vault Dossier Banner --}}
        <div class="relative overflow-hidden bg-white/90 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                {{-- Left Side: Icon + Title + Progress --}}
                <div class="flex items-start gap-4 sm:gap-5 min-w-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-tr {{ $theme['gradient'] }} text-white flex items-center justify-center font-bold text-2xl shadow-xl {{ $theme['glow'] }} shrink-0">
                        @if($goal->is_locked)
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        @else
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>

                    <div class="space-y-1.5 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight truncate">{{ $goal->name }}</h2>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusBadge['classes'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                {{ $statusBadge['label'] }}
                            </span>
                            @if($goal->is_locked)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                    🔒 Early Withdrawal Locked
                                </span>
                            @endif
                        </div>

                        @if($goal->description)
                            <p class="text-xs text-slate-500 leading-relaxed max-w-2xl">{{ $goal->description }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                            @if($goal->account)
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                    Funding Account: <strong class="text-slate-800">{{ $goal->account->name }}</strong>
                                </span>
                            @else
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                    Virtual Dedicated Bucket
                                </span>
                            @endif

                            @if($goal->target_date)
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                    Target Date: <strong class="text-slate-800">{{ $goal->target_date->format('M d, Y') }}</strong>
                                    @if(!$goal->is_completed)
                                        <span class="{{ $goal->is_overdue ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                            ({{ $goal->is_overdue ? 'Passed' : $goal->days_remaining . ' days left' }})
                                        </span>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons: Deposit & Withdraw --}}
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <button type="button" onclick="openDepositModal()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-600/25 transition-all duration-200 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Deposit Funds
                    </button>

                    <button type="button" onclick="openWithdrawModal()"
                        {{ $goal->is_locked && !$goal->is_completed ? 'disabled' : '' }}
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold {{ $goal->is_locked && !$goal->is_completed ? 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 shadow-xs' }} transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                        </svg>
                        Release / Withdraw
                    </button>
                </div>
            </div>

            {{-- Progress Bar & Metrics Strip --}}
            <div class="mt-8 pt-6 border-t border-slate-100 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold font-mono text-slate-900 tracking-tight">
                            {{ format_currency($goal->current_amount) }}
                        </span>
                        <span class="text-sm font-semibold text-slate-400 font-mono">
                            saved of {{ format_currency($goal->target_amount) }} target
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs font-mono">
                        <span class="font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-200/60">
                            {{ $goal->progress_percentage }}% Complete
                        </span>
                        @if($goal->is_completed)
                            <span class="font-bold text-emerald-700">Target Reached 🎉</span>
                        @else
                            <span class="text-slate-500">
                                <strong>{{ format_currency($goal->remaining_amount) }}</strong> needed
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Visual Progress Bar --}}
                <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden p-0.5">
                    <div class="h-full rounded-full bg-gradient-to-r {{ $theme['gradient'] }} transition-all duration-700 shadow-xs"
                        style="width: {{ $goal->progress_percentage }}%"></div>
                </div>

                @if(!$goal->is_completed && $goal->recommended_monthly_contribution)
                    <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                        <span>Pace Recommendation:</span>
                        <span class="font-bold font-mono text-emerald-700">
                            Save approx. {{ format_currency($goal->recommended_monthly_contribution) }} / month to reach target by {{ $goal->target_date->format('M Y') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Contributions & Withdrawals Ledger --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Vault Activity Ledger</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Every deposit and withdrawal made to and from this savings vault.</p>
                </div>

                <span class="text-xs text-slate-400 font-mono">
                    {{ $contributions->total() }} recorded {{ Str::plural('movement', $contributions->total()) }}
                </span>
            </div>

            @if($contributions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px]">
                                <th class="pb-3 font-semibold">Date</th>
                                <th class="pb-3 font-semibold">Type</th>
                                <th class="pb-3 font-semibold">Amount</th>
                                <th class="pb-3 font-semibold">Source / Dest Account</th>
                                <th class="pb-3 font-semibold">Ledger Transaction</th>
                                <th class="pb-3 font-semibold">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($contributions as $item)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 text-slate-700 font-mono">
                                        {{ $item->date ? $item->date->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $item->type_badge_classes }}">
                                            {{ $item->type === 'deposit' ? '↓ Deposit' : '↑ Withdrawal' }}
                                        </span>
                                    </td>
                                    <td class="py-3 font-mono font-bold {{ $item->type === 'deposit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $item->type === 'deposit' ? '+' : '-' }}{{ format_currency($item->amount) }}
                                    </td>
                                    <td class="py-3 text-slate-600">
                                        {{ $item->account ? $item->account->name : 'Virtual Allocation' }}
                                    </td>
                                    <td class="py-3 text-slate-500">
                                        @if($item->transaction)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200/60" title="Linked to transaction #{{ $item->transaction->id }}">
                                                ✓ #{{ $item->transaction->id }} ({{ $item->transaction->type }})
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">No account tx</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-slate-500 max-w-xs truncate">
                                        {{ $item->notes ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    {{ $contributions->links() }}
                </div>
            @else
                <div class="py-12 text-center text-slate-400">
                    <p class="text-xs">No contributions recorded yet. Click "Deposit Funds" to add money to this vault.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Deposit Modal --}}
    <div id="depositModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Deposit into {{ $goal->name }}</h3>
                        <p class="text-xs text-slate-400 font-medium">Add to your savings progress</p>
                    </div>
                </div>
                <button type="button" onclick="closeDepositModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('goals.deposit', $goal) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Amount to Deposit ({{ $currency }}) *</label>
                    <input type="number" step="0.01" min="0.01" name="amount" required autofocus
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-emerald-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Funding Account</label>
                    <select name="account_id"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500">
                        <option value="">Virtual Allocation (No bank balance deduction)</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $goal->account_id == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }} (Balance: {{ format_currency($acc->balance) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="deduct_from_account" value="1" checked
                            class="w-4 h-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs text-slate-700 font-medium">Record as Transaction in Account Ledger</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1 pl-6">Automatically creates an expense transaction linked to this goal.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes (Optional)</label>
                    <input type="text" name="notes" placeholder="e.g. Monthly savings contribution"
                        class="block w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs focus:outline-none focus:border-emerald-500">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeDepositModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition">
                        Confirm Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Withdraw Modal --}}
    <div id="withdrawModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Withdraw from {{ $goal->name }}</h3>
                        <p class="text-xs text-slate-400 font-medium">Release funds for expenses or milestone payout</p>
                    </div>
                </div>
                <button type="button" onclick="closeWithdrawModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('goals.withdraw', $goal) }}" class="space-y-4">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Amount to Withdraw ({{ $currency }}) *</label>
                        <span class="text-[11px] text-slate-400 font-mono">Max: {{ format_currency($goal->current_amount) }}</span>
                    </div>
                    <input type="number" step="0.01" min="0.01" max="{{ $goal->current_amount }}" name="amount" required autofocus
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-rose-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Destination Account (Optional)</label>
                    <select name="account_id"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-rose-500">
                        <option value="">Virtual Allocation (No bank balance credit)</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $goal->account_id == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }} (Balance: {{ format_currency($acc->balance) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="credit_to_account" value="1" checked
                            class="w-4 h-4 rounded-md border-slate-300 text-rose-600 focus:ring-rose-500">
                        <span class="text-xs text-slate-700 font-medium">Credit Back to Account Ledger</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1 pl-6">Automatically creates an income/transfer transaction into destination account.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes (Optional)</label>
                    <input type="text" name="notes" placeholder="e.g. Purchased new laptop equipment"
                        class="block w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs focus:outline-none focus:border-rose-500">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeWithdrawModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-600/20 transition">
                        Confirm Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDepositModal() {
            document.getElementById('depositModal').classList.remove('hidden');
        }
        function closeDepositModal() {
            document.getElementById('depositModal').classList.add('hidden');
        }

        function openWithdrawModal() {
            document.getElementById('withdrawModal').classList.remove('hidden');
        }
        function closeWithdrawModal() {
            document.getElementById('withdrawModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
