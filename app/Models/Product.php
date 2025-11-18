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
        'nama','satuan_id','deskripsi','kode','type','harga','hpp','stok','brand_id','model','kategori_id','is_sealable'
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

    public function productorderlines()
    {
        return $this->hasMany(ProductOrderLine::class, 'product_id');
    }

    public function purchaseorderline()
    {
        return $this->hasMany(PurchaseOrderLine::class,'product_id');
    }

}
