<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;
use App\Models\PurchaseOrderLine;

class Product extends Model
{
    use HasUuid;

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama','satuan_id','deskripsi','kode','tipe','harga','hpp','stok','brand_id','model','kategori_id','is_sealable','gambar'
    ];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    protected $casts = [
        'is_sealable' => 'boolean',
    ];
    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function purchaseorderline()
    {
        return $this->hasMany(PurchaseOrderLine::class,'product_id');
    }

}
