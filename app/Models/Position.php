<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'positions';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;   // UUID
    
    protected $fillable = [
        'id',
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get employees with this position
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    /**
     * Get pegawai with this position
     */
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'position_id');
    }
}
