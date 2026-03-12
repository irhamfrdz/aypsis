<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAsuransi extends Model
{
    use HasFactory;

    protected $table = 'vendor_asuransi';

    protected $fillable = [
        'kode',
        'nama_asuransi',
        'alamat',
        'telepon',
        'email',
        'keterangan',
        'catatan',
        'created_by',
        'updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate the next available Kode with prefix ASN
     *
     * @return string
     */
    public static function generateNextKode(): string
    {
        $prefix = 'ASN';
        $lastVendor = self::where('kode', 'like', $prefix . '%')
            ->orderBy('kode', 'desc')
            ->first();

        if (!$lastVendor || !preg_match('/ASN(\d+)/', $lastVendor->kode, $matches)) {
            return $prefix . '001';
        }

        $lastNumber = (int)$matches[1];
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
