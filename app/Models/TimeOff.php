<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeOff extends Model
{
    protected $table = 'time_offs';
    
    protected $fillable = [
        'pegawai_id',
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Time off types
     */
    const TYPE_ANNUAL_LEAVE = 'annual_leave';
    const TYPE_SICK_LEAVE = 'sick_leave';
    const TYPE_UNPAID_LEAVE = 'unpaid_leave';
    const TYPE_MATERNITY_LEAVE = 'maternity_leave';
    const TYPE_PATERNITY_LEAVE = 'paternity_leave';
    const TYPE_OTHER = 'other';

    /**
     * Time off status
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the employee who requested the time off
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the approver (employee)
     */
    public function approver()
    {
        return $this->belongsTo(Pegawai::class, 'approved_by');
    }
}
