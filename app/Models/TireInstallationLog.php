<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TireInstallationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tire_installation_logs';

    protected $fillable = [
        'mobil_id',
        'alat_berat_id',
        'category',
        'wheel_id',
        'wheel_code',
        'stock_ban_id',
        'nomor_seri',
        'action',
        'is_borrowed',
        'donor_unit_id',
        'donor_unit_name',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'is_borrowed' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function alatBerat(): BelongsTo
    {
        return $this->belongsTo(AlatBerat::class, 'alat_berat_id');
    }

    public function stockBan(): BelongsTo
    {
        return $this->belongsTo(StockBan::class, 'stock_ban_id');
    }

    public static array $wheelNameMap = [
        'A1-LO' => 'Gandar 1 - Kiri Luar',
        'A1-LI' => 'Gandar 1 - Kiri Dalam',
        'A1-RI' => 'Gandar 1 - Kanan Dalam',
        'A1-RO' => 'Gandar 1 - Kanan Luar',
        'A2-LO' => 'Gandar 2 - Kiri Luar',
        'A2-LI' => 'Gandar 2 - Kiri Dalam',
        'A2-RI' => 'Gandar 2 - Kanan Dalam',
        'A2-RO' => 'Gandar 2 - Kanan Luar',
        'A3-LO' => 'Gandar 3 - Kiri Luar',
        'A3-LI' => 'Gandar 3 - Kiri Dalam',
        'A3-RI' => 'Gandar 3 - Kanan Dalam',
        'A3-RO' => 'Gandar 3 - Kanan Luar',
        'A4-LO' => 'Gandar 4 - Kiri Luar',
        'A4-LI' => 'Gandar 4 - Kiri Dalam',
        'A4-RI' => 'Gandar 4 - Kanan Dalam',
        'A4-RO' => 'Gandar 4 - Kanan Luar',
        'A1-L' => 'Gandar 1 - Kiri',
        'A1-R' => 'Gandar 1 - Kanan',
        'A2-L' => 'Gandar 2 - Kiri',
        'A2-R' => 'Gandar 2 - Kanan',
        'A3-L' => 'Gandar 3 - Kiri',
        'A3-R' => 'Gandar 3 - Kanan',
        'A4-L' => 'Gandar 4 - Kiri',
        'A4-R' => 'Gandar 4 - Kanan',
        'W1' => 'Gandar 1 - Kiri Luar',
        'W2' => 'Gandar 1 - Kiri Dalam',
        'W3' => 'Gandar 1 - Kanan Dalam',
        'W4' => 'Gandar 1 - Kanan Luar',
        'W5' => 'Gandar 2 - Kiri Luar',
        'W6' => 'Gandar 2 - Kiri Dalam',
        'W7' => 'Gandar 2 - Kanan Dalam',
        'W8' => 'Gandar 2 - Kanan Luar',
        'W9' => 'Gandar 3 - Kiri Luar',
        'W10' => 'Gandar 3 - Kiri Dalam',
        'W11' => 'Gandar 3 - Kanan Dalam',
        'W12' => 'Gandar 3 - Kanan Luar',
        'SPARE' => 'Ban Serep',
        'SEREP' => 'Ban Serep',
    ];

    public function getPosisiRodaAttribute(): string
    {
        $code = strtoupper(trim($this->wheel_code ?: ($this->wheel_id ?: '')));
        if (empty($code)) {
            return '-';
        }

        if (isset(self::$wheelNameMap[$code])) {
            return self::$wheelNameMap[$code];
        }

        if (preg_match('/^A(\d+)-([LR])([OI])?$/i', $code, $matches)) {
            $gandar = 'Gandar '.$matches[1];
            $sisi = strtoupper($matches[2]) === 'L' ? 'Kiri' : 'Kanan';
            $pos = isset($matches[3]) ? (strtoupper($matches[3]) === 'O' ? ' Luar' : ' Dalam') : '';

            return "{$gandar} - {$sisi}{$pos}";
        }

        return $this->wheel_code ?: ($this->wheel_id ?: '-');
    }

    public function getWheelNameAttribute(): string
    {
        return $this->posisi_roda;
    }
}
