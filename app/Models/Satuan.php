<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{

    protected $table = 'satuan';
    protected $primaryKey = 'id';
    protected $fillable = ['nama','id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'satuan_id');
    }
}
