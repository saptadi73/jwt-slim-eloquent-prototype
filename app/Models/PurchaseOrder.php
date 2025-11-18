<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;
use App\Enums\OrderStatus;

class PurchaseOrder extends Model
{
    use HasUuid;

    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_number',
        'order_date',
        'status',
        'subtotal',
        'tax',
        'total',
        'vendor_id',
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function productLines()
    {
        return $this->hasMany(PurchaseOrderLine::class, 'purchase_order_id');
    }

}
