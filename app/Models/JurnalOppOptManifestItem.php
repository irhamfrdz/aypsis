<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalOppOptManifestItem extends Model
{
    protected $table = 'jurnal_opp_opt_manifest_items';

    protected $fillable = [
        'nomor_kontainer',
        'nomor_bl',
        'saldo',
        'kredit',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    public function jurnalOppOpt(): BelongsTo
    {
        return $this->belongsTo(JurnalOppOpt::class, 'jurnal_opp_opt_id');
    }
}
