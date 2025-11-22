<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class DaftarTagihanKontainerSewa extends Model
{
    use HasFactory;

    use Auditable;
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
        'invoice_id',
        'dpp',
        'adjustment',
        'adjustment_note',
        'nomor_bank',
        'invoice_vendor',
        'tanggal_vendor',
        'dpp_nilai_lain',
        'ppn',
        'pph',
        'grand_total',
    ];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
        'tanggal_vendor' => 'date',
        'periode' => 'integer',
        'masa' => 'string',
        'dpp' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'dpp_nilai_lain' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate grand_total before saving
        static::saving(function ($tagihan) {
            $tagihan->calculateGrandTotal();
        });
    }

    /**
     * Calculate and set the grand total
     * Formula: (DPP + Adjustment) + PPN - PPH
     */
    public function calculateGrandTotal()
    {
        // First, recalculate PPN and PPH based on current DPP and adjustment
        $this->recalculateTaxes();

        $dpp = floatval($this->dpp ?? 0);
        $adjustment = floatval($this->adjustment ?? 0);
        $ppn = floatval($this->ppn ?? 0);
        $pph = floatval($this->pph ?? 0);

        // Calculate base with DPP + adjustment (adjustment will be 0 after being applied)
        $calculationBase = $dpp + $adjustment;

        // Calculate grand total: Base + PPN - PPH
        $this->grand_total = $calculationBase + $ppn - $pph;

        return $this->grand_total;
    }

    /**
     * Recalculate PPN and PPH based on adjusted DPP
     * This ensures consistency when DPP or adjustment changes
     */
    public function recalculateTaxes()
    {
        $dpp = floatval($this->dpp ?? 0);
        $adjustment = floatval($this->adjustment ?? 0);
        
        // Calculate from DPP + adjustment (for cases where adjustment hasn't been applied yet)
        $calculationBase = $dpp + $adjustment;

        // PPN = 11% of calculation base
        $this->ppn = $calculationBase * 0.11;

        // PPH = 2% of calculation base
        $this->pph = $calculationBase * 0.02;

        // Grand total will be auto-calculated by calculateGrandTotal
        return $this;
    }

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
     * Get the pranota that this tagihan belongs to (uses PranotaTagihanKontainerSewa)
     */
    public function pranota()
    {
        return $this->belongsTo(PranotaTagihanKontainerSewa::class, 'pranota_id');
    }

    /**
     * Get the pranota kontainer sewa that this tagihan belongs to
     */
    public function pranotaKontainerSewa()
    {
        return $this->belongsTo(PranotaTagihanKontainerSewa::class, 'pranota_id');
    }

    /**
     * Get the master pricelist for this container
     */
    public function masterPricelist()
    {
        return $this->hasOne(MasterPricelistSewaKontainer::class, 'ukuran_kontainer', 'size')
                    ->where('vendor', $this->vendor);
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

        // Use PranotaTagihanKontainerSewa
        return PranotaTagihanKontainerSewa::find($this->pranota_id);
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

    /**
     * Relasi ke Invoice Kontainer Sewa
     */
    public function invoice()
    {
        return $this->belongsTo(InvoiceKontainerSewa::class, 'invoice_id');
    }

    /**
     * Relasi ke Invoice Item
     */
    public function invoiceItem()
    {
        return $this->hasOne(InvoiceKontainerSewaItem::class, 'tagihan_id');
    }

    /**
     * Scope untuk tagihan yang belum masuk invoice
     */
    public function scopeNotInInvoice($query)
    {
        return $query->whereNull('invoice_id');
    }

    /**
     * Scope untuk tagihan yang sudah masuk invoice
     */
    public function scopeInInvoice($query)
    {
        return $query->whereNotNull('invoice_id');
    }
}
