<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipe extends Model
{


    protected $table = 'tipe';
    protected $primaryKey = 'id';
    protected $fillable = ['nama','id'];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function customerAssets()
    {
        return $this->hasMany(CustomerAsset::class, 'tipe_id');
    }
}
