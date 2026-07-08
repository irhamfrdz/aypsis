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
        'status_aktif',
    ];

    protected $casts = [
        'tax_ppn_percent' => 'decimal:2',
        'tax_pph_percent' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    public function kontainers()
    {
        return $this->hasMany(SkKontainer::class, 'vendor_id');
    }

    public function tarifs()
    {
        return $this->hasMany(SkTarifSewa::class, 'vendor_id');
    }

    public function sewas()
    {
        return $this->hasMany(SkSewa::class, 'vendor_id');
    }

    public function invoiceGrups()
    {
        return $this->hasMany(SkInvoiceGrup::class, 'vendor_id');
    }
}
