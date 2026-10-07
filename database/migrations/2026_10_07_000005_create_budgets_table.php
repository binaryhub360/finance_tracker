<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('period', ['monthly', 'yearly', 'custom'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('alert_threshold')->default(80); // percentage e.g. 80%
            $table->boolean('notify_overrun')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'expense_category_id', 'start_date', 'end_date'], 'user_cat_dates_unique');
            $table->index('user_id');
            $table->index('expense_category_id');
            $table->index(['user_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
