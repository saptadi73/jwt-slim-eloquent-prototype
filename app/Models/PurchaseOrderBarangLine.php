<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class PurchaseOrderBarangLine extends Model
{
    use HasUuid;

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
