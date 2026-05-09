<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LangsirBatam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'langsir_batams';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'no_kontainer',
        'size',
        'no_seal',
        'dari',
        'ke',
        'no_plat',
        'supir',
        'biaya',
        'keterangan',
        'status',
        'input_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'biaya' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function supirKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'supir', 'nama_panggilan');
    }

    public static function generateNoTransaksi()
    {
        $date = now()->format('Ymd');
        $prefix = 'LNG-' . $date . '-';
        $lastRecord = self::where('no_transaksi', 'like', $prefix . '%')
            ->orderBy('no_transaksi', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = intval(str_replace($prefix, '', $lastRecord->no_transaksi));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}
