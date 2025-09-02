<?php
// Backup tagihan_kontainer_sewa and pivot table to backups/ with timestamped CSV files
// Does not perform deletion. Run separately if you want to delete after verifying backups.

// Tagihan feature has been removed. This script is inert to avoid operating on deleted tables.
echo "tagihan scripts disabled: tagihan_kontainer_sewa feature removed.\n";
exit(0);

// Dead code below - keeping for reference but unreachable due to exit(0) above
function setupDatabase() {
    // Load environment variables
    $envFile = __DIR__ . '/../.env';
    $env = [];
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
    }

    $host = $env['DB_HOST'] ?? 'localhost';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_DATABASE'] ?? 'aypsis';
    $user = $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASSWORD'] ?? '';
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

    return new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

function backupTables() {
    $pdo = setupDatabase();

    // Create backup directory with timestamp
    $timestamp = date('Ymd_His');
    $backupDir = __DIR__ . '/../backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $tagihanFile = "{$backupDir}/tagihan_backup_{$timestamp}.csv";
    $pivotFile = "{$backupDir}/tagihan_kontainer_sewa_kontainers_backup_{$timestamp}.csv";

    echo "Backing up tables to: {$backupDir}\n";

    // Backup tagihan_kontainer_sewa
    $stmt = $pdo->query("SELECT * FROM tagihan_kontainer_sewa ORDER BY id");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $fp = fopen($tagihanFile, 'w');
    if ($fp === false) {
        echo "Failed to open $tagihanFile for writing\n";
        exit(1);
    }

    if (count($rows) > 0) {
        // write header
        fputcsv($fp, array_keys($rows[0]), ';');
        foreach ($rows as $r) {
            // Ensure string values
            $row = [];
            foreach ($r as $v) $row[] = $v;
            fputcsv($fp, $row, ';');
        }
    }
    fclose($fp);

    // Backup pivot
    $stmt2 = $pdo->query("SELECT * FROM tagihan_kontainer_sewa_kontainers ORDER BY tagihan_id, kontainer_id");
    $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $fp2 = fopen($pivotFile, 'w');
    if ($fp2 === false) {
        echo "Failed to open $pivotFile for writing\n";
        exit(1);
    }

    if (count($rows2) > 0) {
        fputcsv($fp2, array_keys($rows2[0]), ';');
        foreach ($rows2 as $r) {
            $row = [];
            foreach ($r as $v) $row[] = $v;
            fputcsv($fp2, $row, ';');
        }
    }
    fclose($fp2);

    echo "Counts: tagihan_kontainer_sewa=" . count($rows) . ", pivots=" . count($rows2) . "\n";
    echo "Backups written:\n - $tagihanFile\n - $pivotFile\n";
    echo "Next: to delete data, confirm and run scripts/clean_tagihan_kontainer_sewa.php --hard (for permanent delete) or --soft (for soft-delete).\n";
}

// Note: The following code would normally call backupTables() but is disabled
// backupTables();
