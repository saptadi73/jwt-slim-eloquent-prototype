<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkorderSaleBarangOrderLine extends Model
{
    protected $table = 'workorder_salebarangorderline';

    // Pivot tidak punya kolom id
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true; // di schema kamu pivot pakai timestamps

    // Isi sesuai kolom pivot (+ atribut tambahan kalau nanti ada)
    protected $fillable = [
        'workorder_id',
        'saleorderbarangline_id',
        // 'qty_terpakai', 'catatan', ...
    ];

    // Relasi balik
    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id');
    }

    public function saleOrderBarangLine()
    {
        return $this->belongsTo(SaleOrderBarangLine::class, 'saleorderbarangline_id');
    }
}
