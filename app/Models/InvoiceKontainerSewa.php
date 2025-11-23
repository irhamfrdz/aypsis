<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceKontainerSewa extends Model
{
    use SoftDeletes;

    protected $table = 'invoices_kontainer_sewa';

    protected $fillable = [
        'nomor_invoice',
        'tanggal_invoice',
        'vendor_id',
        'vendor_name',
        'subtotal',
        'ppn',
        'pph',
        'adjustment',
        'total',
        'status',
        'keterangan',
        'catatan',
        'file_attachment',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'subtotal' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'total' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Relasi ke Vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relasi ke User yang membuat invoice
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias untuk creator (untuk konsistensi dengan controller)
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang approve invoice
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Alias untuk approver (untuk konsistensi dengan controller)
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi many-to-many ke Tagihan Kontainer melalui pivot table
     */
    public function tagihans(): BelongsToMany
    {
        return $this->belongsToMany(
            DaftarTagihanKontainerSewa::class,
            'invoice_kontainer_sewa_items',
            'invoice_id',
            'tagihan_id'
        )
        ->withPivot('jumlah', 'catatan')
        ->withTimestamps();
    }

    /**
     * Relasi ke invoice items
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceKontainerSewaItem::class, 'invoice_id');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan vendor
     */
    public function scopeVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Hitung total dari items
     */
    public function calculateTotals()
    {
        $subtotal = $this->items->sum('jumlah');
        $this->subtotal = $subtotal;
        
        // Hitung PPN (11%)
        $this->ppn = $subtotal * 0.11;
        
        // Hitung PPH (2%)
        $this->pph = $subtotal * 0.02;
        
        // Total = Subtotal + PPN - PPH + Adjustment
        $this->total = $subtotal + $this->ppn - $this->pph + $this->adjustment;
        
        return $this;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'paid' => 'bg-purple-100 text-purple-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get formatted nomor invoice
     */
    public function getFormattedNomorAttribute()
    {
        return $this->nomor_invoice;
    }
}
