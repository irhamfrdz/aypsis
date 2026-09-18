<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManifestShipperDetail extends Model
{
    protected $fillable = [
        'manifest_id', 'shipper_id', 'nomor_tanda_terima', 'nama_barang',
        'pengirim', 'alamat_pengirim', 'penerima', 'alamat_penerima',
        'alamat_pengiriman', 'contact_person', 'notify_party', 'alamat_notify_party',
        'tonnage', 'tonnage_perincian', 'volume', 'volume_perincian',
        'satuan', 'term', 'kuantitas', 'hs_code', 'penerimaan', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'tonnage' => 'decimal:3', 'tonnage_perincian' => 'decimal:3',
        'volume' => 'decimal:3', 'volume_perincian' => 'decimal:3',
        'kuantitas' => 'integer', 'penerimaan' => 'date',
    ];

    public function manifest()
    {
        return $this->belongsTo(Manifest::class);
    }

    public function shipperConsignee()
    {
        return $this->belongsTo(ShipperConsignee::class, 'shipper_id');
    }
}
