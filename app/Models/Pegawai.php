<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nama', 'alamat', 'hp', 'email', 'departemen_id', 'group_id', 'position_id', 'url_foto', 'tanda_tangan_id', 'tanda_tangan', 'hire_date', 'is_active'];

    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
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

    public function tandaTangan()
    {
        return $this->belongsTo(TandaTangan::class, 'tanda_tangan_id');
    }

    public function timeOffs()
    {
        return $this->hasMany(TimeOff::class, 'pegawai_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'pegawai_id');
    }

    public function workorders()
    {
        return $this->hasMany(Workorder::class, 'pegawai_id');
    }
}
