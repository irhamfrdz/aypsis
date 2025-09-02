<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'permohonan_id',
        'lokasi',
        'catatan',
        'surat_jalan_vendor',
        'status', // Disimpan untuk penggunaan di masa depan
        'tanggal_checkpoint',
    ];

    protected $casts = [
        'tanggal_checkpoint' => 'date',
    ];

    /**
     * Relasi ke Permohonan.
     */
    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }
}

