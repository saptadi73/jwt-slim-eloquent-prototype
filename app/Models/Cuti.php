<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Cuti extends Model
{
    use HasUuid;

    protected $table = 'cuti';
    protected $primaryKey = 'id';
    protected $fillable = ['tanggal_start','tanggal_end','alasan','pegawai_id'];

    protected $casts = [
        'tanggal_start' => 'date',
        'tanggal_end'   => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
