<?php
/**
 * Demonstrasi grouping berdasarkan nomor invoice vendor yang sama
 * Menunjukkan bagaimana data akan dikelompokkan untuk 1 pranota per invoice
 */

echo "=== DEMO GROUPING BY INVOICE NUMBER ===\n\n";

$csvFile = 'Zona.csv';

if (!file_exists($csvFile)) {
    echo "❌ File tidak ditemukan: $csvFile\n";
    exit(1);
}

// Baca CSV file
$handle = fopen($csvFile, 'r');
$headers = fgetcsv($handle, 1000, ';');

$invoiceGroups = [];
$rowCount = 0;

echo "📊 Membaca dan mengelompokkan data berdasarkan nomor invoice...\n\n";

while (($row = fgetcsv($handle, 1000, ';')) !== false) {
    $rowCount++;

    if ($rowCount === 1 || count($row) < 20) continue;

    $noInvoiceVendor = trim($row[17] ?? '');
    $noBank = trim($row[19] ?? '');
    $tglBank = trim($row[20] ?? '');
    $kontainer = trim($row[1] ?? '');
    $group = trim($row[0] ?? '');

    // Skip jika tidak lengkap
    if (empty($noInvoiceVendor) || empty($noBank) || $noBank === '-') {
        continue;
    }

    // Kelompokkan berdasarkan nomor invoice vendor
    if (!isset($invoiceGroups[$noInvoiceVendor])) {
        $invoiceGroups[$noInvoiceVendor] = [
            'no_invoice_vendor' => $noInvoiceVendor,
            'no_bank' => $noBank,
            'tgl_bank' => $tglBank,
            'containers' => []
        ];
    }

    // Tambahkan container ke grup invoice yang sama
    $invoiceGroups[$noInvoiceVendor]['containers'][] = [
        'group' => $group,
        'container' => $kontainer
    ];
}

fclose($handle);

echo "✅ Analisis selesai!\n\n";

echo "📋 HASIL PENGELOMPOKAN:\n";
echo "━" . str_repeat("━", 80) . "\n";
printf("%-25s %-15s %-12s %s\n", 'Invoice Number', 'Bank Number', 'Bank Date', 'Containers');
echo "━" . str_repeat("━", 80) . "\n";

$totalPranota = 0;
$totalContainers = 0;

foreach ($invoiceGroups as $invoice => $group) {
    $containerCount = count($group['containers']);
    $totalPranota++;
    $totalContainers += $containerCount;

    printf("%-25s %-15s %-12s %d containers\n",
        substr($invoice, 0, 24),
        $group['no_bank'],
        $group['tgl_bank'],
        $containerCount
    );

    // Show first few containers for this invoice
    if ($containerCount <= 3) {
        foreach ($group['containers'] as $container) {
            echo "    └─ {$container['group']} - {$container['container']}\n";
        }
    } else {
        // Show first 2 and indicate more
        for ($i = 0; $i < 2; $i++) {
            $container = $group['containers'][$i];
            echo "    └─ {$container['group']} - {$container['container']}\n";
        }
        echo "    └─ ... dan " . ($containerCount - 2) . " container lainnya\n";
    }
    echo "\n";
}

echo "━" . str_repeat("━", 80) . "\n";
echo "📊 RINGKASAN:\n";
echo "• Total Pranota yang akan dibuat: $totalPranota\n";
echo "• Total Container/Tagihan: $totalContainers\n";
echo "• Rata-rata container per pranota: " . round($totalContainers / $totalPranota, 1) . "\n\n";

echo "🔍 CONTOH DETAIL UNTUK 3 INVOICE PERTAMA:\n";
echo "━" . str_repeat("━", 60) . "\n";

$count = 0;
foreach ($invoiceGroups as $invoice => $group) {
    if ($count >= 3) break;

    echo "\n📝 PRANOTA #" . ($count + 1) . "\n";
    echo "   📋 Invoice Vendor: $invoice\n";
    echo "   🏦 Bank: {$group['no_bank']} (Tanggal: {$group['tgl_bank']})\n";
    echo "   📦 Containers (" . count($group['containers']) . "):\n";

    foreach ($group['containers'] as $container) {
        echo "      • {$container['group']} - {$container['container']}\n";
    }

    echo "   ✅ Hasil: 1 Pranota dengan " . count($group['containers']) . " tagihan\n";

    $count++;
}

echo "\n💡 KONSEP PENGELOMPOKAN:\n";
echo "━" . str_repeat("━", 50) . "\n";
echo "• Jika Invoice Number SAMA → GABUNG ke 1 Pranota\n";
echo "• Jika Invoice Number BEDA → Buat Pranota Terpisah\n";
echo "• Setiap Container = 1 Tagihan dalam Pranota\n";
echo "• Tanggal Pranota = Tanggal Bank\n\n";

echo "🚀 READY TO IMPORT!\n";
echo "Jalankan: php import_csv_to_pranota.php\n";
echo "=== DEMO SELESAI ===\n";
