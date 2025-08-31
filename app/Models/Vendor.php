<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{

    protected $table = 'vendors';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'alamat',
        'email',
        'gambar',
        'hp',
    ];

    public $timestamps = true;

    /* =========================
     * Relasi
     * ========================= */

    // Relasi ke PurchaseOrder
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class, 'vendor_id');
    }
}
