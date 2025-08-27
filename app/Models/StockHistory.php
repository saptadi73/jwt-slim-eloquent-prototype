<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class StockHistory extends Model
{
    use HasUuid;

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
    ];

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
