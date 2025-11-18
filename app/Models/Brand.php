<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{


    protected $table = 'brand';
    protected $primaryKey = 'id';
    protected $fillable = ['nama','id'];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function customerAssets()
    {
        return $this->hasMany(CustomerAsset::class, 'brand_id');
    }

    public function rentalAssets()
    {
        return $this->hasMany(RentalAsset::class, 'brand_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
