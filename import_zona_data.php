<?php

/**
 * Script untuk import data CSV Zona ke database untuk testing fitur grouping
 * Hanya import kontainer yang memiliki invoice vendor dan nomor bank
 */

// Path ke file CSV
$csvFile = 'c:\Users\amanda\Downloads\Zona.csv';

if (!file_exists($csvFile)) {
    die("File CSV tidak ditemukan: {$csvFile}\n");
}

echo "=== IMPORT DATA ZONA KE DATABASE ===\n\n";

// Database connection (adjust sesuai config Laravel)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=aypsis", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Koneksi database berhasil\n";
} catch (PDOException $e) {
    die("Error koneksi database: " . $e->getMessage() . "\n");
}

// Read CSV data
$csvData = [];
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    $header = fgetcsv($handle, 1000, ";");

    // Find column indices
    $kontainerIndex = array_search('Kontainer', $header);
    $invoiceVendorIndex = array_search('No.InvoiceVendor', $header);
    $bankIndex = array_search('No.Bank', $header);
    $tglInvoiceIndex = array_search('Tgl.InvVendor', $header);
    $tglBankIndex = array_search('Tgl.Bank', $header);
    $grandTotalIndex = array_search(' grand_total ', $header);
    $groupIndex = array_search('Group', $header);
    $awalIndex = array_search('Awal', $header);
    $akhirIndex = array_search('Akhir', $header);
    $ukuranIndex = array_search('Ukuran', $header);
    $hargaIndex = array_search(' Harga ', $header);
    $dppIndex = array_search(' DPP ', $header);
    $ppnIndex = array_search(' ppn ', $header);
    $pphIndex = array_search(' pph ', $header);

    echo "Header CSV ditemukan. Membaca data...\n";

    // Read data rows
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        if (empty($data[$kontainerIndex]) || $data[$kontainerIndex] == 'Kontainer') {
            continue;
        }

        $invoiceVendor = trim($data[$invoiceVendorIndex] ?? '');
        $noBank = trim($data[$bankIndex] ?? '');

        // Hanya ambil data yang memiliki invoice vendor dan nomor bank
        if (!empty($invoiceVendor) && !empty($noBank)) {
            $csvData[] = [
                'group' => trim($data[$groupIndex] ?? ''),
                'kontainer' => trim($data[$kontainerIndex] ?? ''),
                'awal' => trim($data[$awalIndex] ?? ''),
                'akhir' => trim($data[$akhirIndex] ?? ''),
                'ukuran' => trim($data[$ukuranIndex] ?? ''),
                'harga' => floatval(str_replace([' ', '.', ','], ['', '', '.'], $data[$hargaIndex] ?? '0')),
                'dpp' => floatval(str_replace([' ', '.', ','], ['', '', '.'], $data[$dppIndex] ?? '0')),
                'ppn' => floatval(str_replace([' ', '.', ','], ['', '', '.'], $data[$ppnIndex] ?? '0')),
                'pph' => floatval(str_replace([' ', '.', ','], ['', '', '.'], $data[$pphIndex] ?? '0')),
                'grand_total' => floatval(str_replace([' ', '.', ','], ['', '', '.'], $data[$grandTotalIndex] ?? '0')),
                'no_invoice_vendor' => $invoiceVendor,
                'tgl_invoice_vendor' => parseDate($data[$tglInvoiceIndex] ?? ''),
                'no_bank' => $noBank,
                'tgl_bank' => parseDate($data[$tglBankIndex] ?? ''),
            ];
        }
    }

    fclose($handle);
}

