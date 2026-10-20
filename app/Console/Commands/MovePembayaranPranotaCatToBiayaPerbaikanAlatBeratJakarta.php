<?php

namespace App\Console\Commands;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaCat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class MovePembayaranPranotaCatToBiayaPerbaikanAlatBeratJakarta extends Command
{
    protected $signature = 'pembayaran-pranota-cat:akun-perbaikan-alat-berat-jakarta {--execute : Simpan perubahan; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Pindahkan akun biaya semua pembayaran pranota CAT dan jurnalnya ke 5101.053 Biaya Perbaikan Alat Berat Jakarta';

    public function handle(): int
    {
        if (! Schema::hasColumn('pembayaran_pranota_cat', 'akun_coa_id')) {
            $this->error('Kolom akun_coa_id belum ada. Jalankan php artisan migrate terlebih dahulu.');

            return self::FAILURE;
        }

        $target = Coa::where('nomor_akun', '5101.053')->first();
        if (! $target || $target->nama_akun !== 'Biaya Perbaikan Alat Berat Jakarta') {
            $this->error('Akun tujuan 5101.053 Biaya Perbaikan Alat Berat Jakarta tidak ditemukan dengan nomor dan nama yang tepat.');

            return self::FAILURE;
        }

        $execute = (bool) $this->option('execute');
        $summary = ['pembayaran' => 0, 'akun_diubah' => 0, 'jurnal_dipindah' => 0, 'tanpa_jurnal' => 0];
        $openingBalances = [];
        $balanceChanges = [];

        try {
            DB::transaction(function () use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges) {
                PembayaranPranotaCat::query()
                    ->orderBy('id')
                    ->chunkById(200, function ($payments) use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges) {
                        $entries = CoaTransaction::whereIn('nomor_referensi', $payments->pluck('nomor_pembayaran'))
                            ->where('jenis_transaksi', 'Pembayaran Pranota CAT Kontainer')
                            ->get(['id', 'nomor_referensi', 'coa_id', 'debit', 'kredit'])
                            ->groupBy('nomor_referensi');

                        foreach ($payments as $payment) {
                            $summary['pembayaran']++;
                            $reference = $payment->nomor_pembayaran;
                            $journal = $entries->get($reference, collect());

                            if ($journal->isEmpty()) {
                                $summary['tanpa_jurnal']++;
                            } else {
                                if ($journal->count() !== 2) {
                                    throw new RuntimeException("Jurnal {$reference} tidak memiliki tepat satu pasangan debit/kredit.");
                                }

                                $bankId = Coa::where('nama_akun', $payment->bank)->value('id');
                                $expense = $journal->first(fn ($entry) => self::toCents($entry->debit) > 0 && self::toCents($entry->kredit) === 0);
                                $bank = $journal->first(fn ($entry) => self::toCents($entry->kredit) > 0 && self::toCents($entry->debit) === 0);

                                if (! $bankId || ! $expense || ! $bank
                                    || (int) $bank->coa_id !== (int) $bankId
                                    || (int) $expense->coa_id === (int) $bankId
                                    || (int) $target->id === (int) $bankId
                                    || self::toCents($expense->debit) !== self::toCents($bank->kredit)) {
                                    throw new RuntimeException("Akun biaya atau bank pada jurnal {$reference} tidak dapat dikenali dengan pasti.");
                                }

                                if ((int) $expense->coa_id !== (int) $target->id) {
                                    $oldId = (int) $expense->coa_id;
                                    $this->rememberOpeningBalance($openingBalances, $oldId, $execute);
                                    $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);

                                    $net = self::toCents($expense->debit);
                                    $balanceChanges[$oldId] = ($balanceChanges[$oldId] ?? 0) - $net;
                                    $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) + $net;
                                    $summary['jurnal_dipindah']++;

                                    if ($execute) {
                                        CoaTransaction::whereKey($expense->id)->update(['coa_id' => $target->id]);
                                    }
                                }
                            }

                            if ((int) $payment->akun_coa_id !== (int) $target->id) {
                                $summary['akun_diubah']++;
                                if ($execute) {
                                    $payment->update(['akun_coa_id' => $target->id]);
                                }
                            }
                        }
                    });

                if ($execute) {
                    foreach ($balanceChanges as $accountId => $change) {
                        DB::update('UPDATE akun_coa SET saldo = saldo + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [self::fromCents($change), $accountId]);
                    }

                    foreach ($openingBalances as $accountId => $opening) {
                        $running = $opening;
                        CoaTransaction::where('coa_id', $accountId)
                            ->orderBy('id')
                            ->chunkById(500, function ($transactions) use (&$running) {
                                foreach ($transactions as $transaction) {
                                    $running += self::toCents($transaction->debit) - self::toCents($transaction->kredit);
                                    if (self::toCents($transaction->saldo) !== $running) {
                                        DB::table('coa_transactions')->where('id', $transaction->id)
                                            ->update(['saldo' => self::fromCents($running)]);
                                    }
                                }
                            });
                    }
                }
            });
        } catch (Throwable $e) {
            $this->error('Tidak ada perubahan yang disimpan: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->table(['Pembayaran', 'Akun pembayaran diubah', 'Jurnal dipindah', 'Tanpa jurnal'], [[
            $summary['pembayaran'], $summary['akun_diubah'], $summary['jurnal_dipindah'], $summary['tanpa_jurnal'],
        ]]);
        $this->info($execute ? 'Selesai. Perubahan tersimpan.' : 'Pratinjau selesai. Jalankan lagi dengan --execute untuk menyimpan.');

        return self::SUCCESS;
    }

    private function rememberOpeningBalance(array &$openings, int $accountId, bool $execute): void
    {
        if (isset($openings[$accountId])) {
            return;
        }

        $query = Coa::whereKey($accountId);
        $account = ($execute ? $query->lockForUpdate() : $query)->firstOrFail();
        $net = DB::table('coa_transactions')->where('coa_id', $accountId)
            ->selectRaw('COALESCE(SUM(debit - kredit), 0) AS net')->value('net');
        $openings[$accountId] = self::toCents($account->saldo) - self::toCents($net);
    }

    private static function toCents(string|int|float $amount): int
    {
        $value = (string) $amount;
        $negative = str_starts_with($value, '-');
        [$whole, $fraction] = array_pad(explode('.', ltrim($value, '-'), 2), 2, '');
        $cents = ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');

        return $negative ? -$cents : $cents;
    }

    private static function fromCents(int $cents): string
    {
        return ($cents < 0 ? '-' : '').intdiv(abs($cents), 100).'.'.str_pad((string) (abs($cents) % 100), 2, '0', STR_PAD_LEFT);
    }
}
