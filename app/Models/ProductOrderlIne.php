<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class ProductOrderLine extends Model
{
    use HasUuid;

    protected $table = 'product_order_line';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sale_order_id',
        'product_id',
        'line_number',
        'description',
        'qty',
        'unit_price',
        'discount',
        'line_total',
    ];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'line_number' => 'integer',
        'qty'         => 'decimal:2',
        'unit_price'  => 'decimal:2',
        'discount'    => 'decimal:2',
        'line_total'  => 'decimal:2',
    ];

    /* =======================
     *  RELASI
     * ======================= */

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
