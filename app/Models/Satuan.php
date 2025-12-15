<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Satuan extends Model
{

    use HasUuid;
    protected $table = 'satuan';
    protected $primaryKey = 'id';
    protected $fillable = ['nama'];

    public function products()
    {
        return $this->hasMany(Product::class, 'satuan_id');
    }
}
