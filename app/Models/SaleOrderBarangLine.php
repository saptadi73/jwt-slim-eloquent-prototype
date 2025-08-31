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
    public $timestamps = true;

    // Rekomendasi: pakai fillable biar eksplisit
    protected $fillable = [
        'qty',
        'harga',
        'total',
        'keterangan',
        'product_id',
        'id',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    // bigInteger di schema → cast ke integer
    protected $casts = [
        'qty'   => 'integer',
        'harga' => 'integer',
        'total' => 'integer',
    ];

    /* =========================
     * Relasi
     * ========================= */

    // Line barang milik satu produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Header SO <-> Line Barang: many-to-many via pivot saleorder_salebarangorderline
    public function saleorders()
    {
        return $this->belongsToMany(
            SaleOrder::class,
            'saleorder_salebarangorderline',
            'saleorderbarangline_id',  // FK ke tabel ini di pivot
            'saleorder_id'             // FK ke saleorder di pivot
        )->withTimestamps();
    }

    // Workorder <-> Line Barang: many-to-many via pivot workorder_salebarangorderline
    public function workorders()
    {
        return $this->belongsToMany(
            Workorder::class,
            'workorder_salebarangorderline',
            'saleorderbarangline_id',  // FK ke tabel ini di pivot
            'workorder_id'             // FK ke workorders di pivot
        )->withTimestamps();
    }
}
