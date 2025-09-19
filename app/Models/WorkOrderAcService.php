<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderAcService extends Model
{
    protected $table = 'workorder_service'; // Nama tabel
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_asset_id',
        'teknisi_id',
        'keluhan',
        'keterangan',
        'pengecekan',
        'service',
        'tambah_freon',
        'isi_freon',
        'bongkar',
        'pasang',
        'bongkar_pasang',
        'perbaikan',
        'check_evaporator',
        'keterangan_evaporator',
        'check_fan_indoor',
        'keterangan_fan_indoor',
        'check_swing',
        'keterangan_swing',
        'check_tegangan_input',
        'keterangan_tegangan_input',
        'check_thermis',
        'keterangan_thermis',
        'check_temperatur_indoor',
        'keterangan_temperatur_indoor',
        'check_lain_indoor',
        'keterangan_lain_indoor',
        'check_kondensor',
        'keterangan_kondensor',
        'check_fan_outdoor',
        'keterangan_fan_outdoor',
        'check_kapasitor',
        'keterangan_kapasitor',
        'check_tekanan_freon',
        'keterangan_tekanan_freon',
        'check_arus',
        'keterangan_arus',
        'check_temperatur_outdoor',
        'keterangan_temperatur_outdoor',
        'check_lain_outdoor',
        'keterangan_lain_outdoor',
        'hasil_pekerjaan',
        'tanda_tangan_pelanggan',
        'status',
        'workorder_id',
        'customerCode'
    ];

    // Relasi dengan tabel customer
    public function customerAsset()
    {
        return $this->belongsTo(CustomerAsset::class, 'customer_asset_id'); // Relasi dengan customer
    }

    // Relasi dengan tabel teknisi (user)
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'teknisi_id'); // Relasi dengan user sebagai teknisi
    }
    // Relasi dengan workorder
    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id'); // Relasi dengan workorder
    }
}