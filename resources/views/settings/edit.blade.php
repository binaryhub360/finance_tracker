<x-layouts.app :title="'Settings'">
    @section('page-title', 'Settings')

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="currency_code" class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                        <input type="text" name="currency_code" id="currency_code" value="{{ old('currency_code', $settings['currency_code']) }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            placeholder="e.g. BDT, USD, EUR">
                        @error('currency_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                        <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            placeholder="e.g. ৳, $, €">
                        @error('currency_symbol') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                        <input type="text" name="timezone" id="timezone" value="{{ old('timezone', $settings['timezone']) }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            placeholder="e.g. Asia/Dhaka">
                        @error('timezone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-1">Date Format</label>
                        <select name="date_format" id="date_format" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @foreach(['d M Y' => 'DD Mon YYYY (23 Sep 2026)', 'Y-m-d' => 'YYYY-MM-DD (2026-09-23)', 'd/m/Y' => 'DD/MM/YYYY (23/09/2026)', 'm/d/Y' => 'MM/DD/YYYY (09/23/2026)', 'd-m-Y' => 'DD-MM-YYYY (23-09-2026)'] as $format => $label)
                                <option value="{{ $format }}" {{ old('date_format', $settings['date_format']) == $format ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('date_format') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
