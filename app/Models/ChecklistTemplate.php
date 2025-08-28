<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class ChecklistTemplate extends Model
{
    use HasUuid;

    protected $table = 'checklist_template';
    protected $primaryKey = 'id';
    protected $fillable = ['no_urut','kode_checklist','title',
        'pic','jenis_workorder','checklist'
    ];

    protected $casts = [
        'no_urut' => 'integer',
    ];

}
