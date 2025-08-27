<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Workorder extends Model
{
    use HasUuid;

    protected $table = 'workorders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nowo','tanggal','keluhan','pengecekan','service','tambahfreon',
        'thermis','bongkar','pasang','bongkarpasang','perbaikan','hasil',
        'customer_id','group_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function workorderbarangline()
    {
        return $this->hasMany(WorkOrderBarangLine::class, 'workorderbarangline_id');
    }

    public function workorderjasaline()
    {
        return $this->hasMany(WorkOrderJasaLine::class, 'workorderjasaline_id');
    }
}
