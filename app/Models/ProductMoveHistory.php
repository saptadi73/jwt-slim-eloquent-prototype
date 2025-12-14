<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class ProductMoveHistory extends Model
{
    use HasUuid;

    protected $table = 'product_move_histories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'move_type',       // 'purchase' / 'sale'
        'direction',       // 'in' / 'out'
        'qty',
        'stock_before',
        'stock_after',
        'source_table',    // 'purchase_order_line' / 'product_order_line'
        'source_id',       // id baris line
        'source_order_id', // id purchase_orders / sale_orders
        'move_date',
        'note',
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
