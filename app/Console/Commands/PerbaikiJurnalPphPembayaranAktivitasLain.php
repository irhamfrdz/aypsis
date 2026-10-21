<?php

namespace App\Console\Commands;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranAktivitasLain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PerbaikiJurnalPphPembayaranAktivitasLain extends Command
{
    protected $signature = 'pembayaran-aktivitas-lain:perbaiki-jurnal-pph {nomor} {--execute : Simpan koreksi jurnal}';

    protected $description = 'Koreksi jurnal bank PAL menjadi nilai setelah PPh dan catat ke akun PPh pembayaran';

    public function handle(): int
    {
        $nomor = (string) $this->argument('nomor');
        $payment = PembayaranAktivitasLain::where('nomor', $nomor)->first();
        if (! $payment || $payment->pph_nominal <= 0 || ! $payment->usesDoubleBook()) {
            $this->error('Pembayaran dengan PPh dan jurnal double book tidak ditemukan.');

            return self::FAILURE;
        }

        $taxAccount = Coa::find($payment->akun_pph_id);
        if (! $taxAccount) {
            $this->error('Akun PPh pembayaran belum dipilih atau tidak ditemukan.');

            return self::FAILURE;
        }

        try {
            $result = DB::transaction(function () use ($nomor, $taxAccount) {
                $payment = PembayaranAktivitasLain::where('nomor', $nomor)->lockForUpdate()->firstOrFail();
                $entries = CoaTransaction::where('nomor_referensi', $nomor)
                    ->where('jenis_transaksi', 'Pembayaran Aktivitas Lain')
                    ->lockForUpdate()->get();
                $bankEntry = $entries->firstWhere('coa_id', $payment->akun_bank_id);
                $expenseEntry = $entries->firstWhere('coa_id', $payment->akun_coa_id);
                $taxEntry = $entries->firstWhere('coa_id', $taxAccount->id);
                if (! $bankEntry || ! $expenseEntry || $entries->count() !== ($taxEntry ? 3 : 2)) {
                    throw new RuntimeException('Susunan jurnal berbeda dari yang diharapkan; koreksi dibatalkan.');
                }

                $pph = (int) $payment->pph_nominal;
                $gross = (int) $payment->jumlah;
                $net = (int) $payment->grand_total;
                $bankIsCredit = $payment->debit_kredit === 'kredit';
                $bankAmount = (int) ($bankIsCredit ? $bankEntry->kredit : $bankEntry->debit);
                $expenseAmount = (int) ($bankIsCredit ? $expenseEntry->debit : $expenseEntry->kredit);
                if ($expenseAmount !== $gross || $net !== $gross - $pph) {
                    throw new RuntimeException('Nilai biaya atau total pembayaran tidak sesuai; koreksi dibatalkan.');
                }
                if ($bankAmount === $net && $taxEntry) {
                    return 'already';
                }
                if ($bankAmount !== $gross || $taxEntry) {
                    throw new RuntimeException('Jurnal bank sudah berubah atau jurnal PPh sudah ada; koreksi dibatalkan.');
                }
                if (! $this->option('execute')) {
                    return 'preview';
                }

                $bankAccount = Coa::whereKey($payment->akun_bank_id)->lockForUpdate()->firstOrFail();
                $lockedTaxAccount = Coa::whereKey($taxAccount->id)->lockForUpdate()->firstOrFail();
                $bankBalanceChange = $bankIsCredit ? $pph : -$pph;
                $bankEntry->update([
                    $bankIsCredit ? 'kredit' : 'debit' => $net,
                    'saldo' => $bankEntry->saldo + $bankBalanceChange,
                ]);
                $bankAccount->update(['saldo' => $bankAccount->saldo + $bankBalanceChange]);

                $taxBalanceChange = $bankIsCredit ? -$pph : $pph;
                CoaTransaction::create([
                    'coa_id' => $lockedTaxAccount->id,
                    'tanggal_transaksi' => $payment->tanggal,
                    'nomor_referensi' => $payment->nomor,
                    'jenis_transaksi' => 'Pembayaran Aktivitas Lain',
                    'keterangan' => 'Pembayaran Aktivitas Lain - '.$payment->jenis_aktivitas.' - PPh',
                    'debit' => $bankIsCredit ? 0 : $pph,
                    'kredit' => $bankIsCredit ? $pph : 0,
                    'saldo' => $lockedTaxAccount->saldo + $taxBalanceChange,
                    'created_by' => $payment->created_by,
                ]);
                $lockedTaxAccount->update(['saldo' => $lockedTaxAccount->saldo + $taxBalanceChange]);

                return 'updated';
            });
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("{$nomor}: bruto Rp ".number_format($payment->jumlah, 0, ',', '.')
            .', PPh Rp '.number_format($payment->pph_nominal, 0, ',', '.')
            .', bank Rp '.number_format($payment->grand_total, 0, ',', '.')." ({$result}).");

        return self::SUCCESS;
    }
}
