<x-layouts.app :title="'Transfer Report'">
    @section('page-title', 'Transfer Report')
    @section('page-actions')
        <a href="{{ route('reports.export.transfer', request()->query()) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Export CSV
        </a>
    @endsection

    <div class="mb-6">
        <x-date-filter :action="route('reports.transfer')">
            <div>
                <label for="from_account_id" class="block text-xs font-medium text-gray-500 mb-1">From Account</label>
                <select name="from_account_id" id="from_account_id" class="block rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('from_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="to_account_id" class="block text-xs font-medium text-gray-500 mb-1">To Account</label>
                <select name="to_account_id" id="to_account_id" class="block rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('to_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
        </x-date-filter>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <x-card title="Total Transfers" :value="format_currency($totalTransfers)" color="blue" />
        <x-card title="Number of Transfers" :value="number_format($transferCount)" color="blue" />
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Transfer Details</h3>
        </div>
        @if($transactions->isEmpty())
            <x-empty-state message="No transfers found for the selected filters." />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">From Account</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">To Account</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transactions as $txn)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $txn->date->format(app_date_format()) }}</td>
                                <td class="px-5 py-3 text-sm text-gray-900">{{ $txn->fromAccount?->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-900">{{ $txn->toAccount?->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm font-medium text-blue-600 text-right">{{ format_currency($txn->amount) }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $txn->description ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">{{ $transactions->links() }}</div>
            @endif
        @endif
    </div>
</x-layouts.app>
