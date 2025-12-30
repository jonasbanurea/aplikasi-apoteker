<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no', 100);
            $table->date('date');
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->enum('status', ['POSTED', 'CONSIGNMENT'])->default('POSTED');
            $table->boolean('is_consignment')->default(false);
            $table->timestamps();

            $table->unique(['supplier_id', 'invoice_no']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
