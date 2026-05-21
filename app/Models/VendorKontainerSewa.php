<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class VendorKontainerSewa extends Model
{
    use Auditable;

    protected $table = 'vendor_kontainer_sewas';

    protected $fillable = [
        'name',
        'npwp',
        'tax_ppn_percent',
        'tax_pph_percent',
    ];

    protected $casts = [
        'tax_ppn_percent' => 'decimal:2',
        'tax_pph_percent' => 'decimal:2',
    ];
}
