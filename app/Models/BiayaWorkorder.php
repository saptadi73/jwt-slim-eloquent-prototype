<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaWorkorder extends Model
{


    protected $table = 'biayaworkorders';
    protected $primaryKey = 'id';
    protected $fillable = ['jumlah','harga','total','workorder_id','product_id','id'];
    protected $casts = [
        'jumlah' => 'integer',
        'harga' => 'integer',
        'total' => 'integer',
    ];
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
