<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_no', 100);
            $table->date('expired_date')->nullable();
            $table->integer('qty_on_hand')->default(0);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->date('received_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'batch_no']);
            $table->index(['product_id', 'expired_date']); // FEFO friendly
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
