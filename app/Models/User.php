<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class User extends Model
{
    use HasUuid;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['id', 'name', 'email', 'password'];
    protected $hidden = ['password'];
    public $timestamps = true;

    // Relasi Many-to-Many dengan Role
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,      // Model terkait
            'role_users',     // Nama pivot table
            'user_id',        // Foreign key user di pivot table
            'role_id'         // Foreign key role di pivot table
        )->withTimestamps();
    }
}
