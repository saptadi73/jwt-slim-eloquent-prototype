<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama',
        'alamat',
        'hp',
        'gambar',
        'email',
        'kode_pelanggan','id'
    ];
    public $timestamps = true;
    public $incrementing = false;
    protected $keyType = 'string';

    public function workorders()
    {
        return $this->hasMany(Workorder::class, 'customer_id');
    }
    public function customerassets()
    {
        return $this->hasMany(CustomerAsset::class, 'customer_id');
    }

    public function saleorders()
    {
        return $this->hasMany(SaleOrder::class, 'customer_id');
    }

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class, 'customer_id');
    }
}
