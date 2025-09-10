<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Pegawai extends Model
{

    protected $table = 'pegawai';
    protected $primaryKey = 'id';
    protected $fillable = ['nama','alamat','hp','departemen_id','group_id','email','id','tanda_tangan'];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absen::class, 'pegawai_id');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'pegawai_id');
    }

    public function lembur()
    {
        return $this->hasMany(Lembur::class, 'pegawai_id');
    }

    public function ijin()
    {
        return $this->hasMany(Ijin::class, 'pegawai_id');
    }

    public function gaji()
    {
        return $this->hasMany(Gaji::class, 'pegawai_id');
    }

    public function jatahCuti()
    {
        return $this->hasMany(JatahCuti::class, 'pegawai_id');
    }
}
