<?php

namespace App\Console\Commands;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaRitKenek;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class MovePranotaRitKenekToDriverIncentiveAccount extends Command
{
    protected $signature = 'pembayaran-pranota-rit-kenek:akun-insentif-supir-truck-jakarta {--execute : Simpan perubahan; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Pindahkan akun semua pembayaran pranota rit kenek dan lengkapi jurnalnya ke 5101.001 Biaya Insentif Supir Truck Jakarta';

    public function handle(): int
    {
        if (! Schema::hasColumn('pembayaran_pranota_rit_keneks', 'akun_coa_id')) {
            $this->error('Kolom akun_coa_id belum ada. Jalankan php artisan migrate --force terlebih dahulu.');

            return self::FAILURE;
        }

        $targets = Coa::where('nomor_akun', '5101.001')
            ->where('nama_akun', 'Biaya Insentif Supir Truck Jakarta')->get();
        if ($targets->count() !== 1) {
            $this->error('Akun tujuan 5101.001 Biaya Insentif Supir Truck Jakarta tidak ditemukan secara unik.');

            return self::FAILURE;
        }

        $target = $targets->first();
        $execute = (bool) $this->option('execute');
        $summary = [
            'pembayaran' => 0,
            'akun_diubah' => 0,
            'jurnal_dipindah' => 0,
            'jurnal_biaya_dibuat' => 0,
            'pasangan_jurnal_dibuat' => 0,
            'sudah_sesuai' => 0,
        ];
        $openingBalances = [];
        $balanceChanges = [];

        try {
            DB::transaction(function () use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges) {
                $paymentsQuery = PembayaranPranotaRitKenek::query()->orderBy('id');
                if ($execute) {
                    $paymentsQuery->lockForUpdate();
                }

                $paymentsQuery->chunkById(200, function ($payments) use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges) {
                    $journalQuery = CoaTransaction::whereIn('nomor_referensi', $payments->pluck('nomor_pembayaran'));
                    if ($execute) {
                        $journalQuery->lockForUpdate();
                    }
                    $journals = $journalQuery
                        ->get(['id', 'nomor_referensi', 'jenis_transaksi', 'coa_id', 'tanggal_transaksi', 'keterangan', 'created_by', 'debit', 'kredit'])
                        ->groupBy('nomor_referensi');

                    foreach ($payments as $payment) {
                        $summary['pembayaran']++;
                        $reference = $payment->nomor_pembayaran;
                        $journal = $journals->get($reference, collect());
                        $amount = self::toCents($payment->total_tagihan_setelah_penyesuaian);

                        if ($amount <= 0 || ! in_array($payment->jenis_transaksi, ['Debit', 'Kredit'], true)) {
                            throw new RuntimeException("Nominal atau jenis transaksi pembayaran {$reference} tidak valid.");
                        }

                        $bankIds = Coa::where('nama_akun', $payment->bank)->pluck('id');
                        if ($bankIds->count() !== 1 || (int) $bankIds->first() === (int) $target->id) {
                            throw new RuntimeException("Akun bank pembayaran {$reference} tidak dapat dikenali dengan pasti.");
                        }
                        $bankId = (int) $bankIds->first();
                        $expectedBankNet = $payment->jenis_transaksi === 'Debit' ? $amount : -$amount;
                        $description = 'Pembayaran Pranota Rit Kenek - '.$reference;
                        $journalAlreadyTarget = false;

                        if ($journal->isEmpty()) {
                            // Pembayaran lama dapat tersimpan tanpa jurnal ketika akun biaya belum ada.
                            $this->rememberOpeningBalance($openingBalances, $bankId, $execute);
                            $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);
                            $balanceChanges[$bankId] = ($balanceChanges[$bankId] ?? 0) + $expectedBankNet;
                            $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) - $expectedBankNet;
                            $summary['pasangan_jurnal_dibuat']++;

                            if ($execute) {
                                $this->insertJournal($payment, $bankId, $expectedBankNet, $description);
                                $this->insertJournal($payment, (int) $target->id, -$expectedBankNet, $description);
                            }
                        } else {
                            if (! in_array($journal->count(), [1, 2], true)
                                || $journal->contains(fn ($entry) => $entry->jenis_transaksi !== 'Pembayaran Pranota Rit Kenek')) {
                                throw new RuntimeException("Jurnal {$reference} tidak memiliki pasangan pembayaran pranota rit kenek yang valid.");
                            }

                            $bankEntries = $journal->where('coa_id', $bankId);
                            $expenseEntries = $journal->reject(fn ($entry) => (int) $entry->coa_id === $bankId);
                            if ($bankEntries->count() !== 1 || $expenseEntries->count() !== $journal->count() - 1) {
                                throw new RuntimeException("Akun biaya pada jurnal {$reference} tidak dapat dikenali dengan pasti.");
                            }

                            $bankEntry = $bankEntries->first();
                            $bankNet = self::toCents($bankEntry->debit) - self::toCents($bankEntry->kredit);
                            if ($bankNet !== $expectedBankNet
                                || substr((string) $bankEntry->tanggal_transaksi, 0, 10) !== $payment->tanggal_pembayaran->format('Y-m-d')) {
                                throw new RuntimeException("Nominal, arah, atau tanggal jurnal bank {$reference} tidak sesuai pembayaran.");
                            }

                            if ($expenseEntries->isEmpty()) {
                                $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);
                                $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) - $expectedBankNet;
                                $summary['jurnal_biaya_dibuat']++;

                                if ($execute) {
                                    $this->insertJournal($payment, (int) $target->id, -$expectedBankNet, $bankEntry->keterangan ?? $description, $bankEntry->created_by);
                                }
                            } else {
                                $expense = $expenseEntries->first();
                                $expenseNet = self::toCents($expense->debit) - self::toCents($expense->kredit);
                                if ($expenseNet !== -$expectedBankNet
                                    || substr((string) $expense->tanggal_transaksi, 0, 10) !== $payment->tanggal_pembayaran->format('Y-m-d')) {
                                    throw new RuntimeException("Nominal, arah, atau tanggal jurnal biaya {$reference} tidak sesuai pembayaran.");
                                }

                                if ((int) $expense->coa_id !== (int) $target->id) {
                                    $oldId = (int) $expense->coa_id;
                                    $this->rememberOpeningBalance($openingBalances, $oldId, $execute);
                                    $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);
                                    $balanceChanges[$oldId] = ($balanceChanges[$oldId] ?? 0) - $expenseNet;
                                    $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) + $expenseNet;
                                    $summary['jurnal_dipindah']++;

                                    if ($execute) {
                                        DB::table('coa_transactions')->where('id', $expense->id)
                                            ->update(['coa_id' => $target->id]);
                                    }
                                } else {
                                    $journalAlreadyTarget = true;
                                }
                            }
                        }

                        if ((int) $payment->akun_coa_id !== (int) $target->id) {
                            $summary['akun_diubah']++;
                            if ($execute) {
                                DB::table('pembayaran_pranota_rit_keneks')->where('id', $payment->id)
                                    ->update(['akun_coa_id' => $target->id, 'updated_at' => now()]);
                            }
                        } elseif ($journalAlreadyTarget) {
                            $summary['sudah_sesuai']++;
                        }
                    }
                });

                if ($execute) {
                    foreach ($balanceChanges as $accountId => $change) {
                        DB::update('UPDATE akun_coa SET saldo = saldo + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [self::fromCents($change), $accountId]);
                    }

                    // Saldo jurnal mengikuti urutan pencatatan tiap akun (id), termasuk baris baru.
                    foreach ($openingBalances as $accountId => $opening) {
                        $running = $opening;
                        CoaTransaction::where('coa_id', $accountId)->orderBy('id')
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

        $this->table(['Pembayaran', 'Akun diubah', 'Jurnal dipindah', 'Jurnal biaya dibuat', 'Pasangan jurnal dibuat', 'Sudah sesuai'], [[
            $summary['pembayaran'], $summary['akun_diubah'], $summary['jurnal_dipindah'], $summary['jurnal_biaya_dibuat'], $summary['pasangan_jurnal_dibuat'], $summary['sudah_sesuai'],
        ]]);
        $this->info($execute ? 'Selesai. Perubahan tersimpan.' : 'Pratinjau selesai. Jalankan lagi dengan --execute untuk menyimpan.');

        return self::SUCCESS;
    }

    private function insertJournal(PembayaranPranotaRitKenek $payment, int $coaId, int $net, string $description, ?int $createdBy = null): void
    {
        DB::table('coa_transactions')->insert([
            'coa_id' => $coaId,
            'tanggal_transaksi' => $payment->tanggal_pembayaran->format('Y-m-d'),
            'nomor_referensi' => $payment->nomor_pembayaran,
            'jenis_transaksi' => 'Pembayaran Pranota Rit Kenek',
            'keterangan' => $description,
            'debit' => self::fromCents(max(0, $net)),
            'kredit' => self::fromCents(max(0, -$net)),
            'saldo' => '0.00',
            'created_by' => $createdBy ?? $payment->created_by,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
