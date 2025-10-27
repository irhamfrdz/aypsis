<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class TandaTerimaLcl extends Model
{
    use HasFactory, Auditable;
    
    protected $table = 'tanda_terima_lcl';
    
    protected $fillable = [
        'nomor_tanda_terima',
        'tanggal_tanda_terima', 
        'no_surat_jalan_customer',
        'term_id',
        'nama_penerima',
        'pic_penerima',
        'telepon_penerima', 
        'alamat_penerima',
        'nama_pengirim',
        'pic_pengirim',
        'telepon_pengirim',
        'alamat_pengirim',
        'nama_barang',
        'jenis_barang_id',
        'kuantitas',
        'keterangan_barang',
        'supir',
        'no_plat',
        'tujuan_pengiriman_id',
        'tipe_kontainer',
        'status',
        'created_by',
        'updated_by'
    ];
    
    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'kuantitas' => 'integer',
    ];
    
    // Relationships
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
    
    public function jenisBarang(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class, 'jenis_barang_id');
    }
    
    public function tujuanPengiriman(): BelongsTo
    {
        return $this->belongsTo(MasterTujuanKirim::class, 'tujuan_pengiriman_id');
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(TandaTerimaLclItem::class);
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
    
    public function getFormattedNumberAttribute(): string
    {
        return $this->nomor_tanda_terima;
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
}
