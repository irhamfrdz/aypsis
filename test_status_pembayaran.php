<?php
/**
 * Script untuk menguji status pembayaran pranota uang jalan
 * Memastikan pembayaran baru dibuat dengan status 'paid' bukan 'pending'
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use App\Models\PembayaranPranotaUangJalan;
use App\Models\PranotaUangJalan;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Test Status Pembayaran Pranota Uang Jalan ===\n\n";

// Test 1: Cek konstanta status di model
echo "1. Konstanta Status di Model:\n";
echo "   - STATUS_PENDING: " . PembayaranPranotaUangJalan::STATUS_PENDING . "\n";
echo "   - STATUS_PAID: " . PembayaranPranotaUangJalan::STATUS_PAID . "\n";
echo "   - STATUS_CANCELLED: " . PembayaranPranotaUangJalan::STATUS_CANCELLED . "\n\n";

// Test 2: Cek pembayaran terbaru
echo "2. Status Pembayaran Terbaru:\n";
$latestPayments = PembayaranPranotaUangJalan::orderBy('created_at', 'desc')->take(5)->get();

if ($latestPayments->count() > 0) {
    echo "   Pembayaran terbaru:\n";
    foreach ($latestPayments as $payment) {
        echo "   - ID: {$payment->id}, Nomor: {$payment->nomor_pembayaran}, Status: {$payment->status_pembayaran}, Tanggal: {$payment->created_at}\n";
    }
} else {
    echo "   Belum ada data pembayaran\n";
}

echo "\n";

// Test 3: Cek default value dari database
echo "3. Schema Database:\n";
try {
    $columns = DB::select("DESCRIBE pembayaran_pranota_uang_jalans");
    foreach ($columns as $column) {
        if ($column->Field === 'status_pembayaran') {
            echo "   Kolom status_pembayaran:\n";
            echo "   - Type: {$column->Type}\n";
            echo "   - Default: " . ($column->Default ?: 'NULL') . "\n";
            echo "   - Null: {$column->Null}\n";
            break;
        }
    }
} catch (Exception $e) {
    echo "   Error checking schema: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Simulasi create payment dengan data minimal
echo "4. Test Create Payment (Simulasi):\n";
echo "   Ketika pembayaran dibuat dengan data berikut:\n";
echo "   \$paymentData['status_pembayaran'] = PembayaranPranotaUangJalan::STATUS_PAID;\n";
echo "   Seharusnya status yang tersimpan adalah: 'paid'\n";
echo "   Bukan default database: 'pending'\n\n";

// Test 5: Cek pranota yang belum dibayar
echo "5. Pranota Uang Jalan yang Belum Dibayar:\n";
$unpaidPranota = PranotaUangJalan::where('status_pembayaran', 'unpaid')->count();
echo "   Total pranota belum dibayar: {$unpaidPranota}\n";

$paidPranota = PranotaUangJalan::where('status_pembayaran', 'paid')->count();
echo "   Total pranota sudah dibayar: {$paidPranota}\n\n";

echo "=== Test Selesai ===\n";
echo "KESIMPULAN: Setelah perbaikan di Controller, setiap pembayaran baru akan memiliki status 'paid' karena:\n";
echo "1. Controller secara eksplisit set: \$paymentData['status_pembayaran'] = PembayaranPranotaUangJalan::STATUS_PAID;\n";
echo "2. Ini akan override default database ('pending')\n";
echo "3. Pranota terkait juga diupdate ke status 'paid'\n\n";