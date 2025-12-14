<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Role extends Model
{
    use HasUuid;

    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['name', 'label'];
    public $timestamps = true;

    // Relasi Many-to-Many dengan User
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'role_users',
            'role_id',
            'user_id'
        )->withTimestamps();
    }
}
