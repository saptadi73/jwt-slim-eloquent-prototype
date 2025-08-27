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
        'change',
        'type',  // 'penjualan', 'pembelian'
        'saleorderbarangline_id',
        'purchasebarangorderline_id',
    ];

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relasi ke saleorderbarangline
    public function saleOrderBarangLine()
    {
        return $this->belongsTo(SaleOrderBarangLine::class, 'saleorderbarangline_id');
    }

    // Relasi ke purchasebarangorderline
    public function purchaseOrderBarangLine()
    {
        return $this->belongsTo(PurchaseOrderBarangLine::class, 'purchasebarangorderline_id');
    }
}
