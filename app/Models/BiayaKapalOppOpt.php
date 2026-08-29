<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalOppOpt extends Model
{
    protected $table = 'biaya_kapal_opp_opts';

    protected $fillable = [
        'biaya_kapal_id',
        'klasifikasi_biaya_id',
        'klasifikasi',
        'pricelist_opp_opt_id',
        'kapal',
        'voyage',
        'vendor',
        'jenis_ukuran',
        'catatan',
        'jumlah',
        'tarif',
        'subtotal',
        'total_nominal',
        'dp',
        'sisa_pembayaran',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_nominal' => 'decimal:2',
        'dp' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
    ];

    // Relationship to BiayaKapal
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
    
    // Relationship to KlasifikasiBiaya
    public function klasifikasiBiaya()
    {
        return $this->belongsTo(KlasifikasiBiaya::class, 'klasifikasi_biaya_id');
    }

    // Relationship to PricelistOppOpt
    public function pricelistOppOpt()
    {
        return $this->belongsTo(PricelistOppOpt::class, 'pricelist_opp_opt_id');
    }

    // Relationship to Manifests (Many-to-Many via Pivot)
    public function manifests()
    {
        return $this->belongsToMany(Manifest::class, 'biaya_kapal_opp_opt_manifest', 'opp_opt_id', 'manifest_id')
            ->withTimestamps();
    }

}
