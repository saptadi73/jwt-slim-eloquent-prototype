<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Product extends Model
{
    use HasUuid;

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama','satuan','deskripsi','kode','type','harga','stok','brand','model','kategori_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function biayaWorkorders()
    {
        return $this->hasMany(BiayaWorkorder::class, 'product_id');
    }
}
