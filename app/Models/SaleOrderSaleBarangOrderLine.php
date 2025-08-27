<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SaleOrderSaleBarangOrderLine extends Pivot
{
    protected $table = 'saleorder_salebarangorderline';

    // Pivot kamu pakai timestamps di schema
    public $timestamps = true;

    // Tidak ada kolom id; primary key komposit ditangani oleh Pivot
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'saleorder_id',
        'saleorderbarangline_id',
        // Tambah atribut pivot lain kalau nanti ada: 'qty_terpakai', 'catatan', dst.
    ];

    // Relasi balik
    public function saleorder()
    {
        return $this->belongsTo(SaleOrder::class, 'saleorder_id');
    }

    public function barangLine()
    {
        return $this->belongsTo(SaleOrderBarangLine::class, 'saleorderbarangline_id');
    }
}
