<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PembayaranInvoiceAktivitasLain extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'pembayaran_invoice_aktivitas_lain';

    protected $fillable = [
        'nomor',
        'nomor_accurate',
        'tanggal',
        'jenis_aktivitas',
        'penerima',
        'total_invoice',
        'jumlah_dibayar',
        'debit_kredit',
        'akun_coa_id',
        'akun_bank_id',
        'keterangan',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_invoice' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationship dengan User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan User (approved_by)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship dengan Akun COA
     */
    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }

    /**
     * Relationship dengan Akun Bank
     */
    public function akunBank()
    {
        return $this->belongsTo(Coa::class, 'akun_bank_id');
    }

    /**
     * Relationship dengan Invoice Aktivitas Lain (many-to-many)
     */
    public function invoices()
    {
        return $this->belongsToMany(
            InvoiceAktivitasLain::class,
            'invoice_aktivitas_lain_pembayaran',
            'pembayaran_invoice_aktivitas_lain_id',
            'invoice_aktivitas_lain_id'
        )->withPivot('jumlah_dibayar')
          ->withTimestamps();
    }

    /**
     * Relationship dengan COA Transactions
     */
    public function coaTransactions()
    {
        return $this->hasMany(CoaTransaction::class, 'nomor_referensi', 'nomor');
    }

    /**
     * Generate nomor pembayaran
     */
    public static function generateNomor()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "PIAL/{$year}/{$month}/";
        
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

    /**
     * Get jumlah invoice yang dipilih
     */
    public function getJumlahInvoiceAttribute()
    {
        return $this->invoices()->count();
    }
}
