@props(['action' => '', 'method' => 'GET', 'extraFields' => false])

<div class="bg-white/80 backdrop-blur-md rounded-2xl border border-slate-200/80 p-3 shadow-sm">
    <form method="{{ $method }}" action="{{ $action }}" class="flex flex-wrap items-center gap-2.5">
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider pl-1">Period:</span>
            <div class="relative">
                <select name="period" id="period" class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer transition shadow-xs" onchange="toggleCustomDates(this)">
                    <option value="this_month" {{ request('period', 'this_month') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                    <option value="last_year" {{ request('period') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </div>
            </div>
        </div>

        <div id="custom-dates" class="{{ request('period') == 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-slate-500">From</span>
                <input type="date" name="from" id="from" value="{{ request('from') }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs">
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-slate-500">To</span>
                <input type="date" name="to" id="to" value="{{ request('to') }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs">
            </div>
        </div>

        {{ $slot }}

        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-emerald-500/20 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
            </svg>
            Apply
        </button>

        @if(request()->hasAny(['period', 'from', 'to']))
            <a href="{{ $action }}" class="text-xs font-medium text-slate-400 hover:text-slate-600 transition px-2">Reset</a>
        @endif
    </form>
</div>

<script>
function toggleCustomDates(select) {
    const customDates = document.getElementById('custom-dates');
    if (select.value === 'custom') {
        customDates.classList.remove('hidden');
        customDates.classList.add('flex');
    } else {
        customDates.classList.add('hidden');
        customDates.classList.remove('flex');
    }
}
</script>
