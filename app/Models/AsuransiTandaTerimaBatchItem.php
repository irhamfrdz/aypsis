<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsuransiTandaTerimaBatchItem extends Model
{
    use HasFactory;

    protected $table = 'asuransi_tanda_terima_batch_items';

    protected $fillable = [
        'batch_id',
        'receipt_type',
        'tanda_terima_id',
        'tanda_terima_tanpa_sj_id',
        'tanda_terima_lcl_id',
        'nilai_pertanggungan',
    ];

    protected $casts = [
        'nilai_pertanggungan' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(AsuransiTandaTerimaBatch::class, 'batch_id');
    }

    public function tandaTerima()
    {
        return $this->belongsTo(TandaTerima::class, 'tanda_terima_id');
    }

    public function tandaTerimaTanpaSj()
    {
        return $this->belongsTo(TandaTerimaTanpaSuratJalan::class, 'tanda_terima_tanpa_sj_id');
    }

    public function tandaTerimaLcl()
    {
        return $this->belongsTo(TandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }
    
    public function getReceiptNumberAttribute()
    {
        if ($this->receipt_type == 'tt' && $this->tandaTerima) return $this->tandaTerima->no_surat_jalan;
        if ($this->receipt_type == 'tttsj' && $this->tandaTerimaTanpaSj) return $this->tandaTerimaTanpaSj->no_tanda_terima;
        if ($this->receipt_type == 'lcl' && $this->tandaTerimaLcl) return $this->tandaTerimaLcl->nomor_tanda_terima;
        return '-';
    }
}
