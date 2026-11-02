<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('BDT');
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('color', 30)->default('emerald');
            $table->string('icon', 50)->default('vault');
            $table->enum('status', ['active', 'completed', 'paused', 'cancelled'])->default('active');
            $table->boolean('is_locked')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('target_date');
        });

        Schema::create('goal_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('goals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['deposit', 'withdraw'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('goal_id');
            $table->index('user_id');
            $table->index('date');
            $table->index('type');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('goal_id')
                ->nullable()
                ->after('recurring_transaction_id')
                ->constrained('goals')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('goal_id');
        });

        Schema::dropIfExists('goal_contributions');
        Schema::dropIfExists('goals');
    }
};
