<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Kategori extends Model
{
    use HasUuid;

    protected $table = 'kategori';
    protected $primaryKey = 'id';
    protected $fillable = ['nama'];

    public function products()
    {
        return $this->hasMany(Product::class, 'kategori_id');
    }
}
