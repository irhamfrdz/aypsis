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

    public function stockBan(): BelongsTo
    {
        return $this->belongsTo(StockBan::class, 'stock_ban_id');
    }
}
