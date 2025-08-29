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
        'nama','satuan_id','deskripsi','kode','type','harga','hpp','stok','brand','model','kategori_id'
    ];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function biayaWorkorders()
    {
        return $this->hasMany(BiayaWorkorder::class, 'product_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

}
