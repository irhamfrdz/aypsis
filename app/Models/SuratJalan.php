<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Traits\Auditable;

class SuratJalan extends Model
{
    use HasFactory, Auditable;

    use Auditable;
    protected $table = 'surat_jalans';

    protected $fillable = [
        'order_id',
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
        'karyawan',
        'supir',
        'supir2',
        'no_plat',
        'kenek',
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
        'jumlah_terbayar'
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'tanggal_muat' => 'date',
        'tanggal_tanda_terima' => 'date',
        'input_date' => 'datetime',
        'waktu_berangkat' => 'datetime',
        'uang_jalan' => 'decimal:2',
        'total_tarif' => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
        'jumlah_retur' => 'integer',
        'karton' => 'integer',
        'plastik' => 'integer',
        'terpal' => 'integer',
        'rit' => 'integer',
    ];

    protected $dates = [
        'tanggal_surat_jalan',
        'tanggal_muat',
        'tanggal_tanda_terima',
        'input_date',
        'waktu_berangkat',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function pengirimRelation()
    {
        return $this->belongsTo(Pengirim::class, 'pengirim', 'id');
    }

    public function jenisBarangRelation()
    {
        return $this->belongsTo(JenisBarang::class, 'jenis_barang', 'id');
    }

    public function tujuanPengambilanRelation()
    {
        return $this->belongsTo(TujuanKegiatanUtama::class, 'tujuan_pengambilan', 'id');
    }

    public function tujuanPengirimanRelation()
    {
        return $this->belongsTo(TujuanKegiatanUtama::class, 'tujuan_pengiriman', 'id');
    }

    public function termRelation()
    {
        return $this->belongsTo(Term::class, 'term', 'id');
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by', 'id');
    }

    public function supirKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'supir', 'nama_panggilan');
    }

    public function kenekKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'kenek', 'nama_lengkap');
    }
    
    // Alternative: Get supir NIK with fallback
    public function getSupirNikAttribute()
    {
        // Try nama_panggilan first
        $karyawan = Karyawan::where('nama_panggilan', $this->supir)->first();
        if (!$karyawan) {
            // Fallback to nama_lengkap
            $karyawan = Karyawan::where('nama_lengkap', $this->supir)->first();
        }
        return $karyawan ? $karyawan->nik : null;
    }
    
    // Alternative: Get kenek NIK with fallback
    public function getKenekNikAttribute()
    {
        // Try nama_lengkap first
        $karyawan = Karyawan::where('nama_lengkap', $this->kenek)->first();
        if (!$karyawan) {
            // Fallback to nama_panggilan
            $karyawan = Karyawan::where('nama_panggilan', $this->kenek)->first();
        }
        return $karyawan ? $karyawan->nik : null;
    }



    public function pranotaSuratJalan()
    {
        return $this->belongsToMany(PranotaSuratJalan::class, 'pranota_surat_jalan_items', 'surat_jalan_id', 'pranota_surat_jalan_id');
    }

    public function pranotaUangRit()
    {
        return $this->hasMany(PranotaUangRit::class);
    }

    // Helper method to check if this surat jalan already has a pranota uang rit
    public function hasPranotaUangRit()
    {
        return $this->pranotaUangRit()->whereNotIn('status', ['cancelled'])->exists();
    }

    // Helper method to check if this surat jalan uses rit
    public function usesRit()
    {
        return $this->rit === 'menggunakan_rit';
    }

    // Status pembayaran uang rit constants
    const STATUS_UANG_RIT_BELUM_DIBAYAR = 'belum_dibayar';
    const STATUS_UANG_RIT_PROSES_PRANOTA = 'proses_pranota';
    const STATUS_UANG_RIT_SUDAH_MASUK_PRANOTA = 'sudah_masuk_pranota';
    const STATUS_UANG_RIT_PRANOTA_SUBMITTED = 'pranota_submitted';
    const STATUS_UANG_RIT_PRANOTA_APPROVED = 'pranota_approved';
    const STATUS_UANG_RIT_DIBAYAR = 'dibayar';

    public static function getStatusPembayaranUangRitOptions()
    {
        return [
            self::STATUS_UANG_RIT_BELUM_DIBAYAR => 'Belum Dibayar',
            self::STATUS_UANG_RIT_PROSES_PRANOTA => 'Proses Pranota',
            self::STATUS_UANG_RIT_SUDAH_MASUK_PRANOTA => 'Sudah Masuk Pranota',
            self::STATUS_UANG_RIT_PRANOTA_SUBMITTED => 'Pranota Submitted',
            self::STATUS_UANG_RIT_PRANOTA_APPROVED => 'Pranota Approved',
            self::STATUS_UANG_RIT_DIBAYAR => 'Dibayar'
        ];
    }

    public function getStatusPembayaranUangRitLabelAttribute()
    {
        $statuses = self::getStatusPembayaranUangRitOptions();
        return $statuses[$this->status_pembayaran_uang_rit] ?? $this->status_pembayaran_uang_rit;
    }

    // Status pembayaran uang rit kenek constants
    const STATUS_UANG_RIT_KENEK_BELUM_DIBAYAR = 'belum_dibayar';
    const STATUS_UANG_RIT_KENEK_PROSES_PRANOTA = 'proses_pranota';
    const STATUS_UANG_RIT_KENEK_SUDAH_MASUK_PRANOTA = 'sudah_masuk_pranota';
    const STATUS_UANG_RIT_KENEK_PRANOTA_SUBMITTED = 'pranota_submitted';
    const STATUS_UANG_RIT_KENEK_PRANOTA_APPROVED = 'pranota_approved';
    const STATUS_UANG_RIT_KENEK_DIBAYAR = 'dibayar';

    public static function getStatusPembayaranUangRitKenekOptions()
    {
        return [
            self::STATUS_UANG_RIT_KENEK_BELUM_DIBAYAR => 'Belum Dibayar',
            self::STATUS_UANG_RIT_KENEK_PROSES_PRANOTA => 'Proses Pranota',
            self::STATUS_UANG_RIT_KENEK_SUDAH_MASUK_PRANOTA => 'Sudah Masuk Pranota',
            self::STATUS_UANG_RIT_KENEK_PRANOTA_SUBMITTED => 'Pranota Submitted',
            self::STATUS_UANG_RIT_KENEK_PRANOTA_APPROVED => 'Pranota Approved',
            self::STATUS_UANG_RIT_KENEK_DIBAYAR => 'Dibayar'
        ];
    }

    public function getStatusPembayaranUangRitKenekLabelAttribute()
    {
        $statuses = self::getStatusPembayaranUangRitKenekOptions();
        return $statuses[$this->status_pembayaran_uang_rit_kenek] ?? $this->status_pembayaran_uang_rit_kenek;
    }



    public function hasPranota()
    {
        return $this->pranotaSuratJalan()->exists();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal_surat_jalan', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getFormattedTanggalSuratJalanAttribute()
    {
        return $this->tanggal_surat_jalan ? $this->tanggal_surat_jalan->format('d-m-Y') : '-';
    }

    public function getFormattedTanggalMuatAttribute()
    {
        return $this->tanggal_muat ? $this->tanggal_muat->format('d-m-Y') : '-';
    }

    public function getFormattedUangJalanAttribute()
    {
        return $this->uang_jalan ? 'Rp ' . number_format($this->uang_jalan, 0, ',', '.') : 'Rp 0';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'active' => 'bg-green-100 text-green-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get formatted total tarif
     */
    public function getFormattedTotalTarifAttribute()
    {
        return $this->total_tarif ? 'Rp ' . number_format((float) $this->total_tarif, 0, ',', '.') : 'Rp 0';
    }

    /**
     * Get formatted jumlah terbayar
     */
    public function getFormattedJumlahTerbayarAttribute()
    {
        return $this->jumlah_terbayar ? 'Rp ' . number_format((float) $this->jumlah_terbayar, 0, ',', '.') : 'Rp 0';
    }

    /**
     * Get sisa pembayaran
     */
    public function getSisaPembayaranAttribute()
    {
        $total = (float) ($this->total_tarif ?? 0);
        $terbayar = (float) ($this->jumlah_terbayar ?? 0);
        return $total - $terbayar;
    }

    /**
     * Get formatted sisa pembayaran
     */
    public function getFormattedSisaPembayaranAttribute()
    {
        return 'Rp ' . number_format($this->sisa_pembayaran, 0, ',', '.');
    }

    /**
     * Get status pembayaran badge
     */
    public function getStatusPembayaranBadgeAttribute()
    {
        $badges = [
            'belum_dibayar' => 'bg-red-100 text-red-800',
            'sudah_dibayar' => 'bg-green-100 text-green-800',
        ];

        return $badges[$this->status_pembayaran] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status pembayaran label
     */
    public function getStatusPembayaranLabelAttribute()
    {
        $labels = [
            'belum_dibayar' => 'Belum Dibayar',
            'sudah_dibayar' => 'Sudah Dibayar',
        ];

        return $labels[$this->status_pembayaran] ?? 'Unknown';
    }

    /**
     * Scopes for status pembayaran
     */
    public function scopeBelumDibayar($query)
    {
        return $query->where('status_pembayaran', 'belum_dibayar');
    }

    public function scopeSudahDibayar($query)
    {
        return $query->where('status_pembayaran', 'sudah_dibayar');
    }

    /**
     * Update status pembayaran based on payment amount
     */
    public function updateStatusPembayaran($status = null, $totalTarif = null, $jumlahTerbayar = null)
    {
        // Update nilai jika diberikan parameter
        if ($totalTarif !== null) {
            $this->total_tarif = $totalTarif;
        }

        if ($jumlahTerbayar !== null) {
            $this->jumlah_terbayar = $jumlahTerbayar;
        }

        // Jika status diberikan langsung, gunakan itu
        if ($status !== null) {
            $this->status_pembayaran = $status;
        } else {
            // Otomatis hitung berdasarkan total dan terbayar
            $total = (float) ($this->total_tarif ?? 0);
            $terbayar = (float) ($this->jumlah_terbayar ?? 0);

            if ($terbayar >= $total && $total > 0) {
                $this->status_pembayaran = 'sudah_dibayar';
            } else {
                $this->status_pembayaran = 'belum_dibayar';
            }
        }

        $this->save();
    }

    /**
     * Accessor for nomor_kontainer (alias for no_kontainer)
     */
    public function getNomorKontainerAttribute()
    {
        return $this->no_kontainer;
    }

    /**
     * Accessor for nomor_seal (alias for no_seal)
     */
    public function getNomorSealAttribute()
    {
        return $this->no_seal;
    }
}
