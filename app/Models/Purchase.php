<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public const STATUS_POSTED = 'POSTED';
    public const STATUS_CONSIGNMENT = 'CONSIGNMENT';

    protected $fillable = [
        'supplier_id',
        'invoice_no',
        'date',
        'discount',
        'total',
        'due_date',
        'status',
        'is_consignment',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'is_consignment' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
