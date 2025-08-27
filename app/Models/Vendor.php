<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Vendor extends Model
{
    use HasUuid;

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
}
