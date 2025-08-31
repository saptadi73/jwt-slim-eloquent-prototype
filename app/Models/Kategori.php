<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Kategori extends Model
{

    protected $table = 'kategori';
    protected $primaryKey = 'id';
    protected $fillable = ['id','nama'];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function products()
    {
        return $this->hasMany(Product::class, 'kategori_id');
    }
}
