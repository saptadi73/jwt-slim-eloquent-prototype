<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SaleOrderSaleJasaOrderLine extends Pivot
{
    protected $table = 'saleorder_salejasaorderline';

    public $timestamps = true;

    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'saleorder_id',
        'saleorderjasaline_id',
        // Tambah atribut pivot lain kalau ada nanti.
    ];

    // Relasi balik
    public function saleorder()
    {
        return $this->belongsTo(SaleOrder::class, 'saleorder_id');
    }

    public function jasaLine()
    {
        return $this->belongsTo(SaleOrderJasaLine::class, 'saleorderjasaline_id');
    }
}
