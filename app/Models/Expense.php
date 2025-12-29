<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Expense extends Model
{

    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tanggal','jenis','nomor','keterangan','jumlah','status','bukti','id'];
    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'status' => 'string',
        'keterangan' =>'string',
        'bukti' => 'string',
        'nomor' => 'string',
        'jenis' => 'string',
    ];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

}