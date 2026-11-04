<x-layouts.app :title="'Audit Trails & Activity Logs'">
    @section('page-title', 'Audit Trails & Activity Logs')

    <div class="space-y-8" x-data="{
        showDiffModal: false,
        activeLog: null,
        modalTab: 'diff',
        openDiff(log) {
            this.activeLog = log;
            this.modalTab = 'diff';
            this.showDiffModal = true;
        }
    }">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-500/15 via-teal-500/10 to-transparent text-emerald-600 border border-emerald-500/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Audit Trails & Activity Logs</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Enterprise Compliance
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Immutable audit trail of record additions, field updates, deletions, and system actions</p>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('audit-logs.export.csv', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200/90 hover:bg-slate-50 hover:border-slate-300 shadow-xs transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export Audit Trail (CSV)</span>
                </a>
            </div>
        </div>

        {{-- 4 KPI Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Events --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs group hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Events Logged</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_events']) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-1">Full system lifetime history</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center border border-blue-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
            </div>

            {{-- Events Today --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs group hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Activity Today</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['events_today']) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-1">Actions recorded today</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
            </div>

            {{-- Updates / Modifications --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs group hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Modifications</p>
                        <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['total_updates']) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-1">Field diffs tracked</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center border border-amber-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>
            </div>

            {{-- Deletions --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 border border-slate-200/70 shadow-xs group hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Deletions</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['total_deletions']) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-1">Removed records snapshot</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center border border-rose-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-red-500"></div>
            </div>
        </div>

        {{-- Filters & Search Toolbar --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
                {{-- Action Filter --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Action</label>
                    <select name="action" class="w-full text-xs font-medium rounded-xl border border-slate-200 px-3 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                        <option value="">All Actions</option>
                        @foreach ($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $act)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Entity Type Filter --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Entity Module</label>
                    <select name="entity_type" class="w-full text-xs font-medium rounded-xl border border-slate-200 px-3 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                        <option value="">All Entities</option>
                        @foreach ($entityTypes as $type)
                            <option value="{{ $type }}" {{ request('entity_type') === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full text-xs font-medium rounded-xl border border-slate-200 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full text-xs font-medium rounded-xl border border-slate-200 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                </div>

                {{-- Search query --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Search Description / IP</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search details or IP..."
                        class="w-full text-xs font-medium rounded-xl border border-slate-200 px-3 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                </div>

                {{-- Filter Actions --}}
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition-all text-center">
                        Filter
                    </button>
                    @if (request()->hasAny(['action', 'entity_type', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('audit-logs.index') }}"
                            class="px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-all"
                            title="Clear Filters">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Activity Log Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                            <th class="py-3.5 px-4">Log ID & Time</th>
                            <th class="py-3.5 px-4">User</th>
                            <th class="py-3.5 px-4">Action</th>
                            <th class="py-3.5 px-4">Entity</th>
                            <th class="py-3.5 px-4">Activity Description</th>
                            <th class="py-3.5 px-4">IP Address</th>
                            <th class="py-3.5 px-4 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse ($logs as $log)
                            @php
                                $badge = $log->action_badge;
                                $hasDiff = !empty($log->diff);
                                $hasPayload = !empty($log->old_values) || !empty($log->new_values);
                                $logJson = [
                                    'id' => $log->id,
                                    'action' => $log->action,
                                    'action_badge' => $badge,
                                    'entity_type' => $log->entity_type_name,
                                    'entity_label' => $log->entity_label ?? 'N/A',
                                    'description' => $log->description,
                                    'user' => $log->user?->name ?? 'System Automated',
                                    'ip_address' => $log->ip_address ?? '127.0.0.1',
                                    'user_agent' => $log->user_agent,
                                    'created_at' => $log->created_at?->format('M d, Y h:i:s A'),
                                    'diff' => $log->diff,
                                    'old_values' => $log->old_values,
                                    'new_values' => $log->new_values,
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                {{-- Timestamp & ID --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-mono text-[11px] font-bold text-slate-900">
                                        #AUD-{{ str_pad($log->id, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5" title="{{ $log->created_at?->format('Y-m-d H:i:s') }}">
                                        {{ $log->created_at?->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- User --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 font-bold text-[10px] flex items-center justify-center">
                                            {{ substr($log->user?->name ?? 'S', 0, 1) }}
                                        </div>
                                        <span class="text-xs font-semibold text-slate-800">
                                            {{ $log->user?->name ?? 'System' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Action Badge --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        {{ $badge['label'] }}
                                    </span>
                                </td>

                                {{-- Entity Type & Label --}}
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200/80 uppercase">
                                            {{ $log->entity_type_name }}
                                        </span>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-900 mt-1 truncate max-w-xs" title="{{ $log->entity_label }}">
                                        {{ $log->entity_label ?? '—' }}
                                    </div>
                                </td>

                                {{-- Description --}}
                                <td class="py-3 px-4 max-w-md">
                                    <p class="text-xs text-slate-700 truncate" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </p>
                                </td>

                                {{-- IP Address --}}
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    {{ $log->ip_address ?? '—' }}
                                </td>

                                {{-- Details / Inspect --}}
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    @if ($hasDiff || $hasPayload)
                                        <button type="button"
                                            @click="openDiff({{ json_encode($logJson) }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100/80 border border-emerald-200/80 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>Inspect Diff</span>
                                        </button>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800">No activity logs recorded</h3>
                                    <p class="text-xs text-slate-400 mt-1">Actions on transactions, accounts, invoices, and budgets will be tracked here automatically.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination footer --}}
            @if ($logs->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        {{-- Alpine.js Diff / Payload Inspection Modal --}}
        <div x-show="showDiffModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div x-show="showDiffModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="showDiffModal = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Panel --}}
                <div x-show="showDiffModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200">

                    {{-- Modal Header --}}
                    <div class="px-6 py-5 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-emerald-400 border border-white/10">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black tracking-tight" x-text="activeLog ? ('Audit Event #' + activeLog.id + ' — ' + activeLog.entity_type) : ''"></h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"
                                        :class="activeLog?.action_badge?.class"
                                        x-text="activeLog?.action_badge?.label"></span>
                                </div>
                                <p class="text-xs text-slate-300 mt-0.5" x-text="activeLog?.entity_label"></p>
                            </div>
                        </div>

                        <button type="button" @click="showDiffModal = false" class="text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Event Metadata Strip --}}
                    <div class="px-6 py-3 bg-slate-50 border-b border-slate-200/80 flex flex-wrap items-center justify-between text-xs text-slate-600 gap-3">
                        <div class="flex items-center gap-4">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Performed By</span>
                                <span class="font-semibold text-slate-800" x-text="activeLog?.user"></span>
                            </div>
                            <div class="h-6 w-px bg-slate-200"></div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">IP Address</span>
                                <span class="font-mono text-slate-800" x-text="activeLog?.ip_address"></span>
                            </div>
                            <div class="h-6 w-px bg-slate-200"></div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Timestamp</span>
                                <span class="font-medium text-slate-800" x-text="activeLog?.created_at"></span>
                            </div>
                        </div>

                        {{-- Modal View Tabs --}}
                        <div class="flex items-center bg-slate-200/70 p-0.5 rounded-xl text-xs font-bold">
                            <button type="button" @click="modalTab = 'diff'"
                                :class="modalTab === 'diff' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1 rounded-lg transition-all">
                                Field Changes (Diff)
                            </button>
                            <button type="button" @click="modalTab = 'raw'"
                                :class="modalTab === 'raw' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1 rounded-lg transition-all">
                                Raw JSON Payload
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body Content --}}
                    <div class="p-6 max-h-[60vh] overflow-y-auto">
                        {{-- Tab 1: Field Diff Table --}}
                        <div x-show="modalTab === 'diff'">
                            <template x-if="activeLog && Object.keys(activeLog.diff || {}).length > 0">
                                <div class="rounded-xl border border-slate-200 overflow-hidden">
                                    <table class="w-full text-left text-xs">
                                        <thead>
                                            <tr class="bg-slate-100/75 border-b border-slate-200 text-[11px] font-bold uppercase text-slate-500">
                                                <th class="py-2.5 px-4 w-1/3">Field Name</th>
                                                <th class="py-2.5 px-4 w-1/3 text-rose-700 bg-rose-50/50">Previous Value</th>
                                                <th class="py-2.5 px-4 w-1/3 text-emerald-700 bg-emerald-50/50">New Updated Value</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <template x-for="(item, key) in activeLog.diff" :key="key">
                                                <tr class="hover:bg-slate-50/50">
                                                    <td class="py-2.5 px-4 font-bold text-slate-700" x-text="item.field"></td>
                                                    <td class="py-2.5 px-4 font-mono text-[11px] text-rose-700 bg-rose-50/30">
                                                        <span class="line-through opacity-75" x-text="item.old !== null && item.old !== '' ? item.old : 'null'"></span>
                                                    </td>
                                                    <td class="py-2.5 px-4 font-mono text-[11px] font-bold text-emerald-700 bg-emerald-50/30">
                                                        <span x-text="item.new !== null && item.new !== '' ? item.new : 'null'"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>

                            {{-- Creation / Snapshot if no diff --}}
                            <template x-if="activeLog && Object.keys(activeLog.diff || {}).length === 0">
                                <div>
                                    <template x-if="activeLog.action === 'created'">
                                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 mb-4">
                                            <div class="flex items-center gap-2 font-bold text-emerald-800 mb-1">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                                Initial Creation Record
                                            </div>
                                            This record was newly created. All initial attributes are stored in the state snapshot below.
                                        </div>
                                    </template>

                                    <template x-if="activeLog.action === 'deleted'">
                                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-900 mb-4">
                                            <div class="flex items-center gap-2 font-bold text-rose-800 mb-1">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79" />
                                                </svg>
                                                Permanently Deleted Entity
                                            </div>
                                            This record was removed. A pre-deletion state snapshot is recorded below for audit recovery.
                                        </div>
                                    </template>

                                    {{-- Attribute Snapshot Grid --}}
                                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                                        <table class="w-full text-left text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase text-slate-500">
                                                    <th class="py-2.5 px-4 w-1/3">Field</th>
                                                    <th class="py-2.5 px-4">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <template x-for="(val, field) in (activeLog.new_values || activeLog.old_values || {})" :key="field">
                                                    <tr class="hover:bg-slate-50/50">
                                                        <td class="py-2 px-4 font-bold text-slate-700" x-text="field"></td>
                                                        <td class="py-2 px-4 font-mono text-[11px] text-slate-900" x-text="typeof val === 'object' ? JSON.stringify(val) : val"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Tab 2: Raw JSON Payload --}}
                        <div x-show="modalTab === 'raw'">
                            <div class="space-y-4">
                                <div x-show="activeLog?.old_values">
                                    <p class="text-xs font-bold text-slate-700 mb-1">Old Values (Previous State)</p>
                                    <pre class="p-3 bg-slate-900 text-slate-100 rounded-xl text-[11px] font-mono overflow-x-auto max-h-48"
                                        x-text="JSON.stringify(activeLog?.old_values, null, 2)"></pre>
                                </div>

                                <div x-show="activeLog?.new_values">
                                    <p class="text-xs font-bold text-slate-700 mb-1">New Values (Captured State)</p>
                                    <pre class="p-3 bg-slate-900 text-slate-100 rounded-xl text-[11px] font-mono overflow-x-auto max-h-48"
                                        x-text="JSON.stringify(activeLog?.new_values, null, 2)"></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Cryptographically immutable audit event</span>
                        <button type="button" @click="showDiffModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-all">
                            Close Inspector
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

