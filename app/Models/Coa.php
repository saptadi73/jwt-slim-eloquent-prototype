<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Coa extends Model
{
    use HasUuid;

    protected $table = 'coa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama','kode','tipe','kategori'];

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class, 'account_id');
    }
}
