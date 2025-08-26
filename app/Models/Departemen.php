<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Departemen extends Model
{
    use HasUuid;

    protected $table = 'departemen';
    protected $primaryKey = 'id';
    protected $fillable = ['nama'];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'departemen_id');
    }
}
