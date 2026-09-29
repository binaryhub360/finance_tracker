@props(['action' => '', 'method' => 'GET', 'extraFields' => false])

<form method="{{ $method }}" action="{{ $action }}" class="flex flex-wrap items-end gap-3">
    <div>
        <label for="period" class="block text-xs font-medium text-gray-500 mb-1">Period</label>
        <select name="period" id="period" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" onchange="toggleCustomDates(this)">
            <option value="this_month" {{ request('period', 'this_month') == 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
            <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>This Week</option>
            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
            <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
            <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
            <option value="last_year" {{ request('period') == 'last_year' ? 'selected' : '' }}>Last Year</option>
            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
        </select>
    </div>

    <div id="custom-dates" class="{{ request('period') == 'custom' ? 'flex' : 'hidden' }} items-end gap-3">
        <div>
            <label for="from" class="block text-xs font-medium text-gray-500 mb-1">From</label>
            <input type="date" name="from" id="from" value="{{ request('from') }}" class="block rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label for="to" class="block text-xs font-medium text-gray-500 mb-1">To</label>
            <input type="date" name="to" id="to" value="{{ request('to') }}" class="block rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
    </div>

    {{ $slot }}

    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
        </svg>
        Filter
    </button>
</form>

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
