<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('opname_date');
            $table->enum('status', ['PENDING_APPROVAL', 'APPROVED', 'REJECTED'])->default('PENDING_APPROVAL');
            $table->boolean('requires_approval')->default(false);
            $table->decimal('approval_threshold_value', 15, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->integer('total_diff_qty')->default(0);
            $table->decimal('total_system_value', 15, 2)->default(0);
            $table->decimal('total_physical_value', 15, 2)->default(0);
            $table->decimal('total_diff_value', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamps();

            $table->index(['opname_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
