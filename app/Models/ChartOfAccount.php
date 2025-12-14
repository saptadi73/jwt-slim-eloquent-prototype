<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class ChartOfAccount extends Model
{
    use HasUuid;

    protected $table = 'chart_of_accounts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'normal_balance',
        'is_active',
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class, 'chart_of_account_id');
    }
}
