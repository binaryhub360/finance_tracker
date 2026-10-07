<x-layouts.app :title="'Clients'">
    @section('page-title', 'Client Directory')
    @section('page-actions')
        <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add New Client
        </a>
    @endsection

    {{-- Top Summary Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <x-card title="Total Clients" :value="number_format($totalClients)" color="emerald" />
        <x-card title="Active Billing Accounts" :value="number_format($activeClients)" color="blue" />
    </div>

    {{-- Search & Filters Filter Bar --}}
    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-4 mb-6 shadow-xs">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3.5 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-xs font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                    placeholder="Search by client name, company, email, or phone...">
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 bg-white/70 text-slate-700 text-xs font-medium focus:outline-none focus:border-emerald-500 transition-all">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('clients.index') }}" class="p-2 text-slate-400 hover:text-slate-600 transition-colors" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Clients Ledger Table --}}
    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl overflow-hidden shadow-sm">
        @if($clients->isEmpty())
            <x-empty-state message="No clients found." :action="route('clients.create')" actionText="Add First Client" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Client / Company</th>
                            <th class="px-6 py-4">Contact Info</th>
                            <th class="px-6 py-4 text-center">Invoices</th>
                            <th class="px-6 py-4 text-right">Total Billed</th>
                            <th class="px-6 py-4 text-right">Outstanding</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($clients as $client)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 flex items-center justify-center font-bold text-emerald-700 text-xs shrink-0">
                                            {{ strtoupper(substr($client->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('clients.show', $client) }}" class="font-bold text-slate-900 hover:text-emerald-600 transition-colors">
                                                {{ $client->name }}
                                            </a>
                                            @if($client->company_name)
                                                <p class="text-[11px] text-slate-400">{{ $client->company_name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-medium text-slate-700">{{ $client->email ?? '—' }}</p>
                                    @if($client->phone)
                                        <p class="text-[11px] text-slate-400">{{ $client->phone }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                        {{ $client->invoices_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-semibold text-slate-900 whitespace-nowrap">
                                    {{ format_currency($client->total_billed) }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-bold whitespace-nowrap {{ $client->outstanding_balance > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    {{ format_currency($client->outstanding_balance) }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <form method="POST" action="{{ route('clients.toggle', $client) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold transition-all {{ $client->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $client->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $client->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="Create Invoice">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('clients.show', $client) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View Profile">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Edit Client">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($clients->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $clients->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
