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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('nama_dagang', 150);
            $table->string('nama_generik', 150)->nullable();
            $table->string('bentuk', 100);
            $table->string('kekuatan_dosis', 100);
            $table->string('satuan', 50);
            $table->enum('golongan', ['OTC', 'BEBAS_TERBATAS', 'RESEP', 'PSIKOTROPIKA', 'NARKOTIKA']);
            $table->boolean('wajib_resep')->default(false);
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->string('lokasi_rak', 100)->nullable();
            $table->unsignedInteger('minimal_stok')->default(0);
            $table->boolean('konsinyasi')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
