<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\UniqueConstraintViolationException;

class LangsirBatam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'langsir_batams';

    protected $fillable = [
        'no_transaksi',
        'no_surat_jalan',
        'tanggal',
        'no_kontainer',
        'size',
        'no_seal',
        'dari',
        'ke',
        'no_plat',
        'sumber_chasis',
        'chasis_mobil_id',
        'no_chasis',
        'supir',
        'biaya',
        'biaya_lolo',
        'keterangan',
        'status',
        'ob_dalam_pelabuhan',
        'menggunakan_lolo',
        'operator_lolo_1_id',
        'operator_lolo_2_id',
        'input_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'biaya' => 'decimal:2',
        'biaya_lolo' => 'decimal:2',
        'ob_dalam_pelabuhan' => 'boolean',
        'menggunakan_lolo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function supirKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'supir', 'nama_panggilan');
    }

    public function chasisMobil()
    {
        return $this->belongsTo(Mobil::class, 'chasis_mobil_id');
    }

    public function tagihanLoloItem()
    {
        return $this->hasOne(TagihanLoloBatamItem::class, 'langsir_batam_id');
    }

    public static function generateNoTransaksi(bool $lock = false)
    {
        $date = now()->format('Ymd');
        $prefix = 'LNG-'.$date.'-';
        $lastNumber = self::withTrashed()->where('no_transaksi', 'like', $prefix.'%')
            ->when($lock, fn ($query) => $query->lockForUpdate())
            ->pluck('no_transaksi')
            ->map(fn ($number) => substr($number, strlen($prefix)))
            ->filter(fn ($number) => ctype_digit($number))
            ->map(fn ($number) => (int) $number)->max() ?? 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix.$newNumber;
    }

    public static function createWithAutomaticNumber(array $attributes): self
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $number = null;
            try {
                return self::getConnectionResolver()->connection()->transaction(
                    function () use ($attributes, &$number) {
                        $number = self::generateNoTransaksi(true);

                        return self::create(array_replace($attributes, ['no_transaksi' => $number]));
                    }, 5
                );
            } catch (UniqueConstraintViolationException $e) {
                // Retry only a number collision; preserve other unique validation errors.
                if ($attempt === 4 || ! self::withTrashed()->where('no_transaksi', $number)->exists()) {
                    throw $e;
                }
            }
        }
    }
}
