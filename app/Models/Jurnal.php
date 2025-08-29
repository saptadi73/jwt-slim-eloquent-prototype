<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Jurnal extends Model
{
    use HasUuid;

    protected $table = 'jurnal';
    protected $primaryKey = 'id';
    protected $fillable = [
        'account_id','vendor_id','customer_id','debit','kredit','keterangan','tanggal'];
    protected $casts = [
        'tanggal' => 'date',
        'debit' => 'bigInteger',
        'kredit' => 'bigInteger',
    ];
    protected $keyType = 'string';
    public $incrementing = false;   // UUID

    public $timestamps = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'account_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
