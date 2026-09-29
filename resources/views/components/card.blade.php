@props(['title' => '', 'value' => '', 'color' => 'gray', 'icon' => null])

@php
$colorClasses = match($color) {
    'emerald' => 'bg-emerald-50 text-emerald-600',
    'red' => 'bg-red-50 text-red-600',
    'blue' => 'bg-blue-50 text-blue-600',
    'amber' => 'bg-amber-50 text-amber-600',
    default => 'bg-gray-50 text-gray-600',
};
@endphp

<div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $value }}</p>
        </div>
        @if($icon)
        <div class="flex items-center justify-center w-12 h-12 rounded-lg {{ $colorClasses }}">
            {!! $icon !!}
        </div>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div class="mt-3">
            {{ $slot }}
        </div>
    @endif
</div>
