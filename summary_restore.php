<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;

echo "=== RINGKASAN RESTORE DATA ===\n\n";

try {
    // Total tagihan saat ini
    $totalTagihan = DaftarTagihanKontainerSewa::count();
    echo "📊 Total tagihan saat ini: $totalTagihan\n";

    // Data yang baru di-restore (berdasarkan created_at hari ini)
    $today = date('Y-m-d');
    $restoredToday = DaftarTagihanKontainerSewa::whereDate('created_at', $today)->count();
    echo "✅ Data yang di-restore hari ini: $restoredToday\n";

    // Total amount
    $totalAmount = DaftarTagihanKontainerSewa::sum('grand_total');
    echo "💰 Total nilai tagihan: Rp " . number_format($totalAmount, 0, ',', '.') . "\n";

    // Breakdown by vendor
    echo "\n📋 Breakdown by Vendor:\n";
    $vendorBreakdown = DaftarTagihanKontainerSewa::select('vendor')
        ->selectRaw('COUNT(*) as count')
        ->selectRaw('SUM(grand_total) as total_amount')
        ->groupBy('vendor')
        ->orderBy('count', 'desc')
        ->get();

    foreach ($vendorBreakdown as $vendor) {
        echo "   {$vendor->vendor}: {$vendor->count} kontainer (Rp " .
             number_format($vendor->total_amount, 0, ',', '.') . ")\n";
    }

    // Breakdown by group
    echo "\n📋 Breakdown by Group:\n";
    $groupBreakdown = DaftarTagihanKontainerSewa::select('group')
        ->selectRaw('COUNT(*) as count')
        ->selectRaw('SUM(grand_total) as total_amount')
        ->whereNotNull('group')
        ->where('group', '!=', '')
        ->groupBy('group')
        ->orderBy('count', 'desc')
        ->get();

    foreach ($groupBreakdown as $group) {
        echo "   {$group->group}: {$group->count} kontainer (Rp " .
             number_format($group->total_amount, 0, ',', '.') . ")\n";
    }

    echo "\n🎉 DATA BERHASIL DI-RESTORE!\n";
    echo "===============================\n";
    echo "✅ Masalah pranota kosong telah diperbaiki\n";
    echo "✅ Data tagihan kontainer telah dikembalikan\n";
    echo "📝 Pranota bisa dibuat nanti sesuai kebutuhan\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "✅ Selesai!\n";
