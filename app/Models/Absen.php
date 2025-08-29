<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Absen extends Model
{
    use HasUuid;

    protected $table = 'absen';
    protected $primaryKey = 'id';
    protected $fillable = ['tanggal','pegawai_id'];

    protected $casts = [
        'tanggal' => 'date',
    ];
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
