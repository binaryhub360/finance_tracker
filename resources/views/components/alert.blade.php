@props(['type' => 'success', 'message'])

@php
$styles = match($type) {
    'success' => [
        'box' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-900',
        'badge' => 'bg-emerald-500/20 text-emerald-600',
        'icon' => 'check',
    ],
    'error' => [
        'box' => 'bg-rose-500/10 border-rose-500/20 text-rose-900',
        'badge' => 'bg-rose-500/20 text-rose-600',
        'icon' => 'exclamation',
    ],
    'warning' => [
        'box' => 'bg-amber-500/10 border-amber-500/20 text-amber-900',
        'badge' => 'bg-amber-500/20 text-amber-600',
        'icon' => 'exclamation',
    ],
    'info' => [
        'box' => 'bg-blue-500/10 border-blue-500/20 text-blue-900',
        'badge' => 'bg-blue-500/20 text-blue-600',
        'icon' => 'info',
    ],
    default => [
        'box' => 'bg-slate-500/10 border-slate-500/20 text-slate-800',
        'badge' => 'bg-slate-500/20 text-slate-600',
        'icon' => 'info',
    ],
};
@endphp

<div class="mb-6 animate-fade-in">
    <div class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl border backdrop-blur-md shadow-sm transition-all duration-200 {{ $styles['box'] }}">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $styles['badge'] }}">
            @if($styles['icon'] === 'check')
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            @elseif($styles['icon'] === 'exclamation')
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            @else
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            @endif
        </div>
        <p class="text-sm font-medium flex-1 tracking-tight">{{ $message }}</p>
        <button type="button" onclick="this.closest('.animate-fade-in').remove()" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-black/5 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
