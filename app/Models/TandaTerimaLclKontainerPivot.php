<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaTerimaLclKontainerPivot extends Model
{
    use HasFactory;
    
    protected $table = 'tanda_terima_lcl_kontainer_pivot';
    
    protected $fillable = [
        'tanda_terima_lcl_id',
        'nomor_kontainer',
        'size_kontainer',
        'tipe_kontainer',
        'nomor_seal',
        'tanggal_seal',
        'assigned_at',
        'assigned_by',
    ];
    
    protected $casts = [
        'tanggal_seal' => 'date',
        'assigned_at' => 'datetime',
    ];
    
    // Relationships
    public function tandaTerima(): BelongsTo
    {
        return $this->belongsTo(TandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }
    
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    // Alias for backward compatibility
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
