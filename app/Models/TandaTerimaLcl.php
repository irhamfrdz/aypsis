<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class TandaTerimaLcl extends Model
{
    use HasFactory, Auditable, SoftDeletes;
    
    protected $table = 'tanda_terimas_lcl';
    
    protected $fillable = [
        'nomor_tanda_terima',
        'tanggal_tanda_terima', 
        'no_surat_jalan_customer',
        'term_id',
        // Single Penerima
        'nama_penerima',
        'pic_penerima',
        'telepon_penerima',
        'alamat_penerima',
        // Single Pengirim
        'nama_pengirim',
        'pic_pengirim',
        'telepon_pengirim',
        'alamat_pengirim',
        // Supir & Pengiriman
        'supir',
        'no_plat',
        'tujuan_pengiriman_id',
        // Upload
        'gambar_surat_jalan',
        // Status
        'status',
        'kegiatan',
        // Audit
        'created_by',
        'updated_by'
    ];
    
    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'gambar_surat_jalan' => 'array',
    ];
    
    // Relationships
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
    
    public function tujuanPengiriman(): BelongsTo
    {
        return $this->belongsTo(MasterTujuanKirim::class, 'tujuan_pengiriman_id');
    }
    
    // Alias for backward compatibility
    public function tujuanKirim(): BelongsTo
    {
        return $this->belongsTo(MasterTujuanKirim::class, 'tujuan_pengiriman_id');
    }
    
    // Items/Dimensi relationship
    public function items(): HasMany
    {
        return $this->hasMany(TandaTerimaLclItem::class, 'tanda_terima_lcl_id');
    }
    
    // Kontainer pivot relationship
    public function kontainerPivot(): HasMany
    {
        return $this->hasMany(TandaTerimaLclKontainerPivot::class, 'tanda_terima_lcl_id');
    }
    
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    // Helper methods
    public function getTotalVolumeAttribute(): float
    {
        return $this->items->sum('meter_kubik') ?? 0;
    }
    
    public function getTotalWeightAttribute(): float
    {
        return $this->items->sum('tonase') ?? 0;
    }
    
    public function getTotalKoliAttribute(): int
    {
        return $this->items->sum('jumlah_koli') ?? 0;
    }
    
    public function getFormattedNumberAttribute(): string
    {
        return $this->nomor_tanda_terima ?? 'TT-LCL-' . $this->id;
    }
    
    /**
     * Get nomor kontainer from pivot (latest assignment)
     */
    public function getNomorKontainerAttribute()
    {
        return $this->kontainerPivot()->latest()->first()?->nomor_kontainer;
    }
    
    /**
     * Get all tanda terima that share the same container
     * Mendukung 1 kontainer bisa memiliki banyak tanda terima LCL
     */
    public function getTandaTerimaSeKontainerAttribute()
    {
        $pivot = $this->kontainerPivot()->latest()->first();
        if (!$pivot || !$pivot->nomor_kontainer) {
            return collect([]);
        }
        
        // Get all tanda terima IDs with same container number
        $tandaTerimaIds = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $pivot->nomor_kontainer)
            ->where('tanda_terima_lcl_id', '!=', $this->id)
            ->pluck('tanda_terima_lcl_id');
        
        return static::whereIn('id', $tandaTerimaIds)
            ->with(['items', 'tujuanPengiriman', 'kontainerPivot'])
            ->get();
    }
    
    /**
     * Get grouped statistics for container
     */
    public function getKontainerStatsAttribute()
    {
        $pivot = $this->kontainerPivot()->latest()->first();
        if (!$pivot || !$pivot->nomor_kontainer) {
            return null;
        }
        
        // Get all tanda terima IDs with same container
        $tandaTerimaIds = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $pivot->nomor_kontainer)
            ->pluck('tanda_terima_lcl_id');
        
        $allInContainer = static::whereIn('id', $tandaTerimaIds)
            ->with('items')
            ->get();
        
        return [
            'total_tanda_terima' => $allInContainer->count(),
            'total_volume' => $allInContainer->sum(function($tt) {
                return $tt->items->sum('meter_kubik');
            }),
            'total_berat' => $allInContainer->sum(function($tt) {
                return $tt->items->sum('tonase');
            }),
            'total_koli' => $allInContainer->sum(function($tt) {
                return $tt->items->sum('jumlah_koli');
            }),
        ];
    }
    
    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
    }
    
    public function scopeByKontainer($query, $nomorKontainer)
    {
        return $query->whereHas('kontainerPivot', function($q) use ($nomorKontainer) {
            $q->where('nomor_kontainer', $nomorKontainer);
        });
    }
    
    public function scopeHasKontainer($query)
    {
        return $query->whereHas('kontainerPivot', function($q) {
            $q->whereNotNull('nomor_kontainer')
              ->where('nomor_kontainer', '!=', '');
        });
    }
    
    public function scopeNoKontainer($query)
    {
        return $query->whereDoesntHave('kontainerPivot')
                     ->orWhereHas('kontainerPivot', function($q) {
                         $q->whereNull('nomor_kontainer')
                           ->orWhere('nomor_kontainer', '');
                     });
    }
}
