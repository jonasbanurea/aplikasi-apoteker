<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_no',
        'expired_date',
        'qty_on_hand',
        'cost_price',
        'received_at',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'received_at' => 'date',
        'qty_on_hand' => 'integer',
        'cost_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class, 'batch_id');
    }
}
