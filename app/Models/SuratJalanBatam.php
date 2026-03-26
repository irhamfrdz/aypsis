<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class SuratJalanBatam extends Model
{
    use HasFactory, Auditable;

    protected $table = 'surat_jalan_batams';

    protected $fillable = [
        'order_batam_id',
        'penerima_id',
        'notify_party_id',
        'alamat_penerima',
        'tanggal_surat_jalan',
        'no_surat_jalan',
        'kegiatan',
        'pengirim',
        'alamat',
        'telp',
        'jenis_barang',
        'tujuan_pengambilan',
        'retur_barang',
        'jumlah_retur',
        'supir',
        'supir2',
        'no_plat',
        'kenek',
        'krani',
        'tipe_kontainer',
        'no_kontainer',
        'no_seal',
        'size',
        'jumlah_kontainer',
        'karton',
        'plastik',
        'terpal',
        'waktu_berangkat',
        'tujuan_pengiriman',
        'tanggal_muat',
        'jam_berangkat',
        'term',
        'aktifitas',
        'rit',
        'uang_jalan',
        'tarif',
        'no_pemesanan',
        'gambar',
        'gambar_checkpoint',
        'input_by',
        'input_date',
        'status',
        'status_pembayaran',
        'status_pembayaran_uang_jalan',
        'status_pembayaran_uang_rit',
        'status_pembayaran_uang_rit_kenek',
        'tanggal_tanda_terima',
        'total_tarif',
        'jumlah_terbayar',
        'uang_rit_kenek',
        'gate_in_id',
        'status_gate_in',
        'tanggal_gate_in',
        'catatan_gate_in',
        'catatan_checkpoint',
        'tanggal_checkpoint',
        'is_supir_customer',
        'lembur',
        'nginap',
        'tidak_lembur_nginap'
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'tanggal_muat' => 'date',
        'tanggal_tanda_terima' => 'date',
        'tanggal_checkpoint' => 'date',
        'input_date' => 'datetime',
        'waktu_berangkat' => 'datetime',
        'is_supir_customer' => 'boolean',
        'lembur' => 'boolean',
        'nginap' => 'boolean',
        'tidak_lembur_nginap' => 'boolean',
    ];

    public function orderBatam()
    {
        return $this->belongsTo(OrderBatam::class, 'order_batam_id');
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function getFormattedTanggalSuratJalanAttribute()
    {
        return $this->tanggal_surat_jalan ? $this->tanggal_surat_jalan->format('d/m/Y') : '-';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'active' => 'bg-green-100 text-green-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'belum masuk checkpoint' => 'bg-yellow-100 text-yellow-800',
            'sudah_checkpoint' => 'bg-purple-100 text-purple-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getOverallStatusPembayaranAttribute()
    {
        if ($this->status_pembayaran === 'sudah_dibayar') {
            return 'sudah_dibayar';
        }
        
        if ($this->status_pembayaran_uang_jalan === 'dibayar') {
            return 'sudah_dibayar';
        } elseif ($this->status_pembayaran_uang_jalan === 'sudah_masuk_uang_jalan') {
            return 'belum_dibayar';
        } else {
            return 'belum_masuk_pranota';
        }
    }

    public function getVendorInvoiceStatusAttribute()
    {
        // Placeholder implementation until vendor payment module is linked
        return 'belum_tagihan';
    }

    /**
     * Relationship with UangJalanBatam
     */
    public function uangJalanBatam()
    {
        return $this->hasOne(UangJalanBatam::class, 'surat_jalan_batam_id');
    }
}
