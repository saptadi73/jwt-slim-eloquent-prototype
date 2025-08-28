<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Checklist extends Model
{
    use HasUuid;

    protected $table = 'checklist';
    protected $primaryKey = 'id';
    protected $fillable = ['jawaban','keterangan',
        'checklist_template_id','workorder_id','pegawai_id'
    ];

    public function workorder()
    {
        return $this->belongsTo(WorkOrder::class, 'workorder_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function checklist()
    {
        return $this->belongsTo(ChecklistTemplate::class, 'checklist_template_id');
    }
}
