@props(['title' => '', 'value' => '', 'color' => 'gray', 'icon' => null, 'badge' => null])

@php
$theme = match($color) {
    'emerald' => [
        'bg' => 'bg-gradient-to-br from-emerald-500 to-teal-600',
        'glow' => 'shadow-lg shadow-emerald-500/25',
        'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
        'top' => 'from-emerald-500/20 via-transparent to-transparent',
    ],
    'red' => [
        'bg' => 'bg-gradient-to-br from-rose-500 to-red-600',
        'glow' => 'shadow-lg shadow-rose-500/25',
        'badge' => 'bg-rose-50 text-rose-700 border-rose-200/60',
        'top' => 'from-rose-500/20 via-transparent to-transparent',
    ],
    'blue' => [
        'bg' => 'bg-gradient-to-br from-blue-500 to-indigo-600',
        'glow' => 'shadow-lg shadow-blue-500/25',
        'badge' => 'bg-blue-50 text-blue-700 border-blue-200/60',
        'top' => 'from-blue-500/20 via-transparent to-transparent',
    ],
    'amber' => [
        'bg' => 'bg-gradient-to-br from-amber-500 to-orange-600',
        'glow' => 'shadow-lg shadow-amber-500/25',
        'badge' => 'bg-amber-50 text-amber-700 border-amber-200/60',
        'top' => 'from-amber-500/20 via-transparent to-transparent',
    ],
    default => [
        'bg' => 'bg-gradient-to-br from-slate-600 to-slate-800',
        'glow' => 'shadow-lg shadow-slate-500/20',
        'badge' => 'bg-slate-100 text-slate-700 border-slate-200',
        'top' => 'from-slate-400/20 via-transparent to-transparent',
    ],
};
@endphp

<div class="relative overflow-hidden bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 p-5 card-hover group">
    {{-- Top accent glow line --}}
    <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r {{ $theme['top'] }}"></div>

    <div class="flex items-start justify-between gap-4">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $title }}</p>
            <p class="mt-2 text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight font-tabular truncate">
                {{ $value }}
            </p>
        </div>
        @if($icon)
        <div class="flex items-center justify-center w-12 h-12 rounded-xl {{ $theme['bg'] }} text-white {{ $theme['glow'] }} shrink-0 transition-transform duration-300 group-hover:scale-105">
            {!! $icon !!}
        </div>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div class="mt-3.5 pt-3 border-t border-slate-100">
            {{ $slot }}
        </div>
    @endif
</div>
