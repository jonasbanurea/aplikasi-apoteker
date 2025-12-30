<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash_actual', 15, 2)->default(0);
            $table->decimal('cash_expected', 15, 2)->default(0);
            $table->decimal('discrepancy', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'opened_at']);
            $table->index(['closed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
