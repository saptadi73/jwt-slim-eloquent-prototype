<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class SaleOrder extends Model
{
    use HasUuid;

    protected $table = 'saleorder';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $guarded = [];        // atur sesuai selera
    public $timestamps = true;

    protected $fillable = [
        'tanggal',
        'total',
        'diskon',
        'grandtotal',
        'customer_id'
    ];

    // -------------------
    // Relasi
    // -------------------

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function saleorderbarangLines()
    {
        return $this->hasMany(SaleOrderBarangLine::class, 'saleorder_id');
    }

    public function saleorderjasaLines()
    {
        return $this->hasMany(SaleOrderJasaLine::class, 'saleorder_id');
    }
}
