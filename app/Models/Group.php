<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Group extends Model
{

    protected $table = 'groups';
    protected $primaryKey = 'id';
    protected $fillable = ['id','nama'];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'group_id');
    }
}
