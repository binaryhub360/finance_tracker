<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add tax fields to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_tax_deductible')->default(false)->after('reference');
            $table->string('tax_category', 100)->nullable()->after('is_tax_deductible');
            $table->decimal('deduction_percentage', 5, 2)->default(100.00)->after('tax_category');
            $table->decimal('tax_deductible_amount', 15, 2)->default(0.00)->after('deduction_percentage');
            $table->text('tax_notes')->nullable()->after('tax_deductible_amount');

            $table->index('is_tax_deductible');
            $table->index('tax_category');
            $table->index('tax_deductible_amount');
        });

        // 2. Add tax default fields to expense_categories
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->boolean('is_tax_deductible_default')->default(false)->after('is_active');
            $table->string('default_tax_category', 100)->nullable()->after('is_tax_deductible_default');
            $table->decimal('default_deduction_percentage', 5, 2)->default(100.00)->after('default_tax_category');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['is_tax_deductible']);
            $table->dropIndex(['tax_category']);
            $table->dropIndex(['tax_deductible_amount']);

            $table->dropColumn([
                'is_tax_deductible',
                'tax_category',
                'deduction_percentage',
                'tax_deductible_amount',
                'tax_notes',
            ]);
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn([
                'is_tax_deductible_default',
                'default_tax_category',
                'default_deduction_percentage',
            ]);
        });
    }
};

