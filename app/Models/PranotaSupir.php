<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PranotaSupir extends Model
{
    use HasFactory, Auditable;

    protected $table = 'pranota_supirs';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'total_biaya_memo',
        'adjustment',
        'alasan_adjustment',
        'total_biaya_pranota',
        'catatan',
        'status_pembayaran',
    ];

    /**
     * Cast date attributes to Carbon instances so views can call ->format() safely.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_pranota' => 'date',
    ];

    public function permohonans()
    {
        // Asumsi nama pivot table dan foreign key sudah benar
        return $this->belongsToMany(Permohonan::class, 'pranota_permohonan', 'pranota_supir_id', 'permohonan_id');
    }

    /**
     * Pembayaran yang melunasi pranota ini.
     */
        public function pembayarans()
        {
            return $this->belongsToMany(PembayaranPranotaSupir::class, 'pembayaran_pranota_supir_pranota_supir', 'pranota_supir_id', 'pembayaran_pranota_supir_id');
        }
}
