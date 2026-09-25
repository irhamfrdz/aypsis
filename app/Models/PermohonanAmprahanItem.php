<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanAmprahanItem extends Model
{
    use HasFactory;

    protected $table = 'permohonan_amprahan_items';

    // Disable timestamps since migration doesn't have them
    public $timestamps = false;

    protected $fillable = [
        'permohonan_id',
        'nama_barang',
        'link_barang',
        'jumlah',
        'jumlah_disetujui',
        'satuan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'jumlah_disetujui' => 'decimal:2',
    ];

    public function permohonan()
    {
        return $this->belongsTo(PermohonanAmprahan::class, 'permohonan_id');
    }
}
