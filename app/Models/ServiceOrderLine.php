<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class ServiceOrderLine extends Model
{
    use HasUuid;

    protected $table = 'service_order_lines';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sale_order_id',
        'service_id',
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

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
