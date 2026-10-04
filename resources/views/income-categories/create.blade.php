<x-layouts.app :title="'Add Income Category'">
    @section('page-title', 'Add Income Category')

    <div class="max-w-2xl">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
            <div class="mb-6">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">New Income Category</h2>
                <p class="text-xs text-slate-500 mt-0.5">Organize and group your revenue and incoming fund sources.</p>
            </div>

            <form method="POST" action="{{ route('income-categories.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="e.g. Salary / Freelance / Investment Dividends">
                    @error('name') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white/70 text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                        placeholder="Optional description or classification notes">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                        class="h-4 w-4 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer select-none">
                        Active Category <span class="block text-[11px] font-normal text-slate-400">Available when recording income</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-200 active:scale-[0.98]">
                        Create Category
                    </button>
                    <a href="{{ route('income-categories.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
