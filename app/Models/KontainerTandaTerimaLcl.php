<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontainerTandaTerimaLcl extends Model
{
    use HasFactory;
    
    protected $table = 'kontainer_tanda_terima_lcl';
    
    protected $fillable = [
        'nomor_kontainer',
        'tanda_terima_lcl_id',
        'keterangan'
    ];
    
    // Relationships
    public function tandaTerima(): BelongsTo
    {
        return $this->belongsTo(TandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }
    
    // Helper methods
    public static function getTandaTerimasForKontainer(string $nomorKontainer)
    {
        return static::where('nomor_kontainer', $nomorKontainer)
            ->with('tandaTerima')
            ->get()
            ->pluck('tandaTerima');
    }
    
    public static function getKontainerInfo(string $nomorKontainer)
    {
        return static::where('nomor_kontainer', $nomorKontainer)
            ->with(['tandaTerima.penerimaPivot', 'tandaTerima.pengirimPivot', 'tandaTerima.items'])
            ->get();
    }
    
    // Scope
    public function scopeByKontainer($query, $nomorKontainer)
    {
        return $query->where('nomor_kontainer', $nomorKontainer);
    }
}
