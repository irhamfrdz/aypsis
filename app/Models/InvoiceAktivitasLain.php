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
        'invoice_vendor',
        'tanggal_invoice',
        'jenis_aktivitas',
        'klasifikasi_biaya_umum_id',
        'referensi',
        'sub_jenis_kendaraan',
        'nomor_polisi',
        'nomor_voyage',
        'bl_details',
        'klasifikasi_biaya_id',
        'barang_detail',
        'surat_jalan_id',
        'jenis_penyesuaian',
        'tipe_penyesuaian',
        'detail_pembayaran',
        'penerima',
        'total',
        'pph',
        'grand_total',
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
        'pph' => 'decimal:2',
        'grand_total' => 'decimal:2',
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
     * Alias for creator relationship
     */
    public function createdBy()
    {
        return $this->creator();
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
     * Accessor untuk bl details dengan join bls table
     */
    public function getBlDetailsArrayAttribute()
    {
        if (!$this->bl_details) {
            return [];
        }

        $blDetails = json_decode($this->bl_details, true);
        if (!is_array($blDetails)) {
            return [];
        }

        $result = [];
        foreach ($blDetails as $item) {
            // Support both bl_id (legacy) and manifest_id (new)
            if (isset($item['manifest_id'])) {
                $manifest = \App\Models\Manifest::find($item['manifest_id']);
                if ($manifest) {
                    $result[] = [
                        'manifest_id' => $item['manifest_id'],
                        'nomor_bl' => $manifest->nomor_bl,
                        'nomor_kontainer' => $manifest->nomor_kontainer,
                        'no_voyage' => $manifest->no_voyage,
                        'nama_kapal' => $manifest->nama_kapal,
                        'pengirim' => $manifest->pengirim
                    ];
                }
            } elseif (isset($item['bl_id'])) {
                // Legacy support: try Manifest table with bl_id
                $manifest = \App\Models\Manifest::find($item['bl_id']);
                if ($manifest) {
                    $result[] = [
                        'bl_id' => $item['bl_id'],
                        'nomor_bl' => $manifest->nomor_bl,
                        'nomor_kontainer' => $manifest->nomor_kontainer,
                        'no_voyage' => $manifest->no_voyage,
                        'nama_kapal' => $manifest->nama_kapal,
                        'pengirim' => $manifest->pengirim
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Relationship dengan KlasifikasiBiaya
     */
    public function klasifikasiBiaya()
    {
        return $this->belongsTo(KlasifikasiBiaya::class, 'klasifikasi_biaya_id');
    }

    /**
     * Relationship dengan KlasifikasiBiaya Umum (untuk dropdown jenis biaya)
     */
    public function klasifikasiBiayaUmum()
    {
        return $this->belongsTo(KlasifikasiBiaya::class, 'klasifikasi_biaya_umum_id');
    }

    /**
     * Relationship dengan Biaya Listrik
     */
    public function biayaListrik()
    {
        return $this->hasOne(InvoiceAktivitasLainListrik::class, 'invoice_aktivitas_lain_id');
    }

    /**
     * Alias untuk relationship biayaListrik
     */
    public function listrikData()
    {
        return $this->biayaListrik();
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
    
    /**
     * Accessor untuk detail pembayaran array
     */
    public function getDetailPembayaranArrayAttribute()
    {
        if (!$this->detail_pembayaran) {
            return [];
        }

        $detailPembayaran = json_decode($this->detail_pembayaran, true);
        if (!is_array($detailPembayaran)) {
            return [];
        }

        return $detailPembayaran;
    }
}
