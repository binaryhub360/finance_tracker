<x-layouts.app :title="'Invoices'">
    @section('page-title', 'Invoices & Billing')
    @section('page-actions')
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Invoice
        </a>
    @endsection

    {{-- Top Overview KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card title="Total Billed" :value="format_currency($totalInvoiced)" color="emerald" />
        <x-card title="Total Collected" :value="format_currency($totalPaid)" color="blue" />
        <x-card title="Pending Receivables" :value="format_currency($totalOutstanding)" color="amber" />
        <x-card title="Overdue Invoices" :value="number_format($overdueCount) . ' (' . format_currency($overdueAmount) . ')'" color="rose" />
    </div>

    {{-- Filter and Search Controls --}}
    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-4 mb-6 shadow-xs">
        <form method="GET" action="{{ route('invoices.index') }}" class="space-y-3">
            {{-- Status Pills --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                @php
                    $currentStatus = request('status', '');
                    $statuses = [
                        '' => 'All Invoices',
                        'draft' => 'Drafts',
                        'sent' => 'Sent',
                        'partially_paid' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ];
                @endphp
                @foreach($statuses as $key => $label)
                    <a href="{{ route('invoices.index', array_merge(request()->except('status'), $key ? ['status' => $key] : [])) }}"
                        class="px-3 py-1.5 rounded-xl font-semibold transition-all whitespace-nowrap {{ $currentStatus === $key ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 pt-1">
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3.5 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="Search invoice number (e.g. INV-2026-0001) or client name...">
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <select name="client_id" class="px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-700 text-xs font-medium focus:outline-none focus:border-emerald-500 transition-all">
                        <option value="">All Clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'status', 'client_id']))
                        <a href="{{ route('invoices.index') }}" class="p-2 text-slate-400 hover:text-slate-600 transition-colors" title="Clear Filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl overflow-hidden shadow-sm">
        @if($invoices->isEmpty())
            <x-empty-state message="No invoices found." :action="route('invoices.create')" actionText="Create Your First Invoice" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Invoice #</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Issue Date</th>
                            <th class="px-6 py-4">Due Date</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-right">Balance Due</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($invoices as $inv)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('invoices.show', $inv) }}" class="font-bold text-slate-900 hover:text-emerald-600 transition-colors font-mono">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($inv->client->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('clients.show', $inv->client) }}" class="font-semibold text-slate-800 hover:text-emerald-600 transition-colors">
                                                {{ $inv->client->name }}
                                            </a>
                                            @if($inv->client->company_name)
                                                <p class="text-[10px] text-slate-400">{{ $inv->client->company_name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $inv->issue_date->format(app_date_format()) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $inv->is_overdue && $inv->status !== 'paid' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                        {{ $inv->due_date->format(app_date_format()) }}
                                    </span>
                                    @if($inv->is_overdue && $inv->status !== 'paid')
                                        <span class="block text-[10px] text-rose-500 font-medium">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ format_currency($inv->total) }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-bold whitespace-nowrap {{ $inv->balance_due > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    {{ format_currency($inv->balance_due) }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $inv->status_badge_classes }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $inv->status === 'paid' ? 'bg-emerald-500' : ($inv->status === 'partially_paid' ? 'bg-amber-500' : ($inv->is_overdue ? 'bg-rose-500' : 'bg-slate-400')) }}"></span>
                                        {{ $inv->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('invoices.show', $inv) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="View Details">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Print / PDF">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.03.543-2.029 1.6-2.029h7.36c1.057 0 1.84 1 1.6 2.029l-.64 2.743H7.36l-.64-2.743z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </a>
                                        @if($inv->status !== 'paid')
                                            <a href="{{ route('invoices.edit', $inv) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Edit">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
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
</x-layouts.app>
