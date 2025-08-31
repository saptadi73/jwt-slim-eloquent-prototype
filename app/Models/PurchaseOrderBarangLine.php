<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PurchaseOrderBarangLine extends Model
{

    protected $table = 'purchaseorderbarangline';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'qty',
        'harga',
        'total',
        'keterangan',
        'product_id',
        'purchaseorder_id',
        'id',
    ];

    public $timestamps = true;

    /* =========================
     * Relasi
     * ========================= */

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relasi ke PurchaseOrder (one-to-many)
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchaseorder_id');
    }
}
