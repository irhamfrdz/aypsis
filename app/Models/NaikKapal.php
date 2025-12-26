<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class NaikKapal extends Model
{
    use HasFactory, Auditable;

    protected $table = 'naik_kapal';

    protected $fillable = [
        'prospek_id',
        'nomor_kontainer',
        'jenis_barang',
        'no_seal',
        'tipe_kontainer',
        'size_kontainer',
        'ukuran_kontainer',
        'nama_kapal',
        'no_voyage',
        'pelabuhan_asal',
        'pelabuhan_tujuan',
        'tanggal_muat',
        'jam_muat',
        'total_volume',
        'total_tonase',
        'kuantitas',
        'sudah_ob',
        'supir_id',
        'tanggal_ob',
        'catatan_ob',
        'is_tl',
        // 'status', // Kolom tidak ada di table
        'keterangan',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'tanggal_muat',
        'tanggal_ob',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal_muat' => 'date',
        'tanggal_ob' => 'datetime',
        'jam_muat' => 'datetime:H:i',
        'total_volume' => 'decimal:3',
        'total_tonase' => 'decimal:3',
        'sudah_ob' => 'boolean',
        'is_tl' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_MENUNGGU = 'menunggu';
    const STATUS_DIMUAT = 'dimuat';
    const STATUS_SELESAI = 'selesai';
    const STATUS_BATAL = 'batal';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_MENUNGGU => 'Menunggu',
            self::STATUS_DIMUAT => 'Sedang Dimuat',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_BATAL => 'Batal'
        ];
    }

    // Relationships
    public function prospek()
    {
        return $this->belongsTo(Prospek::class);
    }

    public function supir()
    {
        return $this->belongsTo(Karyawan::class, 'supir_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByKapal($query, $kapal)
    {
        return $query->where('nama_kapal', $kapal);
    }

    public function scopeByTanggalMuat($query, $tanggal)
    {
        return $query->whereDate('tanggal_muat', $tanggal);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }

    public function getTanggalMuatFormattedAttribute()
    {
        return $this->tanggal_muat ? $this->tanggal_muat->format('d/m/Y') : '';
    }

    public function getFormattedVolumeAttribute()
    {
        return $this->total_volume ? rtrim(rtrim(number_format($this->total_volume, 3, '.', ','), '0'), '.') : '0';
    }

    public function getFormattedTonaseAttribute()
    {
        return $this->total_tonase ? rtrim(rtrim(number_format($this->total_tonase, 3, '.', ','), '0'), '.') : '0';
    }
}
