<?php

/**
 * Script untuk menguji fungsi grouping pranota kontainer sewa
 * Berdasarkan analisis data Zona CSV
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\DaftarTagihanKontainerSewa;
use App\Http\Controllers\PranotaTagihanKontainerSewaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "=== TEST GROUPING PRANOTA KONTAINER SEWA ===\n\n";

try {
    // Create sample test data based on analysis
    echo "1. Membuat sample data untuk testing...\n";

    // Group 1: 5 kontainer dengan invoice ZONA25.05.28123 dan bank EBK250600289
    $group1Data = [
        ['kontainer' => 'FORU8416289', 'no_invoice_vendor' => 'ZONA25.05.28123', 'no_bank' => 'EBK250600289', 'grand_total' => 564640],
        ['kontainer' => 'FORU8480890', 'no_invoice_vendor' => 'ZONA25.05.28123', 'no_bank' => 'EBK250600289', 'grand_total' => 564640],
        ['kontainer' => 'IFLU2990380', 'no_invoice_vendor' => 'ZONA25.05.28123', 'no_bank' => 'EBK250600289', 'grand_total' => 564640],
        ['kontainer' => 'MSKU2218091', 'no_invoice_vendor' => 'ZONA25.05.28123', 'no_bank' => 'EBK250600289', 'grand_total' => 564640],
        ['kontainer' => 'TTNU3216943', 'no_invoice_vendor' => 'ZONA25.05.28123', 'no_bank' => 'EBK250600289', 'grand_total' => 564640]
    ];

    // Group 2: 4 kontainer dengan invoice ZONA24.01.22359 dan bank EBK240500055
    $group2Data = [
        ['kontainer' => 'EGHU9005182', 'no_invoice_vendor' => 'ZONA24.01.22359', 'no_bank' => 'EBK240500055', 'grand_total' => 1374775],
        ['kontainer' => 'NYKU5622053', 'no_invoice_vendor' => 'ZONA24.01.22359', 'no_bank' => 'EBK240500055', 'grand_total' => 1374775],
        ['kontainer' => 'MSCU9903797', 'no_invoice_vendor' => 'ZONA24.01.22359', 'no_bank' => 'EBK240500055', 'grand_total' => 1374775],
        ['kontainer' => 'VGCU2886097', 'no_invoice_vendor' => 'ZONA24.01.22359', 'no_bank' => 'EBK240500055', 'grand_total' => 1374775]
    ];

    // Group 3: Single kontainer dengan invoice ZONA24.02.22500 dan bank EBK240500055
    $group3Data = [
        ['kontainer' => 'TDRU6124340', 'no_invoice_vendor' => 'ZONA24.02.22500', 'no_bank' => 'EBK240500055', 'grand_total' => 1374775]
    ];

    // Group 4: Kontainer tanpa nomor bank (tidak akan diproses)
    $group4Data = [
        ['kontainer' => 'BMOU2495277', 'no_invoice_vendor' => 'ZONA23.07.20493', 'no_bank' => '', 'grand_total' => 736486],
        ['kontainer' => 'GESU2832175', 'no_invoice_vendor' => 'ZONA23.07.20493', 'no_bank' => '', 'grand_total' => 736486]
    ];

    // Group 5: Kontainer tanpa invoice vendor (tidak akan diproses)
    $group5Data = [
        ['kontainer' => 'CMAU1034669', 'no_invoice_vendor' => '', 'no_bank' => 'EBK240500055', 'grand_total' => 500000]
    ];

    $allTestData = array_merge($group1Data, $group2Data, $group3Data, $group4Data, $group5Data);

    // Insert test data (dalam implementasi nyata, ini sudah ada di database)
    echo "Sample data yang akan diuji:\n";
    foreach ($allTestData as $index => $data) {
        echo "- {$data['kontainer']}: Invoice {$data['no_invoice_vendor']}, Bank {$data['no_bank']}\n";
    }
    echo "\n";

    // Simulate the grouping logic from controller
    echo "2. Testing grouping logic...\n";

    $controller = new PranotaTagihanKontainerSewaController();

    // Test the private grouping method using reflection
    $reflection = new ReflectionClass($controller);
    $groupingMethod = $reflection->getMethod('groupKontainerByVendorInvoiceAndBank');
    $groupingMethod->setAccessible(true);

    // Convert array to collection to simulate database result
    $tagihanItems = collect($allTestData)->map(function($item) {
        return (object) [
            'id' => rand(1000, 9999), // Random ID for testing
            'kontainer' => $item['kontainer'],
            'no_invoice_vendor' => $item['no_invoice_vendor'],
            'no_bank' => $item['no_bank'],
            'grand_total' => $item['grand_total'],
            'tgl_invoice_vendor' => '2024-01-01',
            'tgl_bank' => '2024-01-01',
            'supplier' => 'ZONA'
        ];
    });

    // Call grouping method
    $groupedResult = $groupingMethod->invoke($controller, $tagihanItems);

    echo "Hasil grouping:\n";
    echo "Total groups yang terbentuk: " . count($groupedResult) . "\n\n";

    $groupNumber = 1;
    foreach ($groupedResult as $groupKey => $group) {
        echo "GROUP #{$groupNumber}:\n";
        echo "- Key: {$groupKey}\n";
        echo "- Invoice Vendor: {$group['no_invoice_vendor']}\n";
        echo "- Nomor Bank: {$group['no_bank']}\n";
        echo "- Jumlah Kontainer: " . count($group['items']) . "\n";
        echo "- Total Amount: Rp " . number_format(collect($group['items'])->sum('grand_total'), 2, ',', '.') . "\n";
        echo "- Kontainer: " . implode(', ', collect($group['items'])->pluck('kontainer')->toArray()) . "\n";
        echo "\n";
        $groupNumber++;
    }

    // Test expected results
    echo "3. Validasi hasil:\n";

    $expectedGroups = [
        'ZONA25.05.28123|EBK250600289' => 5, // Group 1: 5 kontainer
        'ZONA24.01.22359|EBK240500055' => 4, // Group 2: 4 kontainer
        'ZONA24.02.22500|EBK240500055' => 1, // Group 3: 1 kontainer
    ];

    $allTestsPassed = true;

    foreach ($expectedGroups as $expectedKey => $expectedCount) {
        if (isset($groupedResult[$expectedKey])) {
            $actualCount = count($groupedResult[$expectedKey]['items']);
            if ($actualCount == $expectedCount) {
                echo "✓ Group {$expectedKey}: {$actualCount} kontainer (sesuai)\n";
            } else {
                echo "✗ Group {$expectedKey}: Expected {$expectedCount}, got {$actualCount}\n";
                $allTestsPassed = false;
            }
        } else {
            echo "✗ Group {$expectedKey}: Tidak ditemukan\n";
            $allTestsPassed = false;
        }
    }

    // Test that incomplete data is filtered out
    $kontainerWithoutBank = collect($tagihanItems)->filter(function($item) {
        return empty($item->no_bank);
    })->count();

    $kontainerWithoutInvoice = collect($tagihanItems)->filter(function($item) {
        return empty($item->no_invoice_vendor);
    })->count();

    echo "\n4. Test filtering data tidak lengkap:\n";
    echo "- Kontainer tanpa nomor bank: {$kontainerWithoutBank} (tidak masuk grouping)\n";
    echo "- Kontainer tanpa invoice vendor: {$kontainerWithoutInvoice} (tidak masuk grouping)\n";

    $totalProcessableItems = collect($tagihanItems)->filter(function($item) {
        return !empty($item->no_invoice_vendor) && !empty($item->no_bank);
    })->count();

    $totalItemsInGroups = collect($groupedResult)->sum(function($group) {
        return count($group['items']);
    });

    if ($totalProcessableItems == $totalItemsInGroups) {
        echo "✓ Semua kontainer dengan data lengkap masuk grouping ({$totalItemsInGroups}/{$totalProcessableItems})\n";
    } else {
        echo "✗ Mismatch: Expected {$totalProcessableItems}, got {$totalItemsInGroups} in groups\n";
        $allTestsPassed = false;
    }

    echo "\n5. Simulasi pembuatan pranota:\n";
    foreach ($groupedResult as $groupKey => $group) {
        $totalAmount = collect($group['items'])->sum('grand_total');
        $kontainerCount = count($group['items']);

        echo "Akan membuat pranota untuk:\n";
        echo "- Invoice: {$group['no_invoice_vendor']}\n";
        echo "- Bank: {$group['no_bank']}\n";
        echo "- {$kontainerCount} kontainer\n";
        echo "- Total: Rp " . number_format($totalAmount, 2, ',', '.') . "\n";
        echo "- Keterangan: Pranota kontainer sewa - Invoice Vendor: {$group['no_invoice_vendor']}, No Bank: {$group['no_bank']} ({$kontainerCount} kontainer)\n\n";
    }

    echo "=== HASIL TEST ===\n";
    if ($allTestsPassed) {
        echo "✓ SEMUA TEST BERHASIL!\n";
        echo "Logika grouping berfungsi dengan benar.\n";
        echo "Total pranota yang akan dibuat: " . count($groupedResult) . "\n";
        echo "Total kontainer yang diproses: {$totalItemsInGroups}\n";
        echo "Penghematan: " . ($totalItemsInGroups - count($groupedResult)) . " pranota\n";
    } else {
        echo "✗ ADA TEST YANG GAGAL!\n";
        echo "Periksa kembali logika grouping.\n";
    }

    echo "\n=== CONTOH USAGE DI FRONTEND ===\n";
    echo "1. User memilih beberapa tagihan kontainer sewa di halaman daftar tagihan\n";
    echo "2. User klik tombol 'Buat Pranota Berdasarkan Invoice & Bank'\n";
    echo "3. System akan:\n";
    echo "   - Filter kontainer yang memiliki no_invoice_vendor dan no_bank\n";
    echo "   - Group berdasarkan kombinasi invoice_vendor + no_bank\n";
    echo "   - Buat satu pranota per group\n";
    echo "   - Update status tagihan menjadi 'included'\n";
    echo "4. User mendapat feedback tentang jumlah pranota yang dibuat\n\n";

    echo "Endpoint yang tersedia:\n";
    echo "- POST /pranota-kontainer-sewa/create-by-vendor-invoice-group\n";
    echo "- POST /pranota-kontainer-sewa/preview-vendor-invoice-grouping (untuk preview sebelum create)\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== SELESAI TEST ===\n";
