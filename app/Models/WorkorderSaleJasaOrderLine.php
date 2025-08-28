<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkorderSaleJasaOrderLine extends Model
{
    protected $table = 'workorder_salejasaorderline';

    // Pivot tanpa kolom id
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true; // pivot kamu pakai timestamps

    protected $fillable = [
        'workorder_id',
        'saleorderjasaline_id',
        // 'qty_terpakai', 'catatan', // aktifkan kalau kamu tambahkan di schema
    ];

    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id');
    }

    public function saleOrderJasaLine()
    {
        return $this->belongsTo(SaleOrderJasaLine::class, 'saleorderjasaline_id');
    }
}
