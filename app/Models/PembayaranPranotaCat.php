<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PembayaranPranotaCat extends Model
{
    use HasFactory;

    use Auditable;
    protected $table = 'pembayaran_pranota_cat';

    protected $fillable = [
        'nomor_pembayaran',
        'nomor_cetakan',
        'bank',
        'jenis_transaksi',
        'tanggal_kas',
        'total_pembayaran',
        'penyesuaian',
        'total_setelah_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'total_pembayaran' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_setelah_penyesuaian' => 'decimal:2'
    ];

    /**
     * Relationship to pranota items through pivot table
     */
    public function pranotaTagihanCats()
    {
        return $this->belongsToMany(
            PranotaTagihanCat::class,
            'pembayaran_pranota_cat_items',
            'pembayaran_pranota_cat_id',
            'pranota_tagihan_cat_id'
        )->withPivot('amount')->withTimestamps();
    }

    /**
     * Get all payment items
     */
    public function paymentItems()
    {
        return $this->hasMany(PembayaranPranotaCatItem::class, 'pembayaran_pranota_cat_id');
    }

    /**
     * Get the user who created this pembayaran (dummy relationship since table doesn't have creator field)
     */
    public function pembuatPembayaran()
    {
        // This table doesn't have a creator field, so return a dummy relationship
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'name' => 'System',
            'id' => null
        ]);
    }

    /**
     * Generate nomor pembayaran otomatis
     */
    public static function generateNomorPembayaran($bankCode = '000', $nomorCetakan = 1)
    {
        $today = now();
        $tahun = $today->format('y');
        $bulan = $today->format('m');

        // Count payments created today
        $count = self::whereDate('created_at', $today->toDateString())->count();
        $sequence = str_pad($count + 1, 6, '0', STR_PAD_LEFT);

        return "{$bankCode}{$nomorCetakan}{$tahun}{$bulan}{$sequence}";
    }
}
