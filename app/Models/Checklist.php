<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
   

    protected $table = 'checklist';
    protected $primaryKey = 'id';
    protected $fillable = ['jawaban','keterangan',
        'checklist_template_id','workorder_id','pegawai_id','id'
    ];

    protected $casts = [
        'jawaban' => 'string',
        'keterangan' => 'string',
        'checklist_template_id' => 'string',
        'workorder_id' => 'string',
        'pegawai_id' => 'string',
    ];
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

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
