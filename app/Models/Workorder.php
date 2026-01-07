<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workorder extends Model
{

    protected $table = 'workorders';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'nowo','tanggal','jenis','id'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // WO <-> SALE ORDER BARANG LINE: many-to-many via workorder_salebarangorderline
    public function saleBarangLines()
    {
        return $this->belongsToMany(
            SaleOrderBarangLine::class,
            'workorder_salebarangorderline',
            'workorder_id',            // FK wo di pivot
            'saleorderbarangline_id'   // FK line barang di pivot
        )->withTimestamps();
    }

    // WO <-> SALE ORDER JASA LINE: many-to-many via workorder_salejasaorderline
    public function saleJasaLines()
    {
        return $this->belongsToMany(
            SaleOrderJasaLine::class,
            'workorder_salejasaorderline',
            'workorder_id',            // FK wo di pivot
            'saleorderjasaline_id'     // FK line jasa di pivot
        )->withTimestamps();
    }

    public function workOrderAcService()
    {
        return $this->hasOne(\App\Models\WorkOrderAcService::class, 'workorder_id', 'id');
    }

    public function workorderPenyewaan()
    {
        return $this->hasOne(\App\Models\WorkOrderPenyewaan::class, 'workorder_id', 'id');
    }

    public function workorderPenjualan()
    {
        return $this->hasOne(\App\Models\WorkOrderPenjualan::class, 'workorder_id', 'id');
    }
}
