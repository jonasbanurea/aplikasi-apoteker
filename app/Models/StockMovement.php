<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'batch_id',
        'product_id',
        'qty',
        'ref_type',
        'ref_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function batch()
    {
        return $this->belongsTo(StockBatch::class, 'batch_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
