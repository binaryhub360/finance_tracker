@props(['message' => 'No data found.', 'action' => null, 'actionText' => 'Create'])

<div class="flex flex-col items-center justify-center py-14 px-4 text-center">
    <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-100 to-slate-50 border border-slate-200/80 flex items-center justify-center mb-4 shadow-sm group">
        <div class="absolute inset-0 rounded-2xl bg-emerald-500/10 blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <svg class="w-8 h-8 text-slate-400 group-hover:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
    </div>
    <h3 class="text-sm font-semibold text-slate-800 tracking-tight mb-1">{{ $message }}</h3>
    <p class="text-xs text-slate-400 max-w-sm mb-5">There are no records matching your criteria. Get started by adding a new entry.</p>
    @if($action)
        <a href="{{ $action }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-sm shadow-emerald-600/20 hover:shadow-md hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ $actionText }}
        </a>
    @endif
</div>
