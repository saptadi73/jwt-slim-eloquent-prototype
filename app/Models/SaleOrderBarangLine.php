<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrderBarangLine extends Model
{
    protected $table = 'saleorderbarangline';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'qty',
        'harga',
        'total',
        'keterangan',
        'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
