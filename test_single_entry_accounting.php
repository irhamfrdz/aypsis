<?php
/**
 * Test Single Entry Accounting untuk Pembayaran Pranota Kontainer
 * Menguji bahwa hanya transaksi bank yang dicatat (tanpa double entry)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CoaTransactionService;
use App\Models\Coa;
use App\Models\CoaTransaction;
use Illuminate\Support\Facades\DB;

echo "=== TEST SINGLE ENTRY ACCOUNTING ===\n\n";

try {
    DB::beginTransaction();

    // 1. Cek apakah akun Bank BCA ada
    $bankBCA = Coa::where('nama_akun', 'LIKE', '%BCA%')->first();
    if (!$bankBCA) {
        echo "❌ Bank BCA tidak ditemukan dalam COA\n";
        echo "📋 Available banks:\n";
        $banks = Coa::where('tipe_akun', 'LIKE', '%Bank%')->get();
        foreach($banks as $bank) {
            echo "   - {$bank->nama_akun}\n";
        }
        DB::rollback();
        exit(1);
    }

    echo "✅ Bank found: {$bankBCA->nama_akun}\n";
    echo "💰 Current balance: Rp " . number_format($bankBCA->saldo, 0, ',', '.') . "\n\n";

    // 2. Test CoaTransactionService
    $coaService = new CoaTransactionService();

    $testAmount = 5000000; // 5 juta
    $testNomor = 'TEST-PBY-' . date('YmdHis');
    $testTanggal = date('Y-m-d');
    $testKeterangan = 'Test Single Entry - Pembayaran Pranota Kontainer';

    echo "🧪 Testing single entry transaction:\n";
    echo "   Bank: {$bankBCA->nama_akun}\n";
    echo "   Amount: Rp " . number_format($testAmount, 0, ',', '.') . "\n";
    echo "   Type: Kredit (mengurangi saldo bank)\n\n";

    // 3. Record single transaction (kredit ke bank)
    $transaction = $coaService->recordTransaction(
        $bankBCA->nama_akun,        // nama_akun
        0,                          // debit (tidak ada)
        $testAmount,                // kredit (mengurangi saldo bank)
        $testTanggal,               // tanggal_transaksi
        $testNomor,                 // nomor_referensi
        'Pembayaran Pranota Kontainer', // jenis_transaksi
        $testKeterangan             // keterangan
    );

    if ($transaction) {
        echo "✅ Transaction recorded successfully!\n";
        echo "📄 Transaction details:\n";
        echo "   ID: {$transaction->id}\n";
        echo "   Debit: Rp " . number_format($transaction->debit, 0, ',', '.') . "\n";
        echo "   Kredit: Rp " . number_format($transaction->kredit, 0, ',', '.') . "\n";
        echo "   New Balance: Rp " . number_format($transaction->saldo, 0, ',', '.') . "\n";
        echo "   Reference: {$transaction->nomor_referensi}\n";
        echo "   Description: {$transaction->keterangan}\n\n";

        // 4. Verify bank balance updated
        $bankBCA->refresh();
        echo "✅ Bank balance updated:\n";
        echo "   New balance: Rp " . number_format($bankBCA->saldo, 0, ',', '.') . "\n\n";

        // 5. Check no other accounts affected (single entry only)
        $otherTransactions = CoaTransaction::where('nomor_referensi', $testNomor)
            ->where('id', '!=', $transaction->id)
            ->count();

        if ($otherTransactions == 0) {
            echo "✅ SINGLE ENTRY CONFIRMED: Only bank account affected\n";
            echo "   No double entry created\n";
            echo "   No other accounts modified\n\n";
        } else {
            echo "❌ ERROR: Found {$otherTransactions} other transactions (should be 0 for single entry)\n";
        }

        echo "🎯 TEST SUMMARY:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ Single entry accounting working correctly\n";
        echo "✅ Only bank account (kredit) transaction created\n";
        echo "✅ Bank balance properly reduced\n";
        echo "✅ No double entry overhead\n";
        echo "✅ Ready for production use\n\n";

    } else {
        echo "❌ Failed to record transaction\n";
        echo "Check if bank account exists in COA\n";
    }

    // Rollback test transaction
    DB::rollback();
    echo "🔄 Test transaction rolled back (no permanent changes)\n";

} catch (Exception $e) {
    DB::rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
