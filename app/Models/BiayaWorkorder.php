<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class BiayaWorkorder extends Model
{
    use HasUuid;

    protected $table = 'biayaworkorders';
    protected $primaryKey = 'id';
    protected $fillable = ['jumlah','harga','total','workorder_id','product_id'];

    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
