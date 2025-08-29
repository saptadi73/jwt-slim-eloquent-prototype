<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class SaleOrderJasaLine extends Model
{
    use HasUuid;

    protected $table = 'saleorderjasaline';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'qty', 'harga', 'total', 'keterangan', 'product_id',
    ];

    protected $casts = [
        // tidak ada tanggal di line, biarkan kosong
    ];


    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    // LINE JASA selalu milik satu produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Header SO ke Line Jasa: many-to-many via saleorder_salejasaorderline
    public function saleorders()
    {
        return $this->belongsToMany(
            SaleOrder::class,
            'saleorder_salejasaorderline',
            'saleorderjasaline_id',   // FK ke tabel ini di pivot
            'saleorder_id'            // FK ke tabel saleorder di pivot
        )->withTimestamps();
    }

    // Workorder ke Line Jasa: many-to-many via workorder_salejasaorderline
    public function workorders()
    {
        return $this->belongsToMany(
            Workorder::class,
            'workorder_salejasaorderline',
            'saleorderjasaline_id',   // FK ke tabel ini di pivot
            'workorder_id'            // FK ke tabel workorders di pivot
        )->withTimestamps();
    }
}
