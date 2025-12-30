<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('stock_batches')->nullOnDelete();
            $table->integer('system_qty');
            $table->integer('physical_qty');
            $table->integer('diff_qty');
            $table->enum('reason', ['SELISIH_OPNAME', 'RUSAK', 'KADALUARSA', 'HILANG'])->default('SELISIH_OPNAME');
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('diff_value', 15, 2)->default(0);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['product_id', 'batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
