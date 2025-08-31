<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Expense extends Model
{

    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tanggal','product_id','nomor','keterangan','jumlah','status','bukti','id'];
    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'bigInteger',
        'status' => 'string',
        'keterangan' =>'string',
        'bukti' => 'string',
        'nomor' => 'string',
    ];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}