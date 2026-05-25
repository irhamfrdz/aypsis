<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PerbaikanKontainer extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'perbaikan_kontainers';

    protected $fillable = [
        'no_perbaikan',
        'no_kontainer',
        'ukuran',
        'tipe_kontainer',
        'tanggal_masuk',
        'tanggal_keluar',
        'vendor_bengkel_id',
        'keterangan_kerusakan',
        'keterangan_perbaikan',
        'estimasi_biaya',
        'biaya_riil',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'estimasi_biaya' => 'decimal:2',
        'biaya_riil' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_perbaikan)) {
                $model->no_perbaikan = self::generateNoPerbaikan();
            }
        });
    }

    /**
     * Auto-generate a unique repair number using the nomor_terakhir counter mechanism
     */
    public static function generateNoPerbaikan()
    {
        return DB::transaction(function () {
            $now = now();
            $prefix = 'PRB-'.$now->format('Ym').'-';

            $nomorTerakhir = NomorTerakhir::where('modul', 'perbaikan_kontainer')->lockForUpdate()->first();
            if (! $nomorTerakhir) {
                $nomorTerakhir = NomorTerakhir::create([
                    'modul' => 'perbaikan_kontainer',
                    'nomor_terakhir' => 1,
                    'keterangan' => 'Last sequence number for Container Repair (Perbaikan Kontainer)',
                ]);
                $sequence = 1;
            } else {
                $sequence = $nomorTerakhir->nomor_terakhir + 1;
                $nomorTerakhir->update(['nomor_terakhir' => $sequence]);
            }

            return $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Get the repair shop/vendor associated with this repair
     */
    public function bengkel(): BelongsTo
    {
        return $this->belongsTo(VendorBengkel::class, 'vendor_bengkel_id');
    }

    /**
     * Get the user who recorded this repair
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this repair
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Try to find matching leased container in kontainers table
     */
    public function kontainer()
    {
        return $this->belongsTo(Kontainer::class, 'no_kontainer', 'nomor_seri_gabungan');
    }

    /**
     * Try to find matching stock container in stock_kontainers table
     */
    public function stockKontainer()
    {
        return $this->belongsTo(StockKontainer::class, 'no_kontainer', 'nomor_seri_gabungan');
    }
}
