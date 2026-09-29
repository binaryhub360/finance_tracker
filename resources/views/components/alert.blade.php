@props(['type' => 'success', 'message'])

@php
$classes = match($type) {
    'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    'error' => 'bg-red-50 text-red-800 border-red-200',
    'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
    'info' => 'bg-blue-50 text-blue-800 border-blue-200',
    default => 'bg-gray-50 text-gray-800 border-gray-200',
};

$iconColor = match($type) {
    'success' => 'text-emerald-500',
    'error' => 'text-red-500',
    'warning' => 'text-amber-500',
    'info' => 'text-blue-500',
    default => 'text-gray-500',
};
@endphp

<div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
    <div class="flex items-center gap-3 px-4 py-3 border rounded-lg {{ $classes }}">
        @if($type === 'success')
            <svg class="w-5 h-5 {{ $iconColor }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @else
            <svg class="w-5 h-5 {{ $iconColor }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        @endif
        <p class="text-sm flex-1">{{ $message }}</p>
        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-current opacity-50 hover:opacity-100">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
