<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RentalAsset extends Model
{


    protected $table = 'rental_assets';
    protected $primaryKey = 'id';
    protected $fillable = ['tipe_id','keterangan',
        'lokasi','brand_id','model','freon','kapasitas',
        'gambar','id','status','harga_perolehan','harga_sewa','sisa_harga_sekarang',
    ];
    protected $casts = [
        'harga_perolehan' => 'integer',
        'harga_sewa' => 'integer',
        'sisa_harga_sekarang' => 'integer',
    ];
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'tipe_id');
    }
}
