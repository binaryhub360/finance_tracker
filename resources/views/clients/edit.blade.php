<x-layouts.app :title="'Edit Client'">
    @section('page-title', 'Edit Client')

    <div class="max-w-2xl">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Edit Client Profile</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Update billing and contact information for {{ $client->name }}.</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 flex items-center justify-center font-bold text-emerald-700 text-sm">
                    {{ strtoupper(substr($client->name, 0, 2)) }}
                </div>
            </div>

            <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Contact / Client Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('name') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="company_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Company / Business Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $client->company_name) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('company_name') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Billing Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('email') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $client->phone) }}"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                        @error('phone') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Billing Address</label>
                    <textarea name="address" id="address" rows="2"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">{{ old('address', $client->address) }}</textarea>
                    @error('address') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tax_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">VAT / Tax Identification Number</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $client->tax_number) }}"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    @error('tax_number') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Internal Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">{{ old('notes', $client->notes) }}</textarea>
                    @error('notes') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer select-none">
                        Active Account <span class="block text-[11px] font-normal text-slate-400">Enable this client for new invoices</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Update Client
                    </button>
                    <a href="{{ route('clients.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
