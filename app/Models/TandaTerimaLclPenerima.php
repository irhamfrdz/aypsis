<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaTerimaLclPenerima extends Model
{
    use HasFactory;
    
    protected $table = 'tanda_terima_lcl_penerima';
    
    protected $fillable = [
        'tanda_terima_lcl_id',
        'nama_penerima',
        'pic_penerima',
        'telepon_penerima',
        'alamat_penerima',
        'urutan'
    ];
    
    protected $casts = [
        'urutan' => 'integer'
    ];
    
    // Relationships
    public function tandaTerima(): BelongsTo
    {
        return $this->belongsTo(TandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }
    
    // Helper methods
    public function getFullInfoAttribute(): string
    {
        $info = $this->nama_penerima;
        if ($this->pic_penerima) {
            $info .= " (PIC: {$this->pic_penerima})";
        }
        if ($this->telepon_penerima) {
            $info .= " - {$this->telepon_penerima}";
        }
        return $info;
    }
    
    // Scope untuk ordering by urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
