<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManifestSugeng extends Model
{
    protected $fillable = [
        'source_type', 'source_id', 'nama_kapal', 'no_voyage', 'nomor_bl', 'no_seal',
        'tipe_kontainer', 'size_kontainer', 'perincian_barang',
        'nomor_tanda_terima', 'tanggal_tanda_terima',
        'nomor_kontainer', 'pengirim', 'tujuan_pengiriman', 'shipper_jb_id',
        'manifest_goods', 'dikirim_oleh', 'dikirim_pada',
        'sheet_values', 'belum_selesai',
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'manifest_goods' => 'array',
        'perincian_barang' => 'array',
        'sheet_values' => 'array',
        'dikirim_pada' => 'datetime',
        'belum_selesai' => 'boolean',
    ];

    public function shipperJb()
    {
        return $this->belongsTo(ShipperConsignee::class, 'shipper_jb_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'dikirim_oleh');
    }
}
