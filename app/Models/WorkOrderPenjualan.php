<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderPenjualan extends Model
{
    protected $table = 'workorder_penjualan'; // Nama tabel
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_asset_id',
        'teknisi_id',
        'check_indoor',
        'keterangan_indoor',
        'check_outdoor',
        'keterangan_outdoor',
        'check_pipa',
        'keterangan_pipa',
        'check_selang',
        'keterangan_selang',
        'check_kabel',
        'keterangan_kabel',
        'check_inst_indoor',
        'keterangan_inst_indoor',
        'check_inst_outdoor',
        'keterangan_inst_outdoor',
        'check_inst_listrik',
        'keterangan_inst_listrik',
        'check_inst_pipa',
        'keterangan_inst_pipa',
        'check_buangan',
        'keterangan_buangan',
        'check_vaccum',
        'keterangan_vaccum',
        'check_freon',
        'keterangan_freon',
        'check_arus',
        'keterangan_arus',
        'check_eva',
        'keterangan_eva',
        'check_kondensor',
        'keterangan_kondensor',
        'hasil_pekerjaan',
        'tanda_tangan_pelanggan',
        'status',
        'workorder_id',
        'customerCode'
    ];

    // Relasi dengan customer asset
    public function customerAsset()
    {
        return $this->belongsTo(CustomerAsset::class, 'customer_asset_id'); // Relasi dengan customer_asset
    }

    // Relasi dengan teknisi (pegawai)
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'teknisi_id'); // Relasi dengan pegawai sebagai teknisi
    }

    // Relasi dengan workorder
    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id'); // Relasi dengan workorder
    }
}