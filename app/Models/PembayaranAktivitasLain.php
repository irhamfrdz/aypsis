<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranAktivitasLain extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomor',
        'nomor_accurate',
        'tanggal',
        'jenis_aktivitas',
        'jenis_penyesuaian',
        'tipe_penyesuaian',
        'tipe_penyesuaian_detail',
        'sub_jenis_kendaraan',
        'nomor_polisi',
        'nomor_voyage',
        'penerima',
        'keterangan',
        'jumlah',
        'debit_kredit',
        'metode_pembayaran',
        'akun_coa_id',
        'akun_bank_id',
        'invoice_ids',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'tipe_penyesuaian' => 'array',
        'tipe_penyesuaian_detail' => 'array',
        'approved_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }

    public function akunBank()
    {
        return $this->belongsTo(Coa::class, 'akun_bank_id');
    }

    public function invoices()
    {
        return $this->belongsToMany(
            InvoiceAktivitasLain::class,
            'pembayaran_invoice_pivot',
            'pembayaran_id',
            'invoice_id'
        )->withPivot('jumlah_dibayar')->withTimestamps();
    }

    public function coaTransactions()
    {
        return $this->hasMany(CoaTransaction::class, 'nomor_referensi', 'nomor');
    }

    public static function generateNomor()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "PAL/{$year}/{$month}/";
        
        $lastRecord = self::where('nomor', 'like', $prefix . '%')
            ->orderBy('nomor', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->nomor, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
