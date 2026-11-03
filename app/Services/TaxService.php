<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TaxService
{
    /**
     * Standard Tax Deduction Categories mapped to IRS / Global CPA standards.
     */
    public const CATEGORIES = [
        'software_subscriptions' => [
            'code' => 'software_subscriptions',
            'name' => 'Software & Cloud Subscriptions',
            'tax_schedule' => 'Schedule C, Line 27a',
            'default_percentage' => 100.0,
            'color' => 'indigo',
            'description' => 'SaaS tools, cloud infrastructure, domain names, dev subscriptions',
        ],
        'office_supplies' => [
            'code' => 'office_supplies',
            'name' => 'Office Supplies & Materials',
            'tax_schedule' => 'Schedule C, Line 18',
            'default_percentage' => 100.0,
            'color' => 'blue',
            'description' => 'Stationery, desk equipment, paper, printing consumables',
        ],
        'equipment_hardware' => [
            'code' => 'equipment_hardware',
            'name' => 'Equipment & Hardware',
            'tax_schedule' => 'Schedule C, Line 13 (Sec 179)',
            'default_percentage' => 100.0,
            'color' => 'cyan',
            'description' => 'Laptops, monitors, smartphones, audio/video gear under capitalization limit',
        ],
        'professional_services' => [
            'code' => 'professional_services',
            'name' => 'Legal & Professional Services',
            'tax_schedule' => 'Schedule C, Line 17',
            'default_percentage' => 100.0,
            'color' => 'purple',
            'description' => 'Accountants, legal fees, consultants, external contractors',
        ],
        'advertising_marketing' => [
            'code' => 'advertising_marketing',
            'name' => 'Advertising & Marketing',
            'tax_schedule' => 'Schedule C, Line 8',
            'default_percentage' => 100.0,
            'color' => 'amber',
            'description' => 'Digital ad campaigns, sponsorships, promotional assets, branding',
        ],
        'travel_lodging' => [
            'code' => 'travel_lodging',
            'name' => 'Business Travel & Lodging',
            'tax_schedule' => 'Schedule C, Line 24a',
            'default_percentage' => 100.0,
            'color' => 'sky',
            'description' => 'Flights, trains, hotels, and vehicle rentals for business trips',
        ],
        'meals_entertainment' => [
            'code' => 'meals_entertainment',
            'name' => 'Meals & Client Entertainment (50%)',
            'tax_schedule' => 'Schedule C, Line 24b (50% Rule)',
            'default_percentage' => 50.0,
            'color' => 'orange',
            'description' => 'Client dinners, business meeting lunches (subject to statutory 50% limit)',
        ],
        'home_office_facilities' => [
            'code' => 'home_office_facilities',
            'name' => 'Home Office & Facilities',
            'tax_schedule' => 'Schedule C, Line 30',
            'default_percentage' => 100.0,
            'color' => 'emerald',
            'description' => 'Co-working desks, studio rent, dedicated home office allocation',
        ],
        'utilities_internet' => [
            'code' => 'utilities_internet',
            'name' => 'Internet, Mobile & Utilities',
            'tax_schedule' => 'Schedule C, Line 25',
            'default_percentage' => 100.0,
            'color' => 'teal',
            'description' => 'Broadband, cellular bills, electricity allocated for work',
        ],
        'education_training' => [
            'code' => 'education_training',
            'name' => 'Education, Courses & Books',
            'tax_schedule' => 'Schedule C, Line 27a',
            'default_percentage' => 100.0,
            'color' => 'violet',
            'description' => 'Professional certifications, technical books, workshops, conferences',
        ],
        'vehicle_transportation' => [
            'code' => 'vehicle_transportation',
            'name' => 'Vehicle, Fuel & Parking',
            'tax_schedule' => 'Schedule C, Line 9',
            'default_percentage' => 100.0,
            'color' => 'slate',
            'description' => 'Business ride-hailing (Uber/rides), parking fees, tolls, business mileage',
        ],
        'charity_donations' => [
            'code' => 'charity_donations',
            'name' => 'Charitable Contributions',
            'tax_schedule' => 'Schedule A, Itemized',
            'default_percentage' => 100.0,
            'color' => 'rose',
            'description' => 'Registered charity donations, institutional contributions',
        ],
        'healthcare_medical' => [
            'code' => 'healthcare_medical',
            'name' => 'Medical & Healthcare Deductions',
            'tax_schedule' => 'Schedule A / Health Ins.',
            'default_percentage' => 100.0,
            'color' => 'red',
            'description' => 'Self-employed health insurance premiums, eligible medical costs',
        ],
        'other_deductible' => [
            'code' => 'other_deductible',
            'name' => 'Other Deductible Business Outlays',
            'tax_schedule' => 'Schedule C, Line 27a',
            'default_percentage' => 100.0,
            'color' => 'slate',
            'description' => 'Any other ordinary and necessary business expenses',
        ],
    ];

    /**
     * Get a specific tax category definition.
     */
    public static function getCategory(?string $code): ?array
    {
        if (!$code) {
            return null;
        }

        return self::CATEGORIES[$code] ?? [
            'code' => $code,
            'name' => ucwords(str_replace('_', ' ', $code)),
            'tax_schedule' => 'General Deduction',
            'default_percentage' => 100.0,
            'color' => 'slate',
            'description' => '',
        ];
    }

    /**
     * Compute fiscal year date boundaries.
     * Supports both calendar year (Jan 1 - Dec 31) and off-calendar fiscal years (e.g. July 1 - June 30).
     */
    public static function getFiscalYearDates(?int $fiscalYear = null, ?int $startMonth = null): array
    {
        $startMonth = $startMonth ?? (int) Setting::get('tax_fiscal_year_start_month', 1);
        $fiscalYear = $fiscalYear ?? self::getCurrentFiscalYear($startMonth);

        if ($startMonth === 1) {
            $startDate = Carbon::createFromDate($fiscalYear, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($fiscalYear, 12, 31)->endOfDay();
            $label = "FY {$fiscalYear} (Jan {$fiscalYear} – Dec {$fiscalYear})";
        } else {
            // For example, startMonth = 7 (July) of previous calendar year through June 30 of fiscal year
            $startDate = Carbon::createFromDate($fiscalYear - 1, $startMonth, 1)->startOfDay();
            $endDate = Carbon::createFromDate($fiscalYear, $startMonth - 1, 1)->endOfMonth()->endOfDay();
            $startMonthName = $startDate->format('M');
            $endMonthName = $endDate->format('M');
            $label = "FY {$fiscalYear} ({$startMonthName} " . ($fiscalYear - 1) . " – {$endMonthName} {$fiscalYear})";
        }

        return [
            'year' => $fiscalYear,
            'start' => $startDate,
            'end' => $endDate,
            'start_month' => $startMonth,
            'label' => $label,
        ];
    }

    /**
     * Determine the current active fiscal year.
     */
    public static function getCurrentFiscalYear(?int $startMonth = null): int
    {
        $startMonth = $startMonth ?? (int) Setting::get('tax_fiscal_year_start_month', 1);
        $now = Carbon::now();

        if ($startMonth === 1) {
            return $now->year;
        }

        return $now->month >= $startMonth ? $now->year + 1 : $now->year;
    }

    /**
     * Discover all fiscal years that have transaction activity for the user.
     */
    public static function getAvailableFiscalYears(int $userId, ?int $startMonth = null): array
    {
        $startMonth = $startMonth ?? (int) Setting::get('tax_fiscal_year_start_month', 1);

        $minDate = Transaction::where('user_id', $userId)->min('date');
        $maxDate = Transaction::where('user_id', $userId)->max('date');

        $currentYear = self::getCurrentFiscalYear($startMonth);

        if (!$minDate || !$maxDate) {
            return [$currentYear];
        }

        $minCarbon = Carbon::parse($minDate);
        $maxCarbon = Carbon::parse($maxDate);

        $startYear = $startMonth === 1
            ? $minCarbon->year
            : ($minCarbon->month >= $startMonth ? $minCarbon->year + 1 : $minCarbon->year);

        $endYear = $startMonth === 1
            ? $maxCarbon->year
            : ($maxCarbon->month >= $startMonth ? $maxCarbon->year + 1 : $maxCarbon->year);

        $years = range(max($startYear, $currentYear - 4), max($endYear, $currentYear));
        rsort($years);

        return array_unique($years);
    }

    /**
     * Calculate financial and tax summary for a given date range.
     */
    public function getTaxSummary(int $userId, Carbon $start, Carbon $end, ?float $taxRate = null): array
    {
        $taxRate = $taxRate ?? (float) Setting::get('tax_estimated_rate', 25.0);

        // Gross Income in system base amount
        $totalIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$start, $end])
            ->sum('base_amount');

        // Total Expenses in system base amount
        $totalExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$start, $end])
            ->sum('base_amount');

        // Total Deductible Amount claimed
        $deductibleTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('is_tax_deductible', true)
            ->whereBetween('date', [$start, $end]);

        $totalDeductible = (float) (clone $deductibleTransactions)->sum('tax_deductible_amount');
        $deductibleCount = (clone $deductibleTransactions)->count();

        // Non-deductible expenses
        $nonDeductible = max(0, $totalExpense - $totalDeductible);

        // Deductibility ratio (% of expenses claimed as deductible)
        $deductibleRatio = $totalExpense > 0 ? round(($totalDeductible / $totalExpense) * 100, 1) : 0;

        // Estimated Net Taxable Business Income
        $taxableIncome = max(0, $totalIncome - $totalDeductible);

        // Estimated Tax Savings / Shield = Deductible * Tax Rate
        $estimatedTaxSavings = round($totalDeductible * ($taxRate / 100), 2);

        // Unreviewed / unclaimed expenses count
        $unreviewedCount = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('is_tax_deductible', false)
            ->whereBetween('date', [$start, $end])
            ->count();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'total_deductible' => $totalDeductible,
            'non_deductible' => $nonDeductible,
            'deductible_ratio' => $deductibleRatio,
            'deductible_count' => $deductibleCount,
            'taxable_income' => $taxableIncome,
            'tax_rate' => $taxRate,
            'estimated_tax_savings' => $estimatedTaxSavings,
            'unreviewed_count' => $unreviewedCount,
        ];
    }

    /**
     * Get Schedule of Deductions grouped by Tax Category.
     */
    public function getCategoryBreakdown(int $userId, Carbon $start, Carbon $end): Collection
    {
        $breakdown = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('is_tax_deductible', true)
            ->whereBetween('date', [$start, $end])
            ->selectRaw('tax_category, COUNT(*) as count, SUM(base_amount) as total_gross, SUM(tax_deductible_amount) as total_deductible, AVG(deduction_percentage) as avg_percentage')
            ->groupBy('tax_category')
            ->orderByDesc('total_deductible')
            ->get();

        $grandTotal = $breakdown->sum('total_deductible');

        return $breakdown->map(function ($row) use ($grandTotal) {
            $catDef = self::getCategory($row->tax_category);
            $share = $grandTotal > 0 ? round(($row->total_deductible / $grandTotal) * 100, 1) : 0;

            return (object) [
                'code' => $row->tax_category ?? 'other_deductible',
                'name' => $catDef['name'] ?? 'Other Deductible',
                'tax_schedule' => $catDef['tax_schedule'] ?? 'Schedule C, Line 27a',
                'color' => $catDef['color'] ?? 'slate',
                'count' => (int) $row->count,
                'total_gross' => (float) $row->total_gross,
                'total_deductible' => (float) $row->total_deductible,
                'avg_percentage' => round((float) $row->avg_percentage, 1),
                'percentage_of_total' => $share,
            ];
        });
    }

    /**
     * Get monthly breakdown of deductible expenses across the fiscal year.
     */
    public function getMonthlyBreakdown(int $userId, Carbon $start, Carbon $end): array
    {
        $transactions = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('is_tax_deductible', true)
            ->whereBetween('date', [$start, $end])
            ->get();

        $cursor = $start->copy()->startOfMonth();
        $monthly = [];

        while ($cursor->lte($end)) {
            $monthKey = $cursor->format('Y-m');
            $monthLabel = $cursor->format('M Y');

            $monthTxns = $transactions->filter(function ($t) use ($monthKey) {
                return Carbon::parse($t->date)->format('Y-m') === $monthKey;
            });

            $monthly[] = [
                'month_key' => $monthKey,
                'label' => $monthLabel,
                'count' => $monthTxns->count(),
                'deductible_amount' => (float) $monthTxns->sum('tax_deductible_amount'),
                'gross_amount' => (float) $monthTxns->sum('base_amount'),
            ];

            $cursor->addMonth();
        }

        return $monthly;
    }
}
