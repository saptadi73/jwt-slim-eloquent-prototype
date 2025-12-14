<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class JournalLine extends Model
{
    use HasUuid;

    protected $table = 'journal_lines';
    protected $primaryKey = 'id';

    protected $fillable = [
        'journal_entry_id',
        'chart_of_account_id',
        'description',
        'debit',
        'credit',
        'vendor_id',
        'customer_id',
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
