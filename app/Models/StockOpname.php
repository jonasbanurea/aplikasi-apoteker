<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING_APPROVAL';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';

    protected $fillable = [
        'user_id',
        'opname_date',
        'status',
        'requires_approval',
        'approval_threshold_value',
        'total_items',
        'total_diff_qty',
        'total_system_value',
        'total_physical_value',
        'total_diff_value',
        'notes',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'requires_approval' => 'boolean',
        'approval_threshold_value' => 'decimal:2',
        'total_items' => 'integer',
        'total_diff_qty' => 'integer',
        'total_system_value' => 'decimal:2',
        'total_physical_value' => 'decimal:2',
        'total_diff_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }
}
