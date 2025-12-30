<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'nama_dagang',
        'nama_generik',
        'bentuk',
        'kekuatan_dosis',
        'satuan',
        'golongan',
        'wajib_resep',
        'harga_beli',
        'harga_jual',
        'lokasi_rak',
        'minimal_stok',
        'konsinyasi',
    ];

    protected $casts = [
        'wajib_resep' => 'boolean',
        'konsinyasi' => 'boolean',
        'minimal_stok' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
