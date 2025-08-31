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
        'nowo','tanggal','keluhan','pengecekan','service','tambahfreon',
        'thermis','bongkar','pasang','bongkarpasang','perbaikan','hasil',
        'customer_id','group_id','id'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Induknya WO
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

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
