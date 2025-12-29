<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use App\Traits\Auditable;
class Kontainer extends Model
{
    use HasFactory;
    use Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'awalan_kontainer',
        'nomor_seri_kontainer',
        'akhiran_kontainer',
        'nomor_seri_gabungan',
        'ukuran',
        'tipe_kontainer',
        'vendor',
        'keterangan',
        'tanggal_mulai_sewa',
        'tanggal_selesai_sewa',
        'status',
        'gate_in_id',
        'status_checkpoint_supir',
        'tanggal_checkpoint_supir',
        'status_gate_in',
        'tanggal_gate_in',
        'terminal_id'
    ];

    /**
     * Cast date attributes to Carbon instances for safe formatting in views.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_mulai_sewa' => 'date',
        'tanggal_selesai_sewa' => 'date',
        'tanggal_checkpoint_supir' => 'datetime',
        'tanggal_gate_in' => 'datetime',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kontainer) {
            self::validateNomorSeriUniqueness($kontainer);
        });

        static::updating(function ($kontainer) {
            self::validateNomorSeriUniqueness($kontainer);
        });
    }

    /**
     * Validate nomor seri kontainer uniqueness across kontainers and stock_kontainers
     */
    private static function validateNomorSeriUniqueness($kontainer)
    {
        if (empty($kontainer->nomor_seri_gabungan)) {
            return;
        }

        // Cek duplikasi dengan tabel stock_kontainers
        $existingStock = \App\Models\StockKontainer::where('nomor_seri_gabungan', $kontainer->nomor_seri_gabungan)
            ->where('status', 'active')
            ->first();

        if ($existingStock) {
            // Jika ada stock kontainer aktif dengan nomor yang sama, set stock kontainer ke inactive
            $existingStock->update(['status' => 'inactive']);
        }

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer
        $duplicateQuery = self::where('nomor_seri_kontainer', $kontainer->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $kontainer->akhiran_kontainer)
            ->where('status', 'active');

        if ($kontainer->exists) {
            $duplicateQuery->where('id', '!=', $kontainer->id);
        }

        $existingWithSameSerialAndSuffix = $duplicateQuery->first();
        if ($existingWithSameSerialAndSuffix) {
            // Jika ada kontainer aktif dengan nomor seri dan akhiran yang sama, set yang lama ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);
        }

        // Cek duplikasi nomor seri gabungan (selain validasi nomor seri + akhiran di atas)
        $query = self::where('nomor_seri_gabungan', $kontainer->nomor_seri_gabungan)
            ->where('status', 'active');

        if ($kontainer->exists) {
            $query->where('id', '!=', $kontainer->id);
        }

        $existingKontainer = $query->first();
        if ($existingKontainer) {
            // Jika ada kontainer aktif lain dengan nomor gabungan yang sama, set yang lama ke inactive
            $existingKontainer->update(['status' => 'inactive']);
        }
    }

    // Relasi ke permohonan melalui pivot
    public function permohonans()
    {
        return $this->belongsToMany(Permohonan::class, 'permohonan_kontainers');
    }

    // Relasi ke perbaikan kontainer
    public function perbaikanKontainers()
    {
        return $this->hasMany(PerbaikanKontainer::class, 'nomor_kontainer', 'nomor_seri_gabungan');
    }

    // Gate In relationships
    public function gateIn()
    {
        return $this->belongsTo(GateIn::class);
    }

    public function terminal()
    {
        return $this->belongsTo(MasterTerminal::class, 'terminal_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudangs_id');
    }

    // Accessor untuk nomor kontainer gabungan
    public function getNomorKontainerAttribute()
    {
        // Prefer the stored full serial if present so we display exactly what was
        // entered or discovered (`nomor_seri_gabungan`). Fall back to composing
        // from parts for older records.
        if (!empty($this->nomor_seri_gabungan)) {
            return $this->nomor_seri_gabungan;
        }
        return ($this->awalan_kontainer ?? '') . ($this->nomor_seri_kontainer ?? '') . ($this->akhiran_kontainer ?? '');
    }
}
