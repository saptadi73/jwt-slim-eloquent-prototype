<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisWorkorder extends Model
{


    protected $table = 'jenis_workorder';
    protected $primaryKey = 'id';
    protected $fillable = ['nama','id'];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function checklistTemplates()
    {
        return $this->hasMany(ChecklistTemplate::class, 'jenis_id');
    }
}
