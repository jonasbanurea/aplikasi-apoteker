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
        Schema::table('products', function (Blueprint $table) {
            // Unit satuan untuk penjualan eceran
            $table->string('unit_kemasan', 50)->nullable()->after('satuan')->comment('Unit kemasan: BOX, STRIP, BOTOL');
            $table->string('unit_terkecil', 50)->nullable()->after('unit_kemasan')->comment('Unit terkecil: KAPSUL, TABLET, ML');
            $table->unsignedInteger('isi_per_kemasan')->nullable()->after('unit_terkecil')->comment('Jumlah unit terkecil per kemasan');
            $table->boolean('jual_eceran')->default(false)->after('isi_per_kemasan')->comment('Bisa dijual eceran (per unit terkecil)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit_kemasan', 'unit_terkecil', 'isi_per_kemasan', 'jual_eceran']);
        });
    }
};
