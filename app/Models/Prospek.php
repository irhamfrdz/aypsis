<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Models\User;

class Prospek extends Model
{
    use HasFactory, Auditable;

    protected $table = 'prospek';

    protected $fillable = [
        'tanggal',
        'nama_supir',
        'barang',
        'pt_pengirim',
        'ukuran',
        'tipe',
        'no_surat_jalan',
        'surat_jalan_id',
        'nomor_kontainer',
        'no_seal',
        'tujuan_pengiriman',
        'nama_kapal',
        'keterangan',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'tanggal',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_AKTIF = 'aktif';
    const STATUS_SUDAH_MUAT = 'sudah_muat';
    const STATUS_BATAL = 'batal';

    // Ukuran constants
    const UKURAN_20 = '20';
    const UKURAN_40 = '40';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_SUDAH_MUAT => 'Sudah Muat',
            self::STATUS_BATAL => 'Batal'
        ];
    }

    public static function getUkuranOptions()
    {
        return [
            self::UKURAN_20 => '20 Feet',
            self::UKURAN_40 => '40 Feet'
        ];
    }

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeByTujuan($query, $tujuan)
    {
        return $query->where('tujuan_pengiriman', $tujuan);
    }

    public function scopeByUkuran($query, $ukuran)
    {
        return $query->where('ukuran', $ukuran);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }

    public function getUkuranLabelAttribute()
    {
        $ukurans = self::getUkuranOptions();
        return $ukurans[$this->ukuran] ?? $this->ukuran;
    }

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d/m/Y') : '';
    }
}
