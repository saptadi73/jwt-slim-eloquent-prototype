<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Customer extends Model
{
    use HasUuid;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama','alamat','hp'];

    public function workorders()
    {
        return $this->hasMany(Workorder::class, 'customer_id');
    }
    public function customerassets()
    {
        return $this->hasMany(CustomerAsset::class, 'customer_id');
    }

    public function saleorders()
    {
        return $this->hasMany(SaleOrder::class, 'customer_id');
    }

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class, 'customer_id');
    }
}
