<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Service extends Model
{
    use HasUuid;

    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama','deskripsi','harga'
    ];

    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

}
