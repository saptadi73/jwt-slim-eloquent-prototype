<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SaleOrderSaleBarangOrderLine extends Pivot
{
    protected $table = 'saleorder_salebarangorderline';

    public $timestamps = true;

    // Tidak ada kolom id, jadi primaryKey null
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'saleorder_id',
        'saleorderbarangline_id',
        // Tambahkan atribut pivot lain jika diperlukan seperti 'qty_terpakai', 'catatan'
    ];

    // Relasi balik ke SaleOrder
    public function saleorder()
    {
        return $this->belongsTo(SaleOrder::class, 'saleorder_id');
    }

    // Relasi balik ke SaleOrderBarangLine
    public function saleOrderBarangLine()
    {
        return $this->belongsTo(SaleOrderBarangLine::class, 'saleorderbarangline_id');
    }
}
