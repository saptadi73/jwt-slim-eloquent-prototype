<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class JournalEntry extends Model
{
    use HasUuid;

    protected $table = 'journal_entries';
    protected $primaryKey = 'id';

    protected $fillable = [
        'entry_date',
        'reference_number',
        'description',
        'status',
        'created_by',
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'entry_date' => 'date:Y-m-d',
    ];

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class, 'journal_entry_id');
    }
}
