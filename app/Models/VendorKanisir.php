<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class VendorKanisir extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama',
        'ukuran',
        'harga',
        'tipe',
        'keterangan',
        'catatan',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with User who created this record
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with User who last updated this record
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate next sequential code
     */
    public static function generateNextCode(): string
    {
        $lastVendor = self::orderBy('id', 'desc')->first();
        
        if (!$lastVendor || !$lastVendor->kode) {
            return 'VK-001';
        }

        $lastCode = $lastVendor->kode;
        $number = (int) str_replace('VK-', '', $lastCode);
        $nextNumber = $number + 1;
        
        return 'VK-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
