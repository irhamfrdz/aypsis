<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bl extends Model
{
    use HasFactory;

    protected $table = 'bls';

    protected $fillable = [
        'prospek_id',
        'nomor_bl',
        'nomor_kontainer',
        'no_seal',
        'tipe_kontainer',
        'no_voyage',
        'nama_kapal',
        'nama_barang',
        'penerima',
        'alamat_pengiriman',
        'contact_person',
        'tonnage',
        'volume',
        'satuan',
        'term',
        'kuantitas',
        'supir_ob',
        'status_bongkar',
        'sudah_ob',
    ];

    protected $casts = [
        'sudah_ob' => 'boolean',
    ];

    /**
     * Get the prospek that owns the BL.
     */
    public function prospek()
    {
        return $this->belongsTo(Prospek::class);
    }
}
