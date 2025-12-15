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
        'nomor_kontainer',
        'size_kontainer',
        'tipe_kontainer',
        'nama_barang',
        'keterangan_barang',
        'supir',
        'no_plat',
        'tujuan_pengiriman',
        'master_tujuan_kirim_id',
        'gambar_surat_jalan',
        'status',
        'kegiatan',
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
    
    public function masterTujuanKirim(): BelongsTo
    {
        return $this->belongsTo(MasterTujuanKirim::class, 'master_tujuan_kirim_id');
    }
    
    // Items/Dimensi relationship
    public function items(): HasMany
    {
        return $this->hasMany(TandaTerimaLclItem::class, 'tanda_terima_lcl_id');
    }
    
    // Penerima relationship (multiple)
    public function penerimaPivot(): HasMany
    {
        return $this->hasMany(TandaTerimaLclPenerima::class, 'tanda_terima_lcl_id')->orderBy('urutan');
    }
    
    // Pengirim relationship (multiple)
    public function pengirimPivot(): HasMany
    {
        return $this->hasMany(TandaTerimaLclPengirim::class, 'tanda_terima_lcl_id')->orderBy('urutan');
    }
    
    // Kontainer pivot relationship (untuk track kontainer yang sama)
    public function kontainerPivot(): HasMany
    {
        return $this->hasMany(KontainerTandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }
    
    // Get all tanda terima with same kontainer
    public function tandaTerimaSeKontainer()
    {
        if (!$this->nomor_kontainer) {
            return collect([]);
        }
        
        return static::where('nomor_kontainer', $this->nomor_kontainer)
            ->where('id', '!=', $this->id)
            ->with(['penerimaPivot', 'pengirimPivot', 'items'])
            ->get();
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
    
    // Get first penerima (for backward compatibility)
    public function getNamaPenerimaAttribute(): ?string
    {
        return $this->penerimaPivot->first()?->nama_penerima;
    }
    
    // Get first pengirim (for backward compatibility)
    public function getNamaPengirimAttribute(): ?string
    {
        return $this->pengirimPivot->first()?->nama_pengirim;
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
    
    public function scopeLcl($query)
    {
        return $query->where('tipe_kontainer', 'lcl');
    }
    
    public function scopeByKontainer($query, $nomorKontainer)
    {
        return $query->where('nomor_kontainer', $nomorKontainer);
    }
}
