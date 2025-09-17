<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderPenyewaan extends Model
{
    protected $table = 'workorder_penyewaan'; // Nama tabel
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'rental_asset_id',
        'teknisi_id',
        'tanda_tangan_teknisi',
        'tanda_tangan_pelanggan',
        'hasil_pekerjaan',
        'checkIndoor',
        'keteranganIndoor',
        'checkOutdoor',
        'keteranganOutdoor',
        'checkPipa',
        'keteranganPipa',
        'checkSelang',
        'keteranganSelang',
        'checkKabel',
        'keteranganKabel',
        'checkInstIndoor',
        'keteranganInstIndoor',
        'checkInstOutdoor',
        'keteranganInstOutdoor',
        'checkInstListrik',
        'keteranganInstListrik',
        'checkInstPipa',
        'keteranganInstPipa',
        'checkBuangan',
        'keteranganBuangan',
        'checkVaccum',
        'keteranganVaccum',
        'checkFreon',
        'keteranganFreon',
        'checkArus',
        'keteranganArus',
        'checkEva',
        'keteranganEva',
        'checkKondensor',
        'keteranganKondensor',
        'checkIndoorB',
        'keteranganIndoorB',
        'checkOutdoorB',
        'keteranganOutdoorB',
        'checkPipaB',
        'keteranganPipaB',
        'checkSelangB',
        'keteranganSelangB',
        'checkKabelB',
        'keteranganKabelB',
        'tanda_tangan_pelanggan',
        'status',
        'workorder_id'
    ];

    // Relasi dengan customer asset
    public function rentalAsset()
    {
        return $this->belongsTo(rentalAsset::class, 'rental_asset_id'); // Relasi dengan customer_asset
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'teknisi_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id'); // Relasi dengan customer
    }
    // Relasi dengan workorder
    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id'); // Relasi dengan workorder
    }
}
