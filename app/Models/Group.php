<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Group extends Model
{
    use HasUuid;

    protected $table = 'groups';
    protected $primaryKey = 'id';
    protected $fillable = ['nama'];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function workorders()
    {
        return $this->hasMany(Workorder::class, 'group_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'group_id');
    }
}
