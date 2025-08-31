<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
 

    protected $table = 'cuti';
    protected $primaryKey = 'id';
    protected $fillable = ['tanggal_start','tanggal_end','alasan','pegawai_id','id'];

    protected $casts = [
        'tanggal_start' => 'date',
        'tanggal_end'   => 'date',
    ];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
