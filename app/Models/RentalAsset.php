<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RentalAsset extends Model
{


    protected $table = 'rental_assets';
    protected $primaryKey = 'id';
    protected $fillable = ['tipe_id','keterangan',
        'lokasi','brand_id','model','freon','kapasitas',
        'gambar','id','status'
    ];
    protected $casts = [
        'last_service' => 'datetime',
        'next_service' => 'datetime',
    ];
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

   

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'tipe_id');
    }
}
