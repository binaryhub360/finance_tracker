<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Deductions Schedule & Accountant Pack — FY {{ $dates['year'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 sm:py-12 px-4 sm:px-6">

    {{-- Floating Print Control Bar (Hidden when printing) --}}
    <div class="no-print max-w-5xl mx-auto mb-6 flex items-center justify-between bg-white/90 backdrop-blur-xl border border-slate-200/90 rounded-2xl p-4 shadow-xl shadow-slate-900/5">
        <div class="flex items-center gap-3">
            <span class="p-2 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.077-.4-2.222-.4-3.418 0-4.97 3.358-9 7.5-9s7.5 4.03 7.5 9c0 1.196-.16 2.341-.4 3.418M6.72 13.829C7.45 15.655 9.475 17 12 17c2.525 0 4.55-1.345 5.28-3.171M6.72 13.829A11.953 11.953 0 0112 13.5c1.884 0 3.655.437 5.28 1.218M3 20.25h18" />
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-900">Accountant & CPA Tax Season Pack</h2>
                <p class="text-xs text-slate-500">Official statement of claimed deductions for FY {{ $dates['year'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('taxes.index', ['year' => $dates['year']]) }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                Back to Tax Center
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition shadow-md active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.077-.4-2.222-.4-3.418 0-4.97 3.358-9 7.5-9s7.5 4.03 7.5 9c0 1.196-.16 2.341-.4 3.418M6.72 13.829C7.45 15.655 9.475 17 12 17c2.525 0 4.55-1.345 5.28-3.171M6.72 13.829A11.953 11.953 0 0112 13.5c1.884 0 3.655.437 5.28 1.218M3 20.25h18" />
                </svg>
                Print / Save as PDF
            </button>
        </div>
    </div>

    {{-- Official Statement Document Sheet --}}
    <div class="max-w-5xl mx-auto bg-white border border-slate-200 rounded-3xl p-8 sm:p-12 shadow-xl shadow-slate-900/5 text-slate-900 print:border-none print:shadow-none print:p-0">

        {{-- Statement Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-8 border-b-2 border-slate-900">
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 mb-2 border border-amber-200">
                    TAX SEASON COMPANION & DEDUCTION SCHEDULE
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Statement of Claimed Deductions</h1>
                <p class="text-xs text-slate-500 mt-1">Prepared in accordance with {{ $taxSettings['country'] }} statutory guidelines</p>
            </div>
            <div class="text-left sm:text-right text-xs space-y-1">
                <div class="font-extrabold text-base text-slate-900">{{ $taxSettings['entity_name'] }}</div>
                <div class="text-slate-600 font-mono font-medium">TIN / EIN: <strong class="text-slate-900">{{ $taxSettings['tax_id'] ?: 'Unspecified' }}</strong></div>
                <div class="text-slate-500">Reporting Period: <strong class="text-slate-800">{{ $dates['start']->format('M d, Y') }} – {{ $dates['end']->format('M d, Y') }}</strong></div>
                <div class="text-slate-500">Fiscal Period: <strong class="text-slate-800">FY {{ $dates['year'] }}</strong></div>
                <div class="text-slate-400 text-[11px]">Date Generated: {{ now()->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        {{-- Executive Financial Summary --}}
        <div class="my-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">I. Executive Summary</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase">Gross Revenue</div>
                    <div class="text-base sm:text-lg font-black text-slate-900 font-tabular">{{ format_currency($taxSummary['total_income']) }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase">Total Outlays</div>
                    <div class="text-base sm:text-lg font-black text-slate-900 font-tabular">{{ format_currency($taxSummary['total_expense']) }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-amber-700 uppercase">Total Deductible</div>
                    <div class="text-base sm:text-lg font-black text-amber-700 font-tabular">{{ format_currency($taxSummary['total_deductible']) }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-emerald-700 uppercase">Est. Tax Shield ({{ $taxSummary['tax_rate'] }}%)</div>
                    <div class="text-base sm:text-lg font-black text-emerald-700 font-tabular">{{ format_currency($taxSummary['estimated_tax_savings']) }}</div>
                </div>
            </div>
        </div>

        {{-- Schedule of Deductions Table --}}
        <div class="my-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">II. Schedule of Deductions by Statutory Category</h2>
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100/80 text-[11px] font-bold text-slate-600 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Category Name</th>
                            <th class="px-4 py-3">Schedule / Statutory Ref</th>
                            <th class="px-3 py-3 text-center">Items</th>
                            <th class="px-4 py-3 text-right">Gross Outlay</th>
                            <th class="px-3 py-3 text-right">Avg Rate</th>
                            <th class="px-4 py-3 text-right font-black">Claimed Deductible</th>
                            <th class="px-4 py-3 text-right">% Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categoryBreakdown as $row)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-2.5 font-bold text-slate-900">{{ $row->name }}</td>
                                <td class="px-4 py-2.5 text-slate-500 font-mono text-[11px]">{{ $row->tax_schedule }}</td>
                                <td class="px-3 py-2.5 text-center font-semibold text-slate-700">{{ $row->count }}</td>
                                <td class="px-4 py-2.5 text-right font-tabular text-slate-600">{{ format_currency($row->total_gross) }}</td>
                                <td class="px-3 py-2.5 text-right font-bold text-slate-700">{{ $row->avg_percentage }}%</td>
                                <td class="px-4 py-2.5 text-right font-black text-slate-900 font-tabular">{{ format_currency($row->total_deductible) }}</td>
                                <td class="px-4 py-2.5 text-right font-bold text-slate-600 font-tabular">{{ $row->percentage_of_total }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-400">No deductible expenses recorded in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-300">
                        <tr>
                            <td class="px-4 py-3 text-slate-900 uppercase tracking-wider" colspan="2">Total Deductions Claimed:</td>
                            <td class="px-3 py-3 text-center text-slate-900">{{ $taxSummary['deductible_count'] }}</td>
                            <td class="px-4 py-3 text-right font-tabular text-slate-900">{{ format_currency($taxSummary['total_expense']) }}</td>
                            <td class="px-3 py-3 text-right text-slate-900">-</td>
                            <td class="px-4 py-3 text-right font-black text-amber-800 font-tabular text-sm">{{ format_currency($taxSummary['total_deductible']) }}</td>
                            <td class="px-4 py-3 text-right text-slate-900">100.0%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Itemized Transaction Ledger --}}
        <div class="my-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">III. Itemized Ledger of Deductible Transactions</h2>
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-[11px] border-collapse">
                    <thead class="bg-slate-100/80 font-bold text-slate-600 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2.5">Date</th>
                            <th class="px-3 py-2.5">Ref #</th>
                            <th class="px-3 py-2.5">Schedule</th>
                            <th class="px-3 py-2.5">Account</th>
                            <th class="px-3 py-2.5 text-right">Native Amount</th>
                            <th class="px-2 py-2.5 text-center">%</th>
                            <th class="px-3 py-2.5 text-right font-black">Deductible Claimed</th>
                            <th class="px-3 py-2.5">Purpose / Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $txn)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap font-mono text-slate-700">{{ $txn->date->format('Y-m-d') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap font-mono text-slate-500">{{ $txn->reference ?: 'TXN-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-3 py-2 font-semibold text-slate-900">{{ $txn->tax_category_label ?? 'General' }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $txn->account?->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-right font-tabular text-slate-700">
                                    {{ format_currency($txn->amount, $txn->currency) }}
                                </td>
                                <td class="px-2 py-2 text-center font-bold text-slate-700">{{ (float) $txn->deduction_percentage }}%</td>
                                <td class="px-3 py-2 text-right font-black text-slate-900 font-tabular">{{ format_currency($txn->tax_deductible_amount) }}</td>
                                <td class="px-3 py-2 text-slate-600 max-w-xs truncate">{{ $txn->tax_notes ?: $txn->description ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-slate-400">No itemized deductible transactions.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Signatures & Certification Block --}}
        <div class="mt-12 pt-8 border-t-2 border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs text-slate-600">
            <div>
                <p class="font-bold text-slate-900 uppercase tracking-wider mb-1">Taxpayer / Corporate Officer Declaration</p>
                <p class="text-[11px] text-slate-500 mb-6">I certify that the expenses itemized herein represent ordinary, necessary, and bona fide business deductions claimed during the reported fiscal period.</p>
                <div class="border-b border-slate-400 w-48 mb-1"></div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Authorized Signature &bull; Date</p>
            </div>
            <div>
                <p class="font-bold text-slate-900 uppercase tracking-wider mb-1">CPA / Enrolled Agent / Tax Preparer Review</p>
                <p class="text-[11px] text-slate-500 mb-6">Reviewed and compiled from records and supporting transaction documentation provided by the taxpayer.</p>
                <div class="border-b border-slate-400 w-48 mb-1"></div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Tax Preparer PTIN / Signature &bull; Date</p>
            </div>
        </div>

    </div>

</body>
</html>
