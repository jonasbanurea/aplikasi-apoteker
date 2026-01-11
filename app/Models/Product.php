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
        'unit_kemasan',
        'unit_terkecil',
        'isi_per_kemasan',
        'jual_eceran',
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
        'jual_eceran' => 'boolean',
        'minimal_stok' => 'integer',
        'isi_per_kemasan' => 'integer',
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

    /**
     * Hitung harga per unit terkecil (eceran)
     */
    public function getHargaEceranAttribute()
    {
        if (!$this->jual_eceran || !$this->isi_per_kemasan || $this->isi_per_kemasan == 0) {
            return null;
        }
        
        return round($this->harga_jual / $this->isi_per_kemasan, 2);
    }

    /**
     * Get harga berdasarkan unit
     */
    public function getHargaByUnit($unit = null)
    {
        if (!$unit || $unit === 'kemasan' || $unit === $this->unit_kemasan) {
            return $this->harga_jual;
        }
        
        if ($unit === 'eceran' || $unit === $this->unit_terkecil) {
            return $this->harga_eceran ?? $this->harga_jual;
        }
        
        return $this->harga_jual;
    }

    /**
     * Get display name dengan info unit
     */
    public function getDisplayNameWithUnitAttribute()
    {
        $name = "{$this->sku} - {$this->nama_dagang}";
        
        if ($this->jual_eceran && $this->unit_kemasan && $this->unit_terkecil) {
            $name .= " ({$this->unit_kemasan} @ Rp" . number_format($this->harga_jual, 0, ',', '.') . 
                     " / {$this->unit_terkecil} @ Rp" . number_format($this->harga_eceran, 0, ',', '.') . ")";
        }
        
        return $name;
    }
}
