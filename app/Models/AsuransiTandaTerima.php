<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsuransiTandaTerima extends Model
{
    use HasFactory;

    protected $table = 'asuransi_tanda_terimas';

    protected $fillable = [
        'vendor_asuransi_id',
        'tanda_terima_id',
        'tanda_terima_tanpa_sj_id',
        'tanda_terima_lcl_id',
        'nomor_polis',
        'tanggal_polis',
        'nilai_pertanggungan',
        'premi',
        'asuransi_path',
        'keterangan',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_polis' => 'date',
        'nilai_pertanggungan' => 'decimal:2',
        'premi' => 'decimal:2',
    ];

    /**
     * Relationship to Vendor Asuransi
     */
    public function vendorAsuransi()
    {
        return $this->belongsTo(VendorAsuransi::class, 'vendor_asuransi_id');
    }

    /**
     * Relationship to Tanda Terima
     */
    public function tandaTerima()
    {
        return $this->belongsTo(TandaTerima::class, 'tanda_terima_id');
    }

    /**
     * Relationship to Tanda Terima Tanpa Surat Jalan
     */
    public function tandaTerimaTanpaSj()
    {
        return $this->belongsTo(TandaTerimaTanpaSuratJalan::class, 'tanda_terima_tanpa_sj_id');
    }

    /**
     * Relationship to Tanda Terima LCL
     */
    public function tandaTerimaLcl()
    {
        return $this->belongsTo(TandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }

    /**
     * Get the source receipt object
     */
    public function getSourceAttribute()
    {
        if ($this->tanda_terima_id) return $this->tandaTerima;
        if ($this->tanda_terima_tanpa_sj_id) return $this->tandaTerimaTanpaSj;
        if ($this->tanda_terima_lcl_id) return $this->tandaTerimaLcl;
        return null;
    }

    /**
     * Get the source type name
     */
    public function getSourceTypeNameAttribute()
    {
        if ($this->tanda_terima_id) return 'Tanda Terima';
        if ($this->tanda_terima_tanpa_sj_id) return 'TT Tanpa SJ';
        if ($this->tanda_terima_lcl_id) return 'Tanda Terima LCL';
        return 'Unknown';
    }

    /**
     * Get the source number (manifest/SJ number)
     */
    public function getSourceNumberAttribute()
    {
        $source = $this->source;
        if (!$source) return '-';

        if ($this->tanda_terima_id) return $source->no_surat_jalan;
        if ($this->tanda_terima_tanpa_sj_id) return $source->no_tanda_terima;
        if ($this->tanda_terima_lcl_id) return $source->nomor_tanda_terima;
        
        return '-';
    }

    /**
     * Relationship to User (created by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to User (updated by)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
