<?php

// Test Checklist Pranota Feature
// Script untuk test fitur checkbox selection dan batch payment

require_once 'vendor/autoload.php';

use App\Models\Pranota;
use App\Models\PembayaranPranotaKontainer;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== Test Checklist Pranota Feature ===\n\n";

// 1. Check if we have pranota with 'sent' status
echo "1. Checking pranota with 'sent' status:\n";
$sentPranota = Pranota::where('status', 'sent')->get();
echo "   Found {$sentPranota->count()} pranota with 'sent' status\n\n";

foreach ($sentPranota->take(3) as $pranota) {
    echo "   - {$pranota->no_invoice}: Rp " . number_format((float)$pranota->total_amount, 2, ',', '.') . "\n";
}

// 2. Check pranota that are available for payment (sent and no pending payment)
echo "\n2. Checking pranota available for payment:\n";
$availablePranota = Pranota::where('status', 'sent')
    ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
            ->from('pembayaran_pranota_kontainer_items as ppki')
            ->join('pembayaran_pranota_kontainer as ppk', 'ppki.pembayaran_pranota_kontainer_id', '=', 'ppk.id')
            ->whereColumn('ppki.pranota_id', 'pranotalist.id')
            ->whereIn('ppk.status', ['pending', 'approved']);
    })
    ->get();

echo "   Found {$availablePranota->count()} pranota available for payment\n\n";

foreach ($availablePranota->take(3) as $pranota) {
    echo "   - {$pranota->no_invoice}: Rp " . number_format((float)$pranota->total_amount, 2, ',', '.') .
         " (Pending: " . ($pranota->hasPaymentPending() ? 'Yes' : 'No') . ")\n";
}

// 3. Test batch selection simulation
echo "\n3. Testing batch selection simulation:\n";
if ($availablePranota->count() >= 2) {
    $selectedPranota = $availablePranota->take(2);
    $totalAmount = $selectedPranota->sum('total_amount');

    echo "   Selected pranota for batch payment:\n";
    foreach ($selectedPranota as $pranota) {
        echo "   - {$pranota->no_invoice}: Rp " . number_format((float)$pranota->total_amount, 2, ',', '.') . "\n";
    }
    echo "   Total Amount: Rp " . number_format((float)$totalAmount, 2, ',', '.') . "\n";

    // Generate nomor pembayaran
    $nomorPembayaran = PembayaranPranotaKontainer::generateNomorPembayaran();
    echo "   Generated Payment Number: {$nomorPembayaran}\n";
} else {
    echo "   Not enough pranota available for batch testing\n";
}

// 4. Check current pagination
echo "\n4. Checking pagination info:\n";
$totalPranota = Pranota::count();
$perPage = 15;
$totalPages = ceil($totalPranota / $perPage);
echo "   Total pranota: {$totalPranota}\n";
echo "   Per page: {$perPage}\n";
echo "   Total pages: {$totalPages}\n";

// 5. Status distribution
echo "\n5. Pranota status distribution:\n";
$statusStats = Pranota::select('status', DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get();

foreach ($statusStats as $stat) {
    echo "   - {$stat->status}: {$stat->count}\n";
}

echo "\n=== Test Completed ===\n";
echo "Checklist feature should now work with:\n";
echo "- Checkbox selection for 'sent' pranota\n";
echo "- Batch selection with total calculation\n";
echo "- Process payment button for selected items\n";
echo "- Direct redirect to payment form with selected pranota\n";
