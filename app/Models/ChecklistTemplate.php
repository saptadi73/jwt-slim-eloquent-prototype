<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ChecklistTemplate extends Model
{
    

    protected $table = 'checklist_template';
    protected $primaryKey = 'id';
    protected $fillable = ['no_urut','kode_checklist','title',
        'pic','jenis_workorder','checklist'
    ];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'no_urut' => 'integer',
    ];

}
