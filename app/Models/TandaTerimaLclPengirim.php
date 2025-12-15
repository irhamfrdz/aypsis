<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaTerimaLclPengirim extends Model
{
    use HasFactory;
    
    protected $table = 'tanda_terima_lcl_pengirim';
    
    protected $fillable = [
        'tanda_terima_lcl_id',
        'nama_pengirim',
        'pic_pengirim',
        'telepon_pengirim',
        'alamat_pengirim',
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
        $info = $this->nama_pengirim;
        if ($this->pic_pengirim) {
            $info .= " (PIC: {$this->pic_pengirim})";
        }
        if ($this->telepon_pengirim) {
            $info .= " - {$this->telepon_pengirim}";
        }
        return $info;
    }
    
    // Scope untuk ordering by urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
