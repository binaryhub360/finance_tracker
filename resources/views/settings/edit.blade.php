<x-layouts.app :title="'Settings'">
    @section('page-title', 'System Settings')

    <div class="max-w-2xl">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
            <div class="mb-6">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Localization & Preferences</h2>
                <p class="text-xs text-slate-500 mt-0.5">Configure your preferred currency, timezone, and calendar formats.</p>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="currency_code" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Currency Code</label>
                        <input type="text" name="currency_code" id="currency_code" value="{{ old('currency_code', $settings['currency_code']) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-semibold tracking-wider uppercase focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono"
                            placeholder="e.g. BDT, USD, EUR">
                        @error('currency_code') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="currency_symbol" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Currency Symbol</label>
                        <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-900 text-sm font-bold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                            placeholder="e.g. ৳, $, €">
                        @error('currency_symbol') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="timezone" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Timezone</label>
                        <input type="text" name="timezone" id="timezone" value="{{ old('timezone', $settings['timezone']) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono"
                            placeholder="e.g. Asia/Dhaka">
                        @error('timezone') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="date_format" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Date Display Format</label>
                        <select name="date_format" id="date_format" class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                            @foreach(['d M Y' => 'DD Mon YYYY (23 Sep 2026)', 'Y-m-d' => 'YYYY-MM-DD (2026-09-23)', 'd/m/Y' => 'DD/MM/YYYY (23/09/2026)', 'm/d/Y' => 'MM/DD/YYYY (09/23/2026)', 'd-m-Y' => 'DD-MM-YYYY (23-09-2026)'] as $format => $label)
                                <option value="{{ $format }}" {{ old('date_format', $settings['date_format']) == $format ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('date_format') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
