<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalPelindo extends Model
{
    protected $fillable = ['biaya_kapal_id', 'nama_kapal', 'no_voyage', 'jenis_tagihan', 'status_kontainer', 'ukuran', 'jumlah', 'biaya'];

    public function getBarangLabelAttribute(): string
    {
        $labels = [
            'barang_berbahaya' => 'Barang Berbahaya',
            'lpg' => 'LPG',
            'besi_beton' => 'Besi Beton',
            'alat_berat' => 'Alat Berat',
        ];

        return ($this->ukuran ? $this->ukuran.'ft ' : '')
            .($labels[$this->status_kontainer] ?? ucfirst($this->status_kontainer ?? ''));
    }

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }
}
