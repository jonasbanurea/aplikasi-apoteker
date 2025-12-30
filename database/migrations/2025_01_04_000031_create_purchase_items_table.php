<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_no', 100);
            $table->date('expired_date')->nullable();
            $table->integer('qty');
            $table->integer('bonus_qty')->default(0);
            $table->decimal('cost_price', 15, 2);
            $table->timestamps();

            $table->index(['product_id', 'batch_no', 'expired_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
