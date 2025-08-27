<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class WorkOrderBarangLine extends Model
{
    use HasUuid;
    protected $table = 'workorderbarangline';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $guarded = [];
    public $timestamps = true;

    protected $casts = [
        'qty',
        'harga',
        'total',
        'product_id',
        'workorder_id'
    ];

    // -------------------
    // Relasi
    // -------------------

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function workorders()
    {
        return $this->belongsTo(WorkOrder::class, 'workorder_id');
    }
}
