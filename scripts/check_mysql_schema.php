<?php
// scripts/check_mysql_schema.php
// Read DB config from .env (simple parse), connect to MySQL and inspect table columns and sample rows.
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    echo ".env not found at {$envPath}\n"; exit(2);
}
$env = parse_ini_file($envPath, false, INI_SCANNER_RAW);
// parse_ini_file won't handle quoted values well; do a simple parser
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (!strpos($line, '=')) continue;
    list($k,$v) = explode('=', $line, 2);
    $k = trim($k);
    $v = trim($v);
    // strip surrounding quotes
    if (strlen($v) >=2 && (($v[0]==='"' && substr($v,-1)==='"') || ($v[0]==="'" && substr($v,-1)==="'"))) {
        $v = substr($v,1,-1);
    }
    $env[$k] = $v;
}
$driver = $env['DB_CONNECTION'] ?? 'mysql';
if ($driver !== 'mysql') {
    echo "Configured DB_CONNECTION={$driver} — this script connects only to MySQL when configured.\n";
    exit(1);
}
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db = $env['DB_DATABASE'] ?? null;
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';
if (!$db) { echo "DB_DATABASE not set in .env\n"; exit(2); }
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Failed to connect to MySQL: " . $e->getMessage() . "\n"; exit(3);
}
$tables = ['daftar_tagihan_kontainer_sewa', 'tagihan_kontainer_sewa'];
foreach ($tables as $table) {
    echo "\n=== Table: {$table} ===\n";
    try {
        $stmt = $pdo->prepare("SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table ORDER BY ORDINAL_POSITION");
        $stmt->execute([':db'=>$db, ':table'=>$table]);
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($cols)) {
            echo "Table not found or has no columns.\n"; continue;
        }
        foreach ($cols as $c) {
            echo sprintf("%s | data_type=%s | column_type=%s | nullable=%s\n", $c['COLUMN_NAME'], $c['DATA_TYPE'], $c['COLUMN_TYPE'], $c['IS_NULLABLE']);
        }
        // sample rows
        $s = $pdo->query("SELECT * FROM `{$table}` LIMIT 5");
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        echo "\nSample rows (up to 5):\n";
        if (empty($rows)) { echo "(no rows)\n"; } else {
            foreach ($rows as $r) {
                if (array_key_exists('masa', $r)) {
                    $v = $r['masa'];
                    echo "masa raw: [" . $v . "] php_type=" . gettype($v) . "\n";
                }
                echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
        $textLike = false;
        foreach ($cols as $c) {
            $t = strtolower($c['DATA_TYPE']);
            if (in_array($t, ['varchar','text','char','blob','tinytext','mediumtext','longtext','enum','set'])) { $textLike = true; break; }
        }
        echo "\nText-like column declared: " . ($textLike ? 'YES' : 'NO') . "\n";
    } catch (Exception $e) {
        echo "Error inspecting table {$table}: " . $e->getMessage() . "\n";
    }
}
echo "\nDone.\n";
