<?php

/**
 * Script untuk mengekstrak dan mengimpor data stock kontainer dari file SQL backup
 * Run dengan: php extract_and_migrate_stock_kontainers.php
 */

require_once 'vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "🚀 Stock Kontainer Migration Tool\n";
echo "=================================\n\n";

// Check if source SQL file exists
$sourceFile = 'aypsis1.sql';
if (!file_exists($sourceFile)) {
    die("❌ File sumber '$sourceFile' tidak ditemukan!\n");
}

echo "✅ File sumber ditemukan: $sourceFile\n";

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Berhasil terhubung ke database: " . $_ENV['DB_DATABASE'] . "\n";
} catch (PDOException $e) {
    die("❌ Gagal koneksi database: " . $e->getMessage() . "\n");
}

// Check if stock_kontainers table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'stock_kontainers'");
    if ($stmt->rowCount() == 0) {
        die("❌ Tabel stock_kontainers tidak ditemukan. Jalankan migration terlebih dahulu.\n");
    }
    echo "✅ Tabel stock_kontainers ditemukan\n";
} catch (PDOException $e) {
    die("❌ Error checking table: " . $e->getMessage() . "\n");
}

// Check current data count
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM stock_kontainers");
    $currentCount = $stmt->fetch()['count'];
    echo "📊 Data stock kontainer saat ini: {$currentCount} records\n\n";
    
    if ($currentCount > 0) {
        echo "⚠️  Data sudah ada di tabel stock_kontainers.\n";
        echo "Pilihan:\n";
        echo "1. Skip import (default)\n";
        echo "2. Hapus data lama dan import ulang\n";
        echo "3. Tambahkan data baru (mungkin ada duplikasi)\n";
        echo "Pilih (1/2/3): ";
        
        $handle = fopen("php://stdin", "r");
        $choice = trim(fgets($handle));
        fclose($handle);
        
        switch ($choice) {
            case '2':
                echo "🗑️  Menghapus data lama...\n";
                $pdo->exec("DELETE FROM stock_kontainers");
                $pdo->exec("ALTER TABLE stock_kontainers AUTO_INCREMENT = 1");
                echo "✅ Data lama berhasil dihapus\n";
                break;
            case '3':
                echo "➕ Akan menambahkan data baru...\n";
                break;
            default:
                echo "❌ Import dibatalkan\n";
                exit;
        }
    }
} catch (PDOException $e) {
    die("❌ Error checking current data: " . $e->getMessage() . "\n");
}

// Extract stock_kontainers data from SQL file
echo "🔍 Mencari data stock_kontainers dalam file SQL...\n";

$handle = fopen($sourceFile, 'r');
if (!$handle) {
    die("❌ Tidak bisa membuka file SQL\n");
}

$insertStatement = '';
$found = false;
$lineNumber = 0;

while (($line = fgets($handle)) !== false) {
    $lineNumber++;
    
    if (strpos($line, "INSERT INTO `stock_kontainers` VALUES") !== false) {
        $insertStatement = trim($line);
        $found = true;
        echo "✅ Data ditemukan pada baris $lineNumber\n";
        break;
    }
}

fclose($handle);

if (!$found) {
    die("❌ Data stock_kontainers tidak ditemukan dalam file SQL\n");
}

// Parse the INSERT statement
echo "🔄 Parsing data SQL...\n";

// Remove the INSERT part and get only the VALUES part
$valuesStartPos = strpos($insertStatement, "VALUES ") + 7;
$valuesString = substr($insertStatement, $valuesStartPos);

// Remove the trailing semicolon
$valuesString = rtrim($valuesString, ';');

// Parse the values - this is complex because of nested parentheses and quotes
// For now, let's use a simpler approach and create a prepared statement

echo "📝 Menjalankan import langsung dari SQL...\n";

// Begin transaction
$pdo->beginTransaction();

try {
    // Execute the INSERT statement directly
    // First, let's modify it to handle conflicts
    $modifiedInsert = str_replace(
        "INSERT INTO `stock_kontainers` VALUES",
        "INSERT IGNORE INTO `stock_kontainers` VALUES",
        $insertStatement
    );
    
    $result = $pdo->exec($modifiedInsert);
    
    if ($result !== false) {
        echo "✅ Berhasil mengimport $result records\n";
    } else {
        throw new Exception("Gagal mengeksekusi INSERT statement");
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Get final count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM stock_kontainers");
    $finalCount = $stmt->fetch()['count'];
    
    echo "\n🎉 Import berhasil!\n";
    echo "📊 Statistik:\n";
    echo "   - Data sebelumnya: {$currentCount} records\n";
    echo "   - Data berhasil diimport: {$result} records\n";
    echo "   - Total data sekarang: {$finalCount} records\n";
    
    // Show sample data
    echo "\n📋 Sample data yang diimport:\n";
    $stmt = $pdo->query("SELECT nomor_seri_gabungan, ukuran, tipe_kontainer, status FROM stock_kontainers ORDER BY id LIMIT 5");
    $samples = $stmt->fetchAll();
    
    foreach ($samples as $sample) {
        echo "   - {$sample['nomor_seri_gabungan']} ({$sample['ukuran']}ft {$sample['tipe_kontainer']}) - {$sample['status']}\n";
    }
    
} catch (Exception $e) {
    $pdo->rollBack();
    die("❌ Error during import: " . $e->getMessage() . "\n");
}

echo "\n✨ Migration stock kontainer selesai!\n";
echo "🔍 Untuk memeriksa data:\n";
echo "   - php artisan tinker\n";
echo "   - App\\Models\\StockKontainer::count()\n";
echo "   - App\\Models\\StockKontainer::where('status', 'available')->count()\n";

?>