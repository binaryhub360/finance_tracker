<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create import_batches table
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('filename', 255);
            $table->string('file_type', 50)->default('csv');
            $table->string('status', 30)->default('completed'); // completed, rolled_back, failed
            $table->integer('total_rows')->default(0);
            $table->integer('imported_count')->default(0);
            $table->integer('skipped_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->text('error_log')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('account_id');
        });

        // 2. Add import_batch_id to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('import_batch_id')->nullable()->after('goal_id')->constrained('import_batches')->nullOnDelete();
            $table->index('import_batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['import_batch_id']);
            $table->dropIndex(['import_batch_id']);
            $table->dropColumn('import_batch_id');
        });

        Schema::dropIfExists('import_batches');
    }
};
