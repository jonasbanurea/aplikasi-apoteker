<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReturn extends Model
{
    use HasFactory;

    public const STATUS_POSTED = 'POSTED';
    public const STATUS_VOID = 'VOID';

    protected $fillable = [
        'return_no',
        'supplier_id',
        'user_id',
        'return_date',
        'status',
        'total_qty',
        'total_value',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_qty' => 'integer',
        'total_value' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierReturnItem::class);
    }
}
