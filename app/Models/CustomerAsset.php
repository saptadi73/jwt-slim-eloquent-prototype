<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class CustomerAsset extends Model
{
    use HasUuid;

    protected $table = 'customer_assets';
    protected $primaryKey = 'id';
    protected $fillable = ['tipe','keterangan',
        'lokasi','brand','model','freon','kapasitas',
        'customer_id'
    ];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
