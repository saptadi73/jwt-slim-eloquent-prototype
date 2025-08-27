<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class PurchaseOrder extends Model
{
    use HasUuid;

    protected $table = 'purchaseorders';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'nopo',
        'tanggal',
        'total',
        'diskon',
        'grandtotal',
        'vendor_id',
    ];

    public $timestamps = true;

    /* =========================
     * Relasi
     * ========================= */

    // Relasi ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // Relasi one-to-many ke PurchaseOrderBarangLine
    public function purchaseOrderBarangLines()
    {
        return $this->hasMany(PurchaseOrderBarangLine::class, 'purchaseorder_id');
    }
}
