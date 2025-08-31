<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualTransfer extends Model
{


    protected $table = 'manual_transfer';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    // Rekomendasi: pakai fillable biar eksplisit
    protected $fillable = [
        'qty',
        'satuan',
        'type',
        'keterangan',
        'product_id',
        'id',
    ];


    // bigInteger di schema → cast ke integer
    protected $casts = [
        'qty'   => 'integer',
    ];

    /* =========================
     * Relasi
     * ========================= */

    // Line barang milik satu produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
