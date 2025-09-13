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
        'nowo','tanggal','jenis'
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

}
