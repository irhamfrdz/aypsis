<?php

echo "=== IMPORT KARYAWAN FROM SQL DUMP ===\n";

// Database config - sesuaikan dengan database Anda
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'aypsis';
$port = 3306;

// Path file SQL dump
$sqlDumpPath = 'c:\folder_kerjaan\aypsis3.sql';

try {
    // 1. Connect to database
    echo "Connecting to database...\n";
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✓ Connected to database successfully\n";

    // 2. Read SQL dump file
    echo "Reading SQL dump file...\n";
    if (!file_exists($sqlDumpPath)) {
        throw new Exception("File SQL dump tidak ditemukan: $sqlDumpPath");
    }
    
    $sqlContent = file_get_contents($sqlDumpPath);
    echo "✓ SQL dump file loaded\n";

    // 3. Extract karyawans table section
    echo "Extracting karyawans table...\n";
    
    // Find START: DROP TABLE IF EXISTS `karyawans`;
    $startPattern = '/DROP TABLE IF EXISTS `karyawans`;/';
    // Find END: UNLOCK TABLES; (after the karyawans data)
    $endPattern = '/LOCK TABLES `karyawans`.*?UNLOCK TABLES;/s';
    
    if (!preg_match($startPattern, $sqlContent, $startMatch, PREG_OFFSET_CAPTURE)) {
        throw new Exception("Tidak dapat menemukan tabel karyawans di dump file");
    }
    
    $startPos = $startMatch[0][1];
    
    // Find the complete karyawans section
    if (!preg_match($endPattern, $sqlContent, $endMatch, PREG_OFFSET_CAPTURE, $startPos)) {
        throw new Exception("Tidak dapat menemukan bagian akhir data karyawans");
    }
    
    $endPos = $endMatch[0][1] + strlen($endMatch[0][0]);
    
    // Extract the complete karyawans section
    $karyawanSql = substr($sqlContent, $startPos, $endPos - $startPos);
    
    echo "✓ Karyawans section extracted\n";

    // 4. Backup existing karyawans table
    echo "Creating backup of existing karyawans table...\n";
    $backupFile = "karyawans_backup_" . date('YmdHis') . ".sql";
    
    $result = $pdo->query("SHOW TABLES LIKE 'karyawans'");
    if ($result->rowCount() > 0) {
        // Table exists, create backup
        $count = $pdo->query("SELECT COUNT(*) FROM karyawans")->fetchColumn();
        echo "Current karyawans table has $count records\n";
        
        // Simple backup - just export data
        $backupData = "-- Backup karyawans " . date('Y-m-d H:i:s') . "\n";
        $backupData .= "DELETE FROM karyawans;\n";
        
        $stmt = $pdo->query("SELECT * FROM karyawans");
        while ($row = $stmt->fetch()) {
            $values = array_map(function($val) use ($pdo) {
                return $val === null ? 'NULL' : $pdo->quote($val);
            }, array_values($row));
            
            $backupData .= "INSERT INTO karyawans VALUES (" . implode(', ', $values) . ");\n";
        }
        
        file_put_contents($backupFile, $backupData);
        echo "✓ Backup saved to: $backupFile\n";
    } else {
        echo "ℹ No existing karyawans table found\n";
    }

    // 5. Disable foreign key checks temporarily
    echo "Disabling foreign key checks...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // 6. Execute the karyawans SQL
    echo "Importing karyawans data...\n";
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $karyawanSql)));
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (Exception $e) {
            echo "⚠ Error executing statement: " . substr($statement, 0, 100) . "...\n";
            echo "Error: " . $e->getMessage() . "\n";
            // Continue with other statements
        }
    }
    echo "✓ Karyawans data imported\n";

    // 7. Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "✓ Foreign key checks re-enabled\n";

    // 8. Verify import
    echo "Verifying import...\n";
    $count = $pdo->query("SELECT COUNT(*) FROM karyawans")->fetchColumn();
    echo "✓ Total karyawans in database: $count\n";
    
    if ($count > 0) {
        echo "\nSample records:\n";
        $stmt = $pdo->query("SELECT id, nik, nama, email FROM karyawans ORDER BY id DESC LIMIT 5");
        while ($row = $stmt->fetch()) {
            echo "ID: {$row['id']}, NIK: {$row['nik']}, Nama: {$row['nama']}, Email: {$row['email']}\n";
        }
    }

    echo "\n=== IMPORT COMPLETED SUCCESSFULLY ===\n";
    echo "Backup file: $backupFile\n";
    echo "Total records imported: $count\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    
    if (isset($pdo)) {
        try {
            $pdo->rollback();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $rollbackError) {
            // Ignore rollback errors
        }
    }
    
    exit(1);
}