<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Gaji extends Model
{
    use HasUuid;

    protected $table = 'gaji';
    protected $primaryKey = 'id';
    protected $fillable = ['periode','jumlah','pegawai_id'];

    protected $casts = [
        'periode' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
