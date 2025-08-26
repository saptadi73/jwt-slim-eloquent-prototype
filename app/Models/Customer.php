<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Customer extends Model
{
    use HasUuid;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama','alamat','hp','lokasi','brand','model','freon','kapasitas'
    ];

    public function workorders()
    {
        return $this->hasMany(Workorder::class, 'customer_id');
    }
}
