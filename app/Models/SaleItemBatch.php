<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItemBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_item_id',
        'stock_batch_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function stockBatch()
    {
        return $this->belongsTo(StockBatch::class);
    }
}
