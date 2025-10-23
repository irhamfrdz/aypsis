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
        'total_tarif',
        'jumlah_terbayar'
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
        'karton' => 'integer',
        'plastik' => 'integer',
        'terpal' => 'integer',
        'rit' => 'integer',
    ];

    protected $dates = [
        'tanggal_surat_jalan',
        'tanggal_muat',
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

    public function approvals()
    {
        return $this->hasMany(SuratJalanApproval::class);
    }

    public function pranotaSuratJalan()
    {
        return $this->belongsToMany(PranotaSuratJalan::class, 'pranota_surat_jalan_items', 'surat_jalan_id', 'pranota_surat_jalan_id');
    }

    // Helper methods for approval status
    public function getApprovalStatus($level)
    {
        $approval = $this->approvals()->where('approval_level', $level)->first();
        return $approval ? $approval->status : 'pending';
    }

    public function isApprovedByLevel($level)
    {
        return $this->getApprovalStatus($level) === 'approved';
    }

    public function isFullyApproved()
    {
        return $this->isApprovedByLevel('tugas-1') && $this->isApprovedByLevel('tugas-2');
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
}
