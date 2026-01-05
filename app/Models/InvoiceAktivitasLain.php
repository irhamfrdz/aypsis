<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class InvoiceAktivitasLain extends Model
{
    use HasFactory, Auditable;

    protected $table = 'invoice_aktivitas_lain';

    protected $fillable = [
        'nomor_invoice',
        'tanggal_invoice',
        'jenis_aktivitas',
        'sub_jenis_kendaraan',
        'nomor_polisi',
        'nomor_voyage',
        'bl_id',
        'klasifikasi_biaya_id',
        'barang_detail',
        'surat_jalan_id',
        'jenis_penyesuaian',
        'tipe_penyesuaian',
        'penerima',
        'total',
        'status',
        'deskripsi',
        'catatan',
        'keterangan',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'total' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    /**
     * Relationship dengan User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan User (approved_by)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship dengan SuratJalan
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    /**
     * Relationship dengan Pembayaran Invoice (many-to-many)
     */
    public function pembayarans()
    {
        return $this->belongsToMany(
            PembayaranInvoiceAktivitasLain::class,
            'invoice_aktivitas_lain_pembayaran',
            'invoice_aktivitas_lain_id',
            'pembayaran_invoice_aktivitas_lain_id'
        )->withPivot('jumlah_dibayar')
          ->withTimestamps();
    }

    /**
     * Relationship dengan BL
     */
    public function bl()
    {
        return $this->belongsTo(Bl::class, 'bl_id');
    }

    /**
     * Relationship dengan KlasifikasiBiaya
     */
    public function klasifikasiBiaya()
    {
        return $this->belongsTo(KlasifikasiBiaya::class, 'klasifikasi_biaya_id');
    }

    /**
     * Accessor untuk barang detail dengan join pricelist_buruh
     */
    public function getBarangDetailArrayAttribute()
    {
        if (!$this->barang_detail) {
            return [];
        }

        $barangDetail = json_decode($this->barang_detail, true);
        if (!is_array($barangDetail)) {
            return [];
        }

        $result = [];
        foreach ($barangDetail as $item) {
            if (isset($item['pricelist_buruh_id'])) {
                $pricelist = \App\Models\PricelistBuruh::find($item['pricelist_buruh_id']);
                if ($pricelist) {
                    $result[] = [
                        'pricelist_buruh_id' => $item['pricelist_buruh_id'],
                        'jumlah' => $item['jumlah'] ?? 0,
                        'nama_barang' => $pricelist->nama_barang,
                        'size' => $pricelist->size,
                        'tipe' => $pricelist->tipe,
                        'tarif' => $pricelist->tarif,
                        'subtotal' => ($item['jumlah'] ?? 0) * $pricelist->tarif
                    ];
                }
            }
        }

        return $result;
    }
}
