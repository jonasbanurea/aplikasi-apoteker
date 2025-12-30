<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'CASH';
    public const METHOD_NON_CASH = 'NON_CASH';

    protected $fillable = [
        'invoice_no',
        'user_id',
        'shift_id',
        'sale_date',
        'payment_method',
        'subtotal',
        'discount_total',
        'total',
        'paid_amount',
        'change_amount',
        'no_resep',
        'dokter',
        'catatan',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
