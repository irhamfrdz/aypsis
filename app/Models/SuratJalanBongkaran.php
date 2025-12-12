<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Traits\Auditable;
use App\Models\MasterKapal;

class SuratJalanBongkaran extends Model
{
    use HasFactory, Auditable;

    protected $table = 'surat_jalan_bongkarans';

    protected $fillable = [
        'tanggal_surat_jalan',
        'lanjut_muat',
        'nomor_sj_sebelumnya',
        'nomor_surat_jalan',
        'kegiatan',
        'pengirim',
        'jenis_barang',
        'tujuan_alamat',
        'telp',
        'tujuan_pengambilan',
        'retur_barang',
        'jumlah_retur',
        'karyawan',
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
        'rit',
        'uang_jalan',
        'uang_jalan_type',
        'uang_jalan_nominal',
        'tagihan_ayp',
        'tagihan_atb',
        'tagihan_pb',
        'no_pemesanan',
        'gambar',
        'gambar_checkpoint',
        'input_by',
        'input_date',
        'status',
        'status_pembayaran',
        'status_pembayaran_uang_rit',
        'status_pembayaran_uang_rit_kenek',
        'total_tarif',
        'jumlah_terbayar',
        'aktifitas',
        'nama_kapal',
        'kapal_id',
        'no_voyage',
        'no_bl',
        'bl_id',
        'jenis_pengiriman',
        'tanggal_ambil_barang'
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'tanggal_muat' => 'date',
        'input_date' => 'datetime',
        'waktu_berangkat' => 'datetime',
        'uang_jalan' => 'decimal:2',
        'total_tarif' => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
        'jumlah_retur' => 'integer',
        'jumlah_kontainer' => 'integer',
    ];

    protected $dates = [
        'tanggal_surat_jalan',
        'tanggal_muat',
        'input_date',
        'waktu_berangkat',
    ];

    // Relationships
    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function bl()
    {
        return $this->belongsTo(\App\Models\Bl::class, 'bl_id');
    }

    /**
     * Relationship dengan Uang Jalan Bongkaran (bisa ada lebih dari satu)
     */
    public function uangJalanBongkarans()
    {
        return $this->hasMany(\App\Models\UangJalanBongkaran::class, 'surat_jalan_bongkaran_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_surat_jalan', [$startDate, $endDate]);
    }

    // Accessors
    public function getFormattedTanggalSuratJalanAttribute()
    {
        return $this->tanggal_surat_jalan ? Carbon::parse($this->tanggal_surat_jalan)->format('d/m/Y') : null;
    }

    public function getFormattedTanggalMuatAttribute()
    {
        return $this->tanggal_muat ? Carbon::parse($this->tanggal_muat)->format('d/m/Y') : null;
    }

    public function getFormattedWaktuBerangkatAttribute()
    {
        return $this->waktu_berangkat ? Carbon::parse($this->waktu_berangkat)->format('d/m/Y H:i') : null;
    }

    public function getStatusBadgeAttribute()
    {
        $statusClasses = [
            'draft' => 'bg-gray-100 text-gray-800',
            'active' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'belum masuk checkpoint' => 'bg-yellow-100 text-yellow-800',
            'sudah masuk checkpoint' => 'bg-indigo-100 text-indigo-800',
            'gate in' => 'bg-purple-100 text-purple-800',
            'gate out' => 'bg-emerald-100 text-emerald-800',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-gray-100 text-gray-800';
        $text = ucfirst(str_replace('_', ' ', $this->status));

        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $class . '">' . $text . '</span>';
    }

    public function getStatusPembayaranBadgeAttribute()
    {
        if (!$this->status_pembayaran) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }

        $statusClasses = [
            'belum_bayar' => 'bg-red-100 text-red-800',
            'sebagian' => 'bg-yellow-100 text-yellow-800',
            'lunas' => 'bg-green-100 text-green-800',
        ];

        $statusTexts = [
            'belum_bayar' => 'Belum Bayar',
            'sebagian' => 'Sebagian',
            'lunas' => 'Lunas',
        ];

        $class = $statusClasses[$this->status_pembayaran] ?? 'bg-gray-100 text-gray-800';
        $text = $statusTexts[$this->status_pembayaran] ?? ucfirst($this->status_pembayaran);

        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $class . '">' . $text . '</span>';
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function calculateSisaTagihan()
    {
        return $this->total_tarif - $this->jumlah_terbayar;
    }

    // Mutators
    public function setTanggalSuratJalanAttribute($value)
    {
        $this->attributes['tanggal_surat_jalan'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setTanggalMuatAttribute($value)
    {
        $this->attributes['tanggal_muat'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setWaktuBerangkatAttribute($value)
    {
        $this->attributes['waktu_berangkat'] = $value ? Carbon::parse($value) : null;
    }

    public function setInputDateAttribute($value)
    {
        $this->attributes['input_date'] = $value ? Carbon::parse($value) : null;
    }
}
