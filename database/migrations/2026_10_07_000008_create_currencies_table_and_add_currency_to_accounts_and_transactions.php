<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Currencies & Exchange Rates table
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name', 100);
            $table->string('symbol', 10);
            $table->decimal('exchange_rate', 18, 6)->default(1.000000);
            $table->boolean('is_base')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('source', 50)->default('system');
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('is_base');
            $table->index('is_active');
        });

        // 2. Add currency column to accounts table
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('currency', 10)->default('BDT')->after('type');
        });

        // 3. Add currency and conversion tracking to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('currency', 10)->default('BDT')->after('amount');
            $table->decimal('exchange_rate', 18, 6)->default(1.000000)->after('currency');
            $table->decimal('base_amount', 15, 2)->nullable()->after('exchange_rate');
        });

        // Backfill existing transactions so base_amount = amount
        DB::table('transactions')->whereNull('base_amount')->update([
            'currency' => 'BDT',
            'exchange_rate' => 1.000000,
            'base_amount' => DB::raw('amount'),
        ]);

        // Seed initial essential world currencies
        $now = now();
        $initialCurrencies = [
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳', 'exchange_rate' => 1.000000, 'is_base' => true, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 122.500000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 132.800000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 156.400000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 1.450000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'AED', 'exchange_rate' => 33.350000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'SAR', 'exchange_rate' => 32.650000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'CA$', 'exchange_rate' => 88.500000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'AU$', 'exchange_rate' => 78.900000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'exchange_rate' => 92.800000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'exchange_rate' => 28.100000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 0.810000, 'is_base' => false, 'is_active' => true, 'source' => 'system', 'last_updated_at' => $now, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('currencies')->insert($initialCurrencies);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate', 'base_amount']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::dropIfExists('currencies');
    }
};
