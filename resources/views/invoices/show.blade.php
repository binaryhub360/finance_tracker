<x-layouts.app :title="'Invoice ' . $invoice->invoice_number">
    @section('page-title', 'Invoice ' . $invoice->invoice_number)

    <div class="space-y-6">
        {{-- Top Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-bold font-mono text-slate-900 tracking-tight">{{ $invoice->invoice_number }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $invoice->status_badge_classes }}">
                            {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                        </span>
                        @if($invoice->is_overdue && $invoice->status !== 'paid')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/10 text-rose-700 border border-rose-500/20">
                                Overdue
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Created on {{ $invoice->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Print / Preview --}}
                <a href="{{ route('invoices.print', $invoice) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200/80 hover:bg-slate-50 shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.077-.4-2.224-.4-3.419 0-4.418 3.582-8 8-8s8 3.582 8 8c0 1.195-.16 2.342-.4 3.419m-15.2 0a9.96 9.96 0 001.272 4.171M6.72 13.829a9.96 9.96 0 011.272-4.171M19.28 13.829a9.96 9.96 0 01-1.272 4.171m1.272-4.171a9.96 9.96 0 00-1.272-4.171M12 18.75v-6m-3 3l3-3 3 3" />
                    </svg>
                    Print / PDF
                </a>

                {{-- Mark Sent if Draft --}}
                @if($invoice->status === 'draft')
                    <form method="POST" action="{{ route('invoices.sent', $invoice) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Mark Sent
                        </button>
                    </form>
                @endif

                {{-- Record Payment Modal Trigger --}}
                @if($invoice->balance_due > 0 && $invoice->status !== 'cancelled')
                    <button type="button" onclick="openPaymentModal()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Record Payment
                    </button>
                @endif

                {{-- Edit --}}
                @if($invoice->status !== 'paid')
                    <a href="{{ route('invoices.edit', $invoice) }}"
                        class="p-2 text-slate-600 hover:text-slate-900 bg-white border border-slate-200/80 rounded-xl hover:bg-slate-50 transition-colors" title="Edit Invoice">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </a>
                @endif

                {{-- Delete --}}
                <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Are you sure you want to delete this invoice? Linked payment transactions will also be reversed.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 bg-white border border-rose-200/80 rounded-xl hover:bg-rose-50 transition-colors" title="Delete Invoice">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Billed</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">
                    {{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}
                </p>
                <div class="mt-2 text-[11px] text-slate-400">Subtotal + Tax - Discounts</div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Amount Received</span>
                <p class="text-2xl font-bold font-mono text-emerald-600 mt-1">
                    {{ $invoice->currency }} {{ number_format($invoice->paid_amount, 2) }}
                </p>
                <div class="mt-2 text-[11px] text-emerald-600/70 font-medium">Reconciled in accounts</div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-600">Balance Due</span>
                <p class="text-2xl font-bold font-mono {{ $invoice->balance_due > 0 ? 'text-amber-600' : 'text-slate-400' }} mt-1">
                    {{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}
                </p>
                <div class="mt-2 text-[11px] text-slate-400">
                    Due by {{ $invoice->due_date->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- Invoice Body (Paper / Document Look) --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-900/5 space-y-8">
            {{-- Header with Client and Invoice Meta --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pb-8 border-b border-slate-100">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Billed To</span>
                    <a href="{{ route('clients.show', $invoice->client) }}" class="text-lg font-bold text-slate-900 hover:text-emerald-600 transition-colors">
                        {{ $invoice->client->name }}
                    </a>
                    @if($invoice->client->company_name)
                        <p class="text-sm font-medium text-slate-600 mt-0.5">{{ $invoice->client->company_name }}</p>
                    @endif
                    @if($invoice->client->email)
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            {{ $invoice->client->email }}
                        </p>
                    @endif
                    @if($invoice->client->phone)
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            {{ $invoice->client->phone }}
                        </p>
                    @endif
                    @if($invoice->client->address)
                        <p class="text-xs text-slate-500 mt-1 whitespace-pre-line">{{ $invoice->client->address }}</p>
                    @endif
                </div>

                <div class="sm:text-right space-y-2">
                    <div class="inline-block text-left sm:text-right">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Invoice Metadata</span>
                        <div class="mt-2 space-y-1.5 text-xs">
                            <div class="flex sm:justify-end gap-3">
                                <span class="text-slate-500">Invoice Number:</span>
                                <span class="font-mono font-bold text-slate-900">{{ $invoice->invoice_number }}</span>
                            </div>
                            <div class="flex sm:justify-end gap-3">
                                <span class="text-slate-500">Issue Date:</span>
                                <span class="font-medium text-slate-800">{{ $invoice->issue_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex sm:justify-end gap-3">
                                <span class="text-slate-500">Due Date:</span>
                                <span class="font-medium {{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'text-rose-600 font-bold' : 'text-slate-800' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </span>
                            </div>
                            <div class="flex sm:justify-end gap-3">
                                <span class="text-slate-500">Payment Status:</span>
                                <span class="font-bold {{ $invoice->balance_due <= 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $invoice->balance_due <= 0 ? 'Fully Paid' : 'Pending Payment' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Line Items Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="pb-3 w-1/2">Description</th>
                            <th class="pb-3 w-24 text-center">Qty</th>
                            <th class="pb-3 w-32 text-right">Unit Price</th>
                            <th class="pb-3 w-32 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoice->items as $item)
                            <tr class="text-xs">
                                <td class="py-3 font-medium text-slate-800">{{ $item->description }}</td>
                                <td class="py-3 text-center font-mono text-slate-600">{{ (float)$item->quantity }}</td>
                                <td class="py-3 text-right font-mono text-slate-600">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Calculations Summary --}}
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row justify-end">
                <div class="w-full sm:w-72 space-y-2.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-mono font-semibold text-slate-900">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>

                    @if($invoice->tax_rate > 0)
                        <div class="flex justify-between text-slate-600">
                            <span>Tax ({{ (float)$invoice->tax_rate }}%)</span>
                            <span class="font-mono font-semibold text-slate-900">+{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-emerald-600">
                            <span>Discount</span>
                            <span class="font-mono font-semibold">-{{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between pt-2.5 border-t border-slate-200 text-sm font-bold text-slate-900">
                        <span>Grand Total</span>
                        <span class="font-mono text-emerald-600">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-xs text-slate-500">
                        <span>Amount Paid</span>
                        <span class="font-mono font-semibold text-slate-700">{{ $invoice->currency }} {{ number_format($invoice->paid_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between pt-2 border-t border-slate-100 font-bold text-xs {{ $invoice->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                        <span>Balance Due</span>
                        <span class="font-mono text-sm">{{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes & Terms --}}
            @if($invoice->notes || $invoice->terms)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-100 text-xs text-slate-600">
                    @if($invoice->notes)
                        <div>
                            <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Notes</span>
                            <p class="whitespace-pre-line text-slate-700 bg-slate-50/70 p-3 rounded-xl border border-slate-100">{{ $invoice->notes }}</p>
                        </div>
                    @endif
                    @if($invoice->terms)
                        <div>
                            <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block mb-1">Payment Instructions & Terms</span>
                            <p class="whitespace-pre-line text-slate-700 bg-slate-50/70 p-3 rounded-xl border border-slate-100">{{ $invoice->terms }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Payment History & Auto-Reconciled Ledger Card --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Payment Ledger</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Automated accounting entries linked to this invoice.</p>
                </div>
                @if($invoice->balance_due > 0 && $invoice->status !== 'cancelled')
                    <button type="button" onclick="openPaymentModal()" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                        + Add Payment
                    </button>
                @endif
            </div>

            @if($invoice->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <th class="pb-2.5">Date</th>
                                <th class="pb-2.5">Account Credited</th>
                                <th class="pb-2.5">Method</th>
                                <th class="pb-2.5">Reference</th>
                                <th class="pb-2.5 text-right">Amount</th>
                                <th class="pb-2.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="py-3 font-medium text-slate-800">{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $payment->account ? $payment->account->name : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-600 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                    <td class="py-3 font-mono text-slate-500">{{ $payment->reference ?: '-' }}</td>
                                    <td class="py-3 text-right font-mono font-bold text-emerald-600">
                                        +{{ $invoice->currency }} {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <form method="POST" action="{{ route('invoices.payments.destroy', [$invoice, $payment]) }}"
                                            onsubmit="return confirm('Reversing this payment will delete the linked income transaction from your account ledger. Proceed?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition-colors" title="Reverse Payment">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-xs text-slate-400">No payments recorded yet.</p>
                    @if($invoice->balance_due > 0 && $invoice->status !== 'cancelled')
                        <button type="button" onclick="openPaymentModal()" class="mt-2 text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            Record initial payment
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Record Payment Modal --}}
    <div id="payment-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Record Payment</h3>
                    <p class="text-xs text-slate-500">Collect payment and credit an account ledger.</p>
                </div>
                <button type="button" onclick="closePaymentModal()" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="space-y-4">
                @csrf

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="modal-amount" class="block text-xs font-semibold text-slate-600">Payment Amount <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] text-slate-400">Balance: {{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">{{ $invoice->currency }}</span>
                        <input type="number" name="amount" id="modal-amount" value="{{ old('amount', (float)$invoice->balance_due) }}"
                            step="0.01" min="0.01" max="{{ $invoice->balance_due }}" required
                            class="block w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 font-mono font-bold text-sm focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>
                </div>

                <div>
                    <label for="modal-account" class="block text-xs font-semibold text-slate-600 mb-1">Deposit To Account <span class="text-rose-500">*</span></label>
                    <select name="account_id" id="modal-account" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        <option value="">Select Account...</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">
                                {{ $acc->name }} ({{ $acc->type_label }} - Balance: {{ currency_symbol() }} {{ number_format($acc->balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="modal-date" class="block text-xs font-semibold text-slate-600 mb-1">Payment Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="payment_date" id="modal-date" value="{{ date('Y-m-d') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>

                    <div>
                        <label for="modal-method" class="block text-xs font-semibold text-slate-600 mb-1">Payment Method <span class="text-rose-500">*</span></label>
                        <select name="payment_method" id="modal-method" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="modal-reference" class="block text-xs font-semibold text-slate-600 mb-1">Reference / Trx ID</label>
                    <input type="text" name="reference" id="modal-reference" placeholder="e.g. TRX-982312 or Check #104"
                        class="block w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label for="modal-notes" class="block text-xs font-semibold text-slate-600 mb-1">Notes</label>
                    <textarea name="notes" id="modal-notes" rows="2" placeholder="Optional payment remarks..."
                        class="block w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-100 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-[11px] text-emerald-800 leading-relaxed">
                        This creates an <strong>Income</strong> transaction under "Invoice Payments" and updates your selected account balance automatically.
                    </p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition-all">
                        Confirm & Record
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openPaymentModal() {
            document.getElementById('payment-modal').classList.remove('hidden');
        }
        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
        }
        // Close on backdrop click
        document.getElementById('payment-modal').addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });
    </script>
    @endpush
</x-layouts.app>
