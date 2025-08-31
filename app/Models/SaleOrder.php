<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{

    protected $table = 'saleorder';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID
    protected $keyType = 'string';
    public $timestamps = true;

    // Pilih salah satu: pakai guarded kosong ATAU fillable.
    // Rekomendasi: pakai fillable biar eksplisit.
    protected $fillable = [
        'noso',        // <- ada di schema
        'tanggal',
        'total',
        'diskon',
        'grandtotal',
        'customer_id',
        'status',
        'bukti',
        'id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    /* =========================
     * Relasi
     * ========================= */

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // SALE ORDER <-> BARANG LINE (many-to-many via pivot saleorder_salebarangorderline)
    public function barangLines()
    {
        return $this->belongsToMany(
            SaleOrderBarangLine::class,
            'saleorder_salebarangorderline',
            'saleorder_id',            // FK ke header ini di pivot
            'saleorderbarangline_id'   // FK ke line barang di pivot
        )->withTimestamps();
    }

    // SALE ORDER <-> JASA LINE (many-to-many via pivot saleorder_salejasaorderline)
    public function jasaLines()
    {
        return $this->belongsToMany(
            SaleOrderJasaLine::class,
            'saleorder_salejasaorderline',
            'saleorder_id',            // FK ke header ini di pivot
            'saleorderjasaline_id'     // FK ke line jasa di pivot
        )->withTimestamps();
    }

    /* =========================
     * Quality-of-life helpers
     * ========================= */

    // Tambah line barang ke SO (helper opsional)
    public function attachBarangLine(string $lineId): void
    {
        $this->barangLines()->syncWithoutDetaching([$lineId]);
    }

    public function detachBarangLine(string $lineId): void
    {
        $this->barangLines()->detach($lineId);
    }

    public function attachJasaLine(string $lineId): void
    {
        $this->jasaLines()->syncWithoutDetaching([$lineId]);
    }

    public function detachJasaLine(string $lineId): void
    {
        $this->jasaLines()->detach($lineId);
    }
}
