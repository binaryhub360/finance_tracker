<x-layouts.app :title="'Transaction Details'">
    @section('page-title', 'Transaction Details')
    @section('page-actions')
        <a href="{{ route('transactions.edit', $transaction) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">Edit</a>
    @endsection

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $transaction->type_bg_color }}">
                    {{ $transaction->type_label }}
                </span>
            </div>
            <dl class="divide-y divide-gray-200">
                <div class="px-6 py-4 flex justify-between">
                    <dt class="text-sm text-gray-500">Date</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $transaction->date->format(app_date_format()) }}</dd>
                </div>
                <div class="px-6 py-4 flex justify-between">
                    <dt class="text-sm text-gray-500">Amount</dt>
                    <dd class="text-lg font-semibold {{ $transaction->type_color }}">{{ format_currency($transaction->amount) }}</dd>
                </div>
                @if($transaction->type !== 'transfer')
                    <div class="px-6 py-4 flex justify-between">
                        <dt class="text-sm text-gray-500">Account</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $transaction->account?->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="px-6 py-4 flex justify-between">
                        <dt class="text-sm text-gray-500">Category</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $transaction->category_name }}</dd>
                    </div>
                @else
                    <div class="px-6 py-4 flex justify-between">
                        <dt class="text-sm text-gray-500">From Account</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $transaction->fromAccount?->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="px-6 py-4 flex justify-between">
                        <dt class="text-sm text-gray-500">To Account</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $transaction->toAccount?->name ?? 'N/A' }}</dd>
                    </div>
                @endif
                @if($transaction->description)
                    <div class="px-6 py-4 flex justify-between">
                        <dt class="text-sm text-gray-500">Description</dt>
                        <dd class="text-sm text-gray-900">{{ $transaction->description }}</dd>
                    </div>
                @endif
                @if($transaction->reference)
                    <div class="px-6 py-4 flex justify-between">
                        <dt class="text-sm text-gray-500">Reference</dt>
                        <dd class="text-sm text-gray-900">{{ $transaction->reference }}</dd>
                    </div>
                @endif
                <div class="px-6 py-4 flex justify-between">
                    <dt class="text-sm text-gray-500">Created</dt>
                    <dd class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
            <div class="px-6 py-4 border-t border-gray-200">
                <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Transactions</a>
            </div>
        </div>
    </div>
</x-layouts.app>
