<?php
/**
 * Script untuk PREVIEW daftar tagihan kontainer dengan vendor ZONA
 * Script ini hanya menampilkan data tanpa menghapus
 */

echo "=== PREVIEW ZONA VENDOR RECORDS ===\n\n";

// Include Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

try {
    // Count records
    $zonaCount = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->count();

    if ($zonaCount == 0) {
        echo "✅ Tidak ada record dengan vendor ZONA yang ditemukan.\n";
        exit(0);
    }

    echo "📊 Total record dengan vendor ZONA: $zonaCount\n\n";

    // Group by status
    $statusGroups = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();

    echo "📈 Breakdown berdasarkan status:\n";
    foreach ($statusGroups as $group) {
        echo "  {$group->status}: {$group->count} records\n";
    }

    // Group by group field
    echo "\n📈 Breakdown berdasarkan group:\n";
    $groupBreakdown = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->selectRaw('`group`, COUNT(*) as count')
        ->groupBy('group')
        ->orderBy('count', 'desc')
        ->get();

    foreach ($groupBreakdown as $group) {
        $groupName = $group->group ?: 'NULL';
        echo "  {$groupName}: {$group->count} records\n";
    }

    // Show date range
    echo "\n📅 Rentang tanggal:\n";
    $dateRange = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->selectRaw('MIN(tanggal_awal) as earliest, MAX(tanggal_akhir) as latest')
        ->first();

    echo "  Tanggal terlama: {$dateRange->earliest}\n";
    echo "  Tanggal terbaru: {$dateRange->latest}\n";

    // Financial summary
    echo "\n💰 Summary finansial:\n";
    $financialSummary = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->selectRaw('
            SUM(dpp) as total_dpp,
            SUM(adjustment) as total_adjustment,
            SUM(ppn) as total_ppn,
            SUM(pph) as total_pph,
            SUM(grand_total) as total_grand_total
        ')
        ->first();

    echo "  Total DPP: Rp " . number_format($financialSummary->total_dpp ?? 0, 2) . "\n";
    echo "  Total Adjustment: Rp " . number_format($financialSummary->total_adjustment ?? 0, 2) . "\n";
    echo "  Total PPN: Rp " . number_format($financialSummary->total_ppn ?? 0, 2) . "\n";
    echo "  Total PPH: Rp " . number_format($financialSummary->total_pph ?? 0, 2) . "\n";
    echo "  Total Grand Total: Rp " . number_format($financialSummary->total_grand_total ?? 0, 2) . "\n";

    // Records with adjustment
    $adjustmentCount = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->where('adjustment', '!=', 0)
        ->count();
    echo "  Records dengan adjustment: $adjustmentCount\n";

    // Show sample records
    echo "\n📋 Sample 10 record vendor ZONA:\n";
    echo str_repeat("-", 120) . "\n";
    printf("%-5s %-15s %-12s %-12s %-12s %-12s %-12s\n",
        'ID', 'Container', 'Size', 'DPP', 'Adjustment', 'Status', 'Group');
    echo str_repeat("-", 120) . "\n";

    $sampleRecords = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->select(['id', 'nomor_kontainer', 'size', 'dpp', 'adjustment', 'status', 'group'])
        ->limit(10)
        ->get();

    foreach ($sampleRecords as $record) {
        printf("%-5s %-15s %-12s %-12s %-12s %-12s %-12s\n",
            $record->id,
            substr($record->nomor_kontainer, 0, 14),
            $record->size,
            number_format($record->dpp ?? 0),
            number_format($record->adjustment ?? 0),
            $record->status,
            $record->group ?: '-'
        );
    }

    if ($zonaCount > 10) {
        echo "... dan " . ($zonaCount - 10) . " record lainnya\n";
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "⚠️  Jika Anda yakin ingin menghapus $zonaCount record ini,\n";
    echo "   jalankan script: php delete_zona_records.php\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== PREVIEW SELESAI ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
