<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Traits\Auditable;
class NomorTerakhir extends Model
{
    use HasFactory;

    use Auditable;
    protected $table = 'nomor_terakhir';

    protected $fillable = [
        'modul',
        'nomor_terakhir',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'nomor_terakhir' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generate nomor pembayaran dengan format custom
     */
    public static function generateNomorPembayaranCustom($bankCode = '000', $nomorCetakan = 1)
    {
        return DB::transaction(function () use ($bankCode, $nomorCetakan) {
            // Lock record untuk mencegah race condition
            $nomorTerakhir = self::where('modul', 'nomor_pembayaran')->lockForUpdate()->first();

            if (!$nomorTerakhir) {
                $nomorTerakhir = self::create([
                    'modul' => 'nomor_pembayaran',
                    'nomor_terakhir' => 1,
                    'keterangan' => 'Auto generated payment number'
                ]);
                $nomorBaru = 1;
            } else {
                $nomorBaru = $nomorTerakhir->nomor_terakhir + 1;
                $nomorTerakhir->update(['nomor_terakhir' => $nomorBaru]);
            }

            // Format: BANKCODE-CETAKAN-YY-MM-XXXXXX
            $now = now();
            $tahun = $now->format('y'); // 2 digit year
            $bulan = $now->format('m');
            $sequence = str_pad($nomorBaru, 6, '0', STR_PAD_LEFT);

            return "{$bankCode}-{$nomorCetakan}-{$tahun}-{$bulan}-{$sequence}";
        });
    }

    /**
     * Get current nomor terakhir for a module
     */
    public static function getCurrentNumber($modul)
    {
        $record = self::where('modul', $modul)->first();
        return $record ? $record->nomor_terakhir : 0;
    }
}
