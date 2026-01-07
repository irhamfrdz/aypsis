<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    protected $fillable = [
        'nomor_bl',
        'nomor_manifest',
        'nomor_tanda_terima',
        'prospek_id',
        'nomor_kontainer',
        'no_seal',
        'tipe_kontainer',
        'size_kontainer',
        'no_voyage',
        'pelabuhan_asal',
        'pelabuhan_tujuan',
        'pelabuhan_muat',
        'pelabuhan_bongkar',
        'nama_kapal',
        'tanggal_berangkat',
        'nama_barang',
        'asal_kontainer',
        'ke',
        'pengirim',
        'alamat_pengirim',
        'penerima',
        'alamat_penerima',
        'alamat_pengiriman',
        'contact_person',
        'tonnage',
        'volume',
        'satuan',
        'term',
        'kuantitas',
        'penerimaan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'penerimaan' => 'date',
        'tonnage' => 'decimal:3',
        'volume' => 'decimal:3',
        'kuantitas' => 'integer',
    ];

    // Relationships
    public function prospek()
    {
        return $this->belongsTo(Prospek::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function suratJalanBongkaran()
    {
        return $this->hasOne(SuratJalanBongkaran::class, 'manifest_id');
    }
}
