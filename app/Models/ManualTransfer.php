<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class ManualTransfer extends Model
{
    use HasUuid;

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
