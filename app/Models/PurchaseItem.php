<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'batch_no',
        'expired_date',
        'qty',
        'bonus_qty',
        'cost_price',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'qty' => 'integer',
        'bonus_qty' => 'integer',
        'cost_price' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
