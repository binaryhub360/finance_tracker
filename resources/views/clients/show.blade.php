<x-layouts.app :title="$client->name . ' - Client Details'">
    @section('page-title', 'Client Profile')
    @section('page-actions')
        <div class="flex items-center gap-2.5">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white/80 hover:bg-slate-100 border border-slate-200 transition-all duration-200">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back
            </a>
            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-sm shadow-emerald-600/20 hover:shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Invoice
            </a>
            <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                Edit Client
            </a>
        </div>
    @endsection

    <div class="space-y-6">
        {{-- Client Summary Card --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500/20 via-teal-500/15 to-emerald-500/10 border border-emerald-500/30 flex items-center justify-center font-bold text-emerald-800 text-xl shadow-sm">
                        {{ strtoupper(substr($client->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ $client->name }}</h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $client->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $client->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $client->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if($client->company_name)
                            <p class="text-sm font-medium text-slate-500 mt-0.5">{{ $client->company_name }}</p>
                        @endif
                    </div>
                </div>

                {{-- Client Quick Stats --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Billed</span>
                        <span class="font-mono text-sm font-bold text-slate-900 mt-0.5 block">{{ format_currency($client->total_billed) }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-center">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-600">Collected</span>
                        <span class="font-mono text-sm font-bold text-emerald-700 mt-0.5 block">{{ format_currency($client->total_paid) }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-100 text-center">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-600">Outstanding</span>
                        <span class="font-mono text-sm font-bold text-amber-700 mt-0.5 block">{{ format_currency($client->outstanding_balance) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 text-xs">
                <div>
                    <span class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Email & Phone</span>
                    <p class="font-medium text-slate-800">{{ $client->email ?? 'No email provided' }}</p>
                    <p class="text-slate-500 mt-0.5">{{ $client->phone ?? 'No phone number' }}</p>
                </div>
                <div>
                    <span class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Billing Address</span>
                    <p class="font-medium text-slate-800 leading-relaxed">{{ $client->address ?? 'No address registered' }}</p>
                </div>
                <div>
                    <span class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Tax / VAT ID & Notes</span>
                    <p class="font-mono font-medium text-slate-800">{{ $client->tax_number ? 'VAT: ' . $client->tax_number : 'None' }}</p>
                    @if($client->notes)
                        <p class="text-slate-500 mt-1 italic">{{ $client->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Client Invoices Table --}}
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Invoice History ({{ $invoices->total() }})</h3>
                <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    + New Invoice
                </a>
            </div>

            @if($invoices->isEmpty())
                <div class="p-8 text-center text-xs text-slate-400">
                    No invoices created for this client yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left">
                        <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-6 py-3.5">Invoice #</th>
                                <th class="px-6 py-3.5">Issue Date</th>
                                <th class="px-6 py-3.5">Due Date</th>
                                <th class="px-6 py-3.5 text-right">Total</th>
                                <th class="px-6 py-3.5 text-right">Balance Due</th>
                                <th class="px-6 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($invoices as $inv)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-6 py-3.5 whitespace-nowrap">
                                        <a href="{{ route('invoices.show', $inv) }}" class="font-bold text-slate-900 hover:text-emerald-600 font-mono">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ $inv->issue_date->format(app_date_format()) }}</td>
                                    <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ $inv->due_date->format(app_date_format()) }}</td>
                                    <td class="px-6 py-3.5 font-mono font-semibold text-slate-900 text-right whitespace-nowrap">{{ format_currency($inv->total) }}</td>
                                    <td class="px-6 py-3.5 font-mono font-bold text-right whitespace-nowrap {{ $inv->balance_due > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                        {{ format_currency($inv->balance_due) }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $inv->status_badge_classes }}">
                                            {{ $inv->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                        <a href="{{ route('invoices.show', $inv) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors">
                                            View &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $invoices->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
