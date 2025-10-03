<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarTagihanKontainerSewa extends Model
{
    use HasFactory;

    protected $table = 'daftar_tagihan_kontainer_sewa';

    protected $fillable = [
        'vendor',
        'nomor_kontainer',
        'size',
        'tanggal_awal',
        'tanggal_akhir',
        'group',
        'periode',
        'masa',
        'tarif',
        'status',
        'status_pranota',
        'pranota_id',
        'dpp',
        'adjustment',
        'dpp_nilai_lain',
        'ppn',
        'pph',
        'grand_total',
    ];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
        'periode' => 'integer',
        'masa' => 'string',
        'dpp' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'dpp_nilai_lain' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    // Numeric days derived from masa string when needed
    public function getMasaDaysAttribute()
    {
        if (empty($this->masa)) return null;
        // try to parse pattern 'D MMMM YYYY - D MMMM YYYY' and compute diffInDays
        try {
            $parts = explode(' - ', $this->masa);
            if (count($parts) !== 2) return null;
            $s = \Carbon\Carbon::parse($parts[0]);
            $e = \Carbon\Carbon::parse($parts[1]);
            return $s->diffInDays($e);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Optional: presentational accessor for formatted total
    public function getFormattedGrandTotalAttribute()
    {
        return number_format($this->grand_total ?? 0, 2, '.', ',');
    }

    /**
     * Get the pranota that this tagihan belongs to (legacy Pranota table)
     */
    public function pranota()
    {
        return $this->belongsTo(Pranota::class, 'pranota_id');
    }

    /**
     * Get the pranota kontainer sewa that this tagihan belongs to
     */
    public function pranotaKontainerSewa()
    {
        return $this->belongsTo(PranotaTagihanKontainerSewa::class, 'pranota_id');
    }

    /**
     * Get the actual pranota record (checks both tables)
     * Returns either Pranota or PranotaTagihanKontainerSewa
     */
    public function getPranotaRecordAttribute()
    {
        if (!$this->pranota_id) {
            return null;
        }

        // First try PranotaTagihanKontainerSewa
        $pranotaKontainerSewa = PranotaTagihanKontainerSewa::find($this->pranota_id);
        if ($pranotaKontainerSewa) {
            return $pranotaKontainerSewa;
        }

        // Fallback to regular Pranota
        return Pranota::find($this->pranota_id);
    }

    /**
     * Scope for items not included in any pranota
     */
    public function scopeNotInPranota($query)
    {
        return $query->whereNull('status_pranota');
    }

    /**
     * Scope for items included in pranota
     */
    public function scopeInPranota($query)
    {
        return $query->where('status_pranota', 'included');
    }
}
