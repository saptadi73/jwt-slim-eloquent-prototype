<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{

    protected $table = 'stock_history';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'qty',
        'satuan',
        'type',  // 'penjualan', 'pembelian','manual keluar','manual masuk'
        'order_id',
        'id',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
