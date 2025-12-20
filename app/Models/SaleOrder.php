<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;
use App\Enums\OrderStatus;

class SaleOrder extends Model
{
    use HasUuid;

    protected $table = 'sale_orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'order_number',
        'order_date',
        'status',
        'subtotal',
        'tax',
        'total',
        'customer_id',
        'nama',
        'alamat',
        'hp',
        'keterangan',
    ];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'order_date' => 'date:Y-m-d',
        'subtotal'   => 'decimal:2',
        'tax'        => 'decimal:2',
        'total'      => 'decimal:2',
        'status'     => OrderStatus::class,  // ⬅️ ENUM cast
    ];

    /* =======================
     *  RELASI
     * ======================= */

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function productLines()
    {
        return $this->hasMany(ProductOrderLine::class, 'sale_order_id');
    }

    public function serviceLines()
    {
        return $this->hasMany(ServiceOrderLine::class, 'sale_order_id');
    }
}
