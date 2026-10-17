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
        'lokasi',
        'pph_persen',
        'pph_nominal',
        'pph_manual',
        'jenis_penyesuaian',
        'tipe_penyesuaian',
        'tipe_penyesuaian_detail',
        'sub_jenis_kendaraan',
        'nomor_polisi',
        'nomor_polisi_list',
        'nomor_voyage',
        'no_surat_jalan',
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
        'pph_persen' => 'decimal:1',
        'pph_nominal' => 'integer',
        'pph_manual' => 'boolean',
        'grand_total' => 'integer',
        'tipe_penyesuaian' => 'array',
        'tipe_penyesuaian_detail' => 'array',
        'nomor_polisi_list' => 'array',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $payment): void {
            $manual = $payment->jenis_aktivitas === 'Pembayaran Kendaraan'
                && (float) $payment->pph_persen > 0
                && (bool) $payment->pph_manual;
            $payment->pph_manual = $manual;
            $totals = self::calculateTotals(
                (int) $payment->jumlah,
                $payment->jenis_aktivitas,
                $payment->pph_persen === null ? null : (float) $payment->pph_persen,
                $manual ? (int) $payment->pph_nominal : null
            );

            $payment->pph_nominal = $totals['pph_nominal'];
            $payment->grand_total = $totals['grand_total'];
        });
    }

    public static function calculateTotals(int $jumlah, ?string $jenisAktivitas, ?float $pphPersen, ?int $manualPphNominal = null): array
    {
        $pphNominal = $jenisAktivitas === 'Pembayaran Kendaraan'
            ? ($manualPphNominal ?? (int) round($jumlah * ($pphPersen ?? 0) / 100))
            : 0;

        if ($pphNominal < 0 || $pphNominal > $jumlah) {
            throw new \InvalidArgumentException('Nominal PPh harus berada antara 0 dan jumlah pembayaran.');
        }

        return [
            'pph_nominal' => $pphNominal,
            'grand_total' => $jumlah - $pphNominal,
        ];
    }

    public static function requiresDoubleBook(?string $jenisAktivitas, ?string $lokasi): bool
    {
        return ! ($jenisAktivitas === 'Pembayaran Kendaraan' && $lokasi === 'batam');
    }

    public function usesDoubleBook(): bool
    {
        return self::requiresDoubleBook($this->jenis_aktivitas, $this->lokasi);
    }

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

        // lockForUpdate() ensures that inside an active DB transaction no other
        // concurrent request can read the same last record before we insert ours,
        // preventing duplicate-nomor race conditions.
        $lastRecord = self::withTrashed()->where('nomor', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderBy('nomor', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->nomor, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
