<?php

// Jalankan dari root proyek: php repair_pembayaran_rit_kenek_btj26_1000123.php [--execute]
// Tanpa --execute hanya memeriksa data. Skrip ini khusus untuk satu pembayaran.

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$execute = in_array('--execute', $argv, true);
$accurate = 'BTJ26 1000123';
$expectedReference = 'SIS-K-10-26-001209';
$expectedDate = '2026-10-10';
$expectedAmount = '1100000.00';
$expenseCode = '5101.001';
$expenseName = 'Biaya Insentif Supir Truck Jakarta';
$created = false;

try {
    DB::transaction(function () use ($execute, $accurate, $expectedReference, $expectedDate, $expectedAmount, $expenseCode, $expenseName, &$created): void {
        $paymentQuery = DB::table('pembayaran_pranota_rit_keneks')->where('nomor_accurate', $accurate);
        $payments = ($execute ? $paymentQuery->lockForUpdate() : $paymentQuery)->get();
        if ($payments->count() !== 1) {
            throw new RuntimeException("Ditemukan {$payments->count()} pembayaran dengan nomor Accurate {$accurate}; harus tepat satu.");
        }

        $payment = $payments->first();
        if ($payment->nomor_pembayaran !== $expectedReference
            || substr((string) $payment->tanggal_pembayaran, 0, 10) !== $expectedDate
            || number_format((float) $payment->total_tagihan_setelah_penyesuaian, 2, '.', '') !== $expectedAmount
            || $payment->jenis_transaksi !== 'Kredit'
            || $payment->status_pembayaran !== 'paid') {
            throw new RuntimeException('Data pembayaran berbeda dari data yang sudah diperiksa; tidak ada perubahan.');
        }

        $bankQuery = DB::table('akun_coa')->where('nama_akun', $payment->bank);
        $banks = ($execute ? $bankQuery->lockForUpdate() : $bankQuery)->get();
        $expenseQuery = DB::table('akun_coa')->where('nomor_akun', $expenseCode)->where('nama_akun', $expenseName);
        $expenses = ($execute ? $expenseQuery->lockForUpdate() : $expenseQuery)->get();
        if ($banks->count() !== 1 || $expenses->count() !== 1 || $banks->first()->id === $expenses->first()->id) {
            throw new RuntimeException('Akun bank atau akun biaya tidak ditemukan secara unik; tidak ada perubahan.');
        }

        $bank = $banks->first();
        $expense = $expenses->first();
        $journalQuery = DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor_pembayaran);
        $existing = ($execute ? $journalQuery->lockForUpdate() : $journalQuery)->get();
        if ($existing->isNotEmpty()) {
            echo "Jurnal {$payment->nomor_pembayaran} sudah ada ({$existing->count()} baris). Tidak menambahkan jurnal agar tidak ganda.\n";

            return;
        }

        echo "Pembayaran: {$payment->nomor_pembayaran} / {$accurate}\n";
        echo "Tanggal: {$expectedDate}; nominal: Rp {$expectedAmount}\n";
        echo "Debit {$expense->nomor_akun} {$expense->nama_akun}; kredit {$bank->nama_akun}\n";
        if (! $execute) {
            echo "Pratinjau saja. Jalankan kembali dengan --execute untuk menyimpan.\n";

            return;
        }

        $amount = $expectedAmount;
        $now = now();
        $description = 'Pembayaran Pranota Rit Kenek - '.$payment->nomor_pembayaran;
        $base = [
            'tanggal_transaksi' => $expectedDate,
            'nomor_referensi' => $payment->nomor_pembayaran,
            'jenis_transaksi' => 'Pembayaran Pranota Rit Kenek',
            'keterangan' => $description,
            'created_by' => $payment->created_by,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::update('UPDATE akun_coa SET saldo = saldo + ?, updated_at = ? WHERE id = ?', [$amount, $now, $expense->id]);
        $expenseBalance = DB::table('akun_coa')->where('id', $expense->id)->value('saldo');
        DB::table('coa_transactions')->insert($base + [
            'coa_id' => $expense->id,
            'debit' => $expectedAmount,
            'kredit' => '0.00',
            'saldo' => $expenseBalance,
        ]);

        DB::update('UPDATE akun_coa SET saldo = saldo - ?, updated_at = ? WHERE id = ?', [$amount, $now, $bank->id]);
        $bankBalance = DB::table('akun_coa')->where('id', $bank->id)->value('saldo');
        DB::table('coa_transactions')->insert($base + [
            'coa_id' => $bank->id,
            'debit' => '0.00',
            'kredit' => $expectedAmount,
            'saldo' => $bankBalance,
        ]);

        $created = true;
    });
    if ($created) {
        echo "Selesai: dua baris jurnal dibuat. Pembayaran akan tampil di Kas Truck Oktober 2026.\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Gagal; transaksi dibatalkan: '.$e->getMessage().PHP_EOL);
    exit(1);
}
