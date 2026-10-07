<x-layouts.app :title="'Edit Invoice ' . $invoice->invoice_number">
    @section('page-title', 'Edit Invoice ' . $invoice->invoice_number)

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoice-form" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Invoice Meta Card --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Invoice Details</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Edit recipient, invoice numbering, and billing schedule.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $invoice->status_badge_classes }}">
                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Client selector --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="client_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Client <span class="text-rose-500">*</span></label>
                            <a href="{{ route('clients.create') }}" class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700" target="_blank">+ New Client</a>
                        </div>
                        <select name="client_id" id="client_id" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            <option value="">Select a Client...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_id', $invoice->client_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} {{ $c->company_name ? "({$c->company_name})" : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Invoice Number --}}
                    <div>
                        <label for="invoice_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Invoice Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-bold font-mono tracking-tight focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('invoice_number') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            <option value="draft" {{ old('status', $invoice->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ old('status', $invoice->status) === 'sent' ? 'selected' : '' }}>Sent</option>
                            @if($invoice->status === 'partially_paid')
                                <option value="partially_paid" selected>Partially Paid</option>
                            @endif
                            <option value="cancelled" {{ old('status', $invoice->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Issue Date --}}
                    <div>
                        <label for="issue_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Issue Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('issue_date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label for="due_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Due Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('due_date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Currency --}}
                    <div>
                        <label for="currency" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Currency</label>
                        <input type="text" name="currency" id="currency" value="{{ old('currency', $invoice->currency) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-semibold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono"
                            placeholder="e.g. ৳, $, EUR">
                        @error('currency') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Line Items Table Card --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight">Line Items</h3>
                        <p class="text-xs text-slate-500 mt-0.5">List products, services, hours, or deliverables.</p>
                    </div>
                    <button type="button" id="add-item-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left" id="items-table">
                        <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="pb-3 w-1/2">Item Description <span class="text-rose-500">*</span></th>
                                <th class="pb-3 w-24">Qty <span class="text-rose-500">*</span></th>
                                <th class="pb-3 w-32">Price <span class="text-rose-500">*</span></th>
                                <th class="pb-3 w-32 text-right">Total</th>
                                <th class="pb-3 w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 space-y-2" id="items-body">
                            @foreach($invoice->items as $idx => $item)
                                <tr class="item-row">
                                    <td class="py-2.5 pr-3">
                                        <input type="text" name="items[{{ $idx }}][description]" value="{{ old("items.{$idx}.description", $item->description) }}" required
                                            class="block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500"
                                            placeholder="Service or item description...">
                                    </td>
                                    <td class="py-2.5 pr-3">
                                        <input type="number" name="items[{{ $idx }}][quantity]" value="{{ old("items.{$idx}.quantity", (float)$item->quantity) }}" step="0.01" min="0.01" required
                                            class="item-qty block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-mono font-medium focus:outline-none focus:border-emerald-500">
                                    </td>
                                    <td class="py-2.5 pr-3">
                                        <input type="number" name="items[{{ $idx }}][unit_price]" value="{{ old("items.{$idx}.unit_price", (float)$item->unit_price) }}" step="0.01" min="0" required
                                            class="item-price block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-mono font-medium focus:outline-none focus:border-emerald-500">
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold text-xs text-slate-900 item-total">
                                        {{ number_format($item->total, 2, '.', '') }}
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <button type="button" class="remove-item-btn p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Summary Calculation Panel --}}
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end">
                    <div class="w-full sm:w-80 space-y-3 text-xs">
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Subtotal</span>
                            <span class="font-mono font-semibold text-slate-900" id="calc-subtotal">0.00</span>
                        </div>

                        <div class="flex justify-between items-center text-slate-600 gap-4">
                            <span class="flex items-center gap-1.5 shrink-0">
                                Tax Rate (%)
                            </span>
                            <div class="flex items-center gap-2 justify-end w-36">
                                <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', (float)$invoice->tax_rate) }}" step="0.1" min="0" max="100"
                                    class="w-16 px-2 py-1 text-right rounded-lg border border-slate-200 bg-white/70 text-xs font-mono focus:outline-none focus:border-emerald-500">
                                <span class="font-mono font-semibold text-slate-900" id="calc-tax">+0.00</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-slate-600 gap-4">
                            <span class="shrink-0">Discount Amount</span>
                            <div class="flex items-center justify-end w-36">
                                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', (float)$invoice->discount_amount) }}" step="0.01" min="0"
                                    class="w-24 px-2 py-1 text-right rounded-lg border border-slate-200 bg-white/70 text-xs font-mono focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-slate-200 text-sm font-bold text-slate-900">
                            <span>Total Due</span>
                            <span class="font-mono text-base text-emerald-600" id="calc-total">0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes & Terms Card --}}
            <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Client Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                            placeholder="Thank you for your business!">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>

                    <div>
                        <label for="terms" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Payment Terms & Instructions</label>
                        <textarea name="terms" id="terms" rows="3"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                            placeholder="e.g. Please send payment via bank transfer or bKash within 14 days of issue date.">{{ old('terms', $invoice->terms) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Update Invoice
                    </button>
                    <a href="{{ route('invoices.show', $invoice) }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = {{ $invoice->items->count() }};
            const itemsBody = document.getElementById('items-body');
            const addItemBtn = document.getElementById('add-item-btn');
            const taxInput = document.getElementById('tax_rate');
            const discountInput = document.getElementById('discount_amount');

            function recalculate() {
                let subtotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                    const price = parseFloat(row.querySelector('.item-price').value) || 0;
                    const total = qty * price;
                    row.querySelector('.item-total').textContent = total.toFixed(2);
                    subtotal += total;
                });

                const taxRate = parseFloat(taxInput.value) || 0;
                const taxAmount = (subtotal * taxRate) / 100;
                const discount = parseFloat(discountInput.value) || 0;
                const grandTotal = Math.max(0, subtotal + taxAmount - discount);

                document.getElementById('calc-subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('calc-tax').textContent = '+' + taxAmount.toFixed(2);
                document.getElementById('calc-total').textContent = grandTotal.toFixed(2);
            }

            addItemBtn.addEventListener('click', function () {
                const tr = document.createElement('tr');
                tr.className = 'item-row';
                tr.innerHTML = `
                    <td class="py-2.5 pr-3">
                        <input type="text" name="items[${itemIndex}][description]" required
                            class="block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500"
                            placeholder="Service or item description...">
                    </td>
                    <td class="py-2.5 pr-3">
                        <input type="number" name="items[${itemIndex}][quantity]" value="1" step="0.01" min="0.01" required
                            class="item-qty block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-mono font-medium focus:outline-none focus:border-emerald-500">
                    </td>
                    <td class="py-2.5 pr-3">
                        <input type="number" name="items[${itemIndex}][unit_price]" value="0.00" step="0.01" min="0" required
                            class="item-price block w-full px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-mono font-medium focus:outline-none focus:border-emerald-500">
                    </td>
                    <td class="py-2.5 text-right font-mono font-bold text-xs text-slate-900 item-total">
                        0.00
                    </td>
                    <td class="py-2.5 text-center">
                        <button type="button" class="remove-item-btn p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </td>
                `;
                itemsBody.appendChild(tr);
                itemIndex++;
                attachRowListeners(tr);
                recalculate();
            });

            function attachRowListeners(row) {
                row.querySelectorAll('.item-qty, .item-price').forEach(input => {
                    input.addEventListener('input', recalculate);
                });
                row.querySelector('.remove-item-btn').addEventListener('click', function () {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        row.remove();
                        recalculate();
                    }
                });
            }

            document.querySelectorAll('.item-row').forEach(attachRowListeners);
            taxInput.addEventListener('input', recalculate);
            discountInput.addEventListener('input', recalculate);
            recalculate();
        });
    </script>
    @endpush
</x-layouts.app>
