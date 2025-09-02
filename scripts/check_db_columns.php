<?php
// scripts/check_db_columns.php
// Prints PRAGMA table_info for tables and sample rows; useful to check column types (masa)
$tables = array_slice($argv, 1);
if (empty($tables)) {
    $tables = ['daftar_tagihan_kontainer_sewa', 'tagihan_kontainer_sewa'];
}
$dbFile = realpath(__DIR__ . '/../database/database.sqlite');
if (!$dbFile || !file_exists($dbFile)) {
    echo "Database file not found at database/database.sqlite\n";
    exit(2);
}
try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Failed to open database: " . $e->getMessage() . "\n";
    exit(3);
}
foreach ($tables as $table) {
    echo "\n=== Table: {$table} ===\n";
    try {
        $stmt = $pdo->query("PRAGMA table_info('" . $table . "')");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($cols)) {
            echo "Table not found or has no columns.\n";
            continue;
        }
        foreach ($cols as $c) {
            echo sprintf("%s | type=%s | notnull=%s | dflt=%s | pk=%s\n",
                $c['name'], $c['type'], $c['notnull'], $c['dflt_value'], $c['pk']);
        }
        // Sample rows
        $s = $pdo->query("SELECT * FROM \"{$table}\" LIMIT 5");
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        echo "\nSample rows (up to 5):\n";
        if (empty($rows)) {
            echo "(no rows)\n";
        } else {
            foreach ($rows as $r) {
                // print masa column if present
                if (isset($r['masa'])) {
                    $v = $r['masa'];
                    $len = strlen((string)$v);
                    $type = gettype($v);
                    echo "masa value (raw): [" . (string)$v . "] (len={$len}, php_type={$type})\n";
                }
                echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
        // Determine if any column type suggests text affinity
        $textLike = false;
        foreach ($cols as $c) {
            $t = strtoupper($c['type']);
            if (strpos($t, 'CHAR') !== false || strpos($t, 'TEXT') !== false || strpos($t, 'CLOB') !== false) {
                $textLike = true; break;
            }
        }
        echo "\nText-like column declared: " . ($textLike ? 'YES' : 'NO') . "\n";
        // Note about SQLite affinity
        echo "Note: SQLite uses type affinity and generally accepts text in most columns; numeric affinity columns may still hold text but behavior differs.\n";
    } catch (Exception $e) {
        echo "Error inspecting table {$table}: " . $e->getMessage() . "\n";
    }
}

echo "\nDone.\n";
