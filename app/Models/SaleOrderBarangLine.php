<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class SaleOrderBarangLine extends Model
{
    use HasUuid;
    protected $table = 'saleorderbarangline';
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
        'saleorder_id'
    ];

    // -------------------
    // Relasi
    // -------------------

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function saleorders()
    {
        return $this->belongsTo(SaleOrder::class, 'saleorder_id');
    }
}
