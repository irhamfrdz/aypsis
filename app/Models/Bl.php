<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'size_kontainer',
        'no_voyage',
        'nama_kapal',
        'pelabuhan_asal',
        'pelabuhan_tujuan',
        'nama_barang',
        'pengirim',
        'penerima',
        'alamat_pengiriman',
        'contact_person',
        'tonnage',
        'volume',
        'satuan',
        'term',
        'kuantitas',
        'supir_ob',
        'supir_id',
        'tanggal_ob',
        'catatan_ob',
        'status_bongkar',
        'sudah_ob',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sudah_ob' => 'boolean',
        'tanggal_ob' => 'datetime',
    ];

    /**
     * Get the prospek that owns the BL.
     */
    public function prospek()
    {
        return $this->belongsTo(Prospek::class);
    }

    /**
     * Get the surat jalan bongkaran for this BL.
     */
    public function suratJalanBongkaran()
    {
        return $this->hasOne(SuratJalanBongkaran::class, 'bl_id');
    }

    /**
     * Get the supir for this BL.
     */
    public function supir()
    {
        return $this->belongsTo(Karyawan::class, 'supir_id', 'id');
    }

    /**
     * Get the user who created this BL.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this BL.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