function parseDate($dateString) {
    if (empty($dateString)) return null;

    // Format date dari CSV (contoh: "01 Feb 24")
    $dateString = trim($dateString);
    if (strlen($dateString) > 0) {
        try {
            $date = DateTime::createFromFormat('d M y', $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
        } catch (Exception $e) {
            // Jika gagal parse, return null
        }
    }
    return null;
}

echo "Total data dengan invoice vendor & nomor bank: " . count($csvData) . "\n\n";

if (empty($csvData)) {
    die("Tidak ada data yang bisa diimport.\n");
}

// Cek apakah tabel sudah ada
try {
    $checkTable = $pdo->query("SHOW TABLES LIKE 'daftar_tagihan_kontainer_sewa'");
    if ($checkTable->rowCount() == 0) {
        die("Tabel 'daftar_tagihan_kontainer_sewa' tidak ditemukan. Silakan buat tabel terlebih dahulu.\n");
    }
    echo "✓ Tabel daftar_tagihan_kontainer_sewa ditemukan\n";
} catch (PDOException $e) {
    die("Error checking table: " . $e->getMessage() . "\n");
}

// Clear existing test data (opsional)
$response = readline("Hapus data existing dengan supplier 'ZONA'? (y/N): ");
if (strtolower($response) == 'y') {
    try {
        $stmt = $pdo->prepare("DELETE FROM daftar_tagihan_kontainer_sewa WHERE supplier = 'ZONA'");
        $stmt->execute();
        echo "✓ Data existing ZONA telah dihapus\n";
    } catch (PDOException $e) {
        echo "Warning: Gagal menghapus data existing: " . $e->getMessage() . "\n";
    }
}

// Insert data
echo "\nMulai import data...\n";

$insertQuery = "
    INSERT INTO daftar_tagihan_kontainer_sewa
    (kontainer, tgl_awal, tgl_akhir, ukuran, harga, dpp, ppn, pph, grand_total,
     no_invoice_vendor, tgl_invoice_vendor, no_bank, tgl_bank,
     supplier, status_pranota, catatan, created_at, updated_at)
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ZONA', 'pending', ?, NOW(), NOW())
";

$insertStmt = $pdo->prepare($insertQuery);

$successCount = 0;
$errorCount = 0;
$errors = [];

foreach ($csvData as $index => $row) {
    try {
        $catatan = "Import dari CSV Zona - Group: {$row['group']}";

        $insertStmt->execute([
            $row['kontainer'],
            $row['awal'] ? date('Y-m-d', strtotime($row['awal'])) : null,
            $row['akhir'] ? date('Y-m-d', strtotime($row['akhir'])) : null,
            $row['ukuran'],
            $row['harga'],
            $row['dpp'],
            $row['ppn'],
            $row['pph'],
            $row['grand_total'],
            $row['no_invoice_vendor'],
            $row['tgl_invoice_vendor'],
            $row['no_bank'],
            $row['tgl_bank'],
            $catatan
        ]);

        $successCount++;

        if ($successCount % 50 == 0) {
            echo "Progress: {$successCount} data berhasil diimport\n";
        }

    } catch (PDOException $e) {
        $errorCount++;
        $errors[] = "Row " . ($index + 1) . " - {$row['kontainer']}: " . $e->getMessage();

        if ($errorCount <= 10) { // Tampilkan max 10 error
            echo "Error: {$row['kontainer']} - " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== HASIL IMPORT ===\n";
echo "✓ Berhasil: {$successCount} data\n";
if ($errorCount > 0) {
    echo "✗ Error: {$errorCount} data\n";
    if ($errorCount > 10) {
        echo "   (hanya 10 error pertama yang ditampilkan)\n";
    }
}

// Analisis hasil import
if ($successCount > 0) {
    echo "\n=== ANALISIS DATA YANG DIIMPORT ===\n";

    try {
        // Count by invoice vendor
        $stmt = $pdo->query("
            SELECT no_invoice_vendor, COUNT(*) as count, SUM(grand_total) as total_amount
            FROM daftar_tagihan_kontainer_sewa
            WHERE supplier = 'ZONA'
            GROUP BY no_invoice_vendor
            ORDER BY count DESC
            LIMIT 10
        ");

        echo "Top 10 Invoice Vendor:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['no_invoice_vendor']}: {$row['count']} kontainer, Rp " . number_format($row['total_amount'], 2, ',', '.') . "\n";
        }

        // Count by bank
        $stmt = $pdo->query("
            SELECT no_bank, COUNT(*) as count, SUM(grand_total) as total_amount
            FROM daftar_tagihan_kontainer_sewa
            WHERE supplier = 'ZONA'
            GROUP BY no_bank
            ORDER BY count DESC
            LIMIT 10
        ");

        echo "\nTop 10 Nomor Bank:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['no_bank']}: {$row['count']} kontainer, Rp " . number_format($row['total_amount'], 2, ',', '.') . "\n";
        }

        // Analisis grouping potential
        $stmt = $pdo->query("
            SELECT
                CONCAT(no_invoice_vendor, '|', no_bank) as group_key,
                no_invoice_vendor,
                no_bank,
                COUNT(*) as kontainer_count,
                SUM(grand_total) as total_amount
            FROM daftar_tagihan_kontainer_sewa
            WHERE supplier = 'ZONA'
            GROUP BY no_invoice_vendor, no_bank
            HAVING kontainer_count > 1
            ORDER BY kontainer_count DESC
            LIMIT 20
        ");

        echo "\nTop 20 Group Efisien (Multiple Kontainer):\n";
        $totalEfficient = 0;
        $totalKontainerEfficient = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- Invoice: {$row['no_invoice_vendor']}, Bank: {$row['no_bank']}: {$row['kontainer_count']} kontainer, Rp " . number_format($row['total_amount'], 2, ',', '.') . "\n";
            $totalEfficient++;
            $totalKontainerEfficient += $row['kontainer_count'];
        }

        echo "\nPotensi Penghematan dengan Grouping:\n";
        echo "- {$totalEfficient} group efisien dengan {$totalKontainerEfficient} kontainer\n";
        echo "- Penghematan: " . ($totalKontainerEfficient - $totalEfficient) . " pranota\n";

    } catch (PDOException $e) {
        echo "Error analisis: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SIAP UNTUK TESTING ===\n";
echo "Data telah diimport ke database.\n";
echo "Silakan test fitur grouping dengan mengakses halaman daftar tagihan kontainer sewa.\n";
echo "Pilih beberapa tagihan dan gunakan fitur 'Buat Pranota Berdasarkan Invoice & Bank'.\n\n";

echo "Query untuk testing manual:\n";
echo "SELECT no_invoice_vendor, no_bank, COUNT(*) as count FROM daftar_tagihan_kontainer_sewa WHERE supplier = 'ZONA' GROUP BY no_invoice_vendor, no_bank HAVING count > 1;\n";

echo "\n=== SELESAI IMPORT ===\n";
