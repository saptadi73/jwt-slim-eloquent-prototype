<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{


    protected $table = 'coa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama','kode','tipe','kategori','id'];

    public function jurnals()
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }
}
