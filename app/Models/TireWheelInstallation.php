<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TireWheelInstallation extends Model
{
    use HasFactory;

    protected $table = 'tire_wheel_installations';

    protected $fillable = [
        'mobil_id',
        'alat_berat_id',
        'category',
        'wheel_id',
        'wheel_code',
        'wheel_name',
        'stock_ban_id',
        'nomor_seri',
        'merk',
        'ukuran',
        'kondisi',
        'is_borrowed',
        'donor_unit_id',
        'donor_unit_name',
        'donor_category',
        'borrowed_at',
        'installed_at',
    ];

    protected $casts = [
        'is_borrowed' => 'boolean',
        'borrowed_at' => 'datetime',
        'installed_at' => 'datetime',
    ];

    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function stockBan(): BelongsTo
    {
        return $this->belongsTo(StockBan::class, 'stock_ban_id');
    }
}
