<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'batch_id',
        'system_qty',
        'physical_qty',
        'diff_qty',
        'reason',
        'unit_cost',
        'diff_value',
        'notes',
    ];

    protected $casts = [
        'system_qty' => 'integer',
        'physical_qty' => 'integer',
        'diff_qty' => 'integer',
        'unit_cost' => 'decimal:2',
        'diff_value' => 'decimal:2',
    ];

    public function opname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(StockBatch::class, 'batch_id');
    }
}
