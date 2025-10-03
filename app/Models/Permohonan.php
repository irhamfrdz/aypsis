<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_memo',
        'kegiatan',
        'vendor_perusahaan',
        'supir_id',
        'krani_id', // Menggantikan 'kenek'
        'plat_nomor',
        'no_chasis',
        'ukuran',
        'dari',
        'ke',
        'jumlah_kontainer',
        'tanggal_memo',
        'jumlah_uang_jalan',
        'adjustment',
        'alasan_adjustment',
        'total_harga_setelah_adj',
        'catatan',
        'lampiran',
        'status',
        'approved_by_system_1',
        'approved_by_system_2',
    ];

    protected $casts = [
        'tanggal_memo' => 'date',
    ];

    /**
     * Relasi ke Karyawan sebagai Supir.
     */
    public function supir()
    {
        return $this->belongsTo(Karyawan::class, 'supir_id');
    }

    /**
     * Relasi ke Karyawan sebagai Krani.
     */
    public function krani()
    {
        return $this->belongsTo(Karyawan::class, 'krani_id');
    }

    /**
     * Relasi ke Checkpoints.
     */
    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class)->latest();
    }

    /**
     * Relasi ke Kontainer.
     */
    public function kontainers()
    {
        return $this->belongsToMany(Kontainer::class, 'permohonan_kontainers');
    }

    /**
     * Relasi ke PranotaSupir.
     */
    public function pranotas()
    {
        return $this->belongsToMany(PranotaSupir::class, 'pranota_permohonan', 'permohonan_id', 'pranota_supir_id');
    }

    /**
     * Accessor untuk tujuan yang menggabungkan dari dan ke.
     */
    public function getTujuanAttribute(): string
    {
        $dari = trim($this->dari ?? '');
        $ke = trim($this->ke ?? '');

        if (empty($dari) && empty($ke)) {
            return '-';
        }

        if (empty($dari)) {
            return "Ke: {$ke}";
        }

        if (empty($ke)) {
            return "Dari: {$dari}";
        }

        if ($dari === $ke) {
            return $dari;
        }

        return "{$dari} → {$ke}";
    }
}
