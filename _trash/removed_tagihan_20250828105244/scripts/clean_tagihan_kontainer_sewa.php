<?php
// Clean tagihan_kontainer_sewa data. Use with caution.
// Usage: php scripts/clean_tagihan_kontainer_sewa.php --mode=soft|hard --yes
// soft: soft-delete all rows (sets deleted_at). hard: permanently delete rows and pivots.

$opts = [];
foreach ($argv as $arg) {
    if (preg_match('/^--([a-zA-Z0-9_\-]+)=(.*)$/', $arg, $m)) {
        $opts[$m[1]] = $m[2];
    } elseif ($arg === '--yes') {
        $opts['yes'] = true;
    }
}
$mode = $opts['mode'] ?? 'soft';
$confirm = isset($opts['yes']);
if (!$confirm) {
    echo "Refusing to run without --yes. Use --mode=soft|hard --yes\n";
    exit(1);
}

$env = @parse_ini_file(__DIR__ . '/../.env');
$dbConnection = $env['DB_CONNECTION'] ?? 'mysql';
if ($dbConnection === 'sqlite') {
    $dbPath = __DIR__ . '/../' . ($env['DB_DATABASE'] ?? 'database/database.sqlite');
    $pdo = new PDO('sqlite:' . $dbPath);
} else {
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_DATABASE'] ?? 'aypsis';
    $user = $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASSWORD'] ?? '';
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

if ($mode === 'soft') {
    // set deleted_at to now() for all tagihan rows
    $now = date('Y-m-d H:i:s');
    $pdo->exec("UPDATE tagihan_kontainer_sewa SET deleted_at = '$now' WHERE deleted_at IS NULL");
    echo "Soft-deleted tagihan_kontainer_sewa (updated deleted_at).\n";
} elseif ($mode === 'hard') {
    // delete pivot rows then delete tagihan rows
    $pdo->exec("DELETE FROM tagihan_kontainer_sewa_kontainers");
    $pdo->exec("DELETE FROM tagihan_kontainer_sewa");
    echo "Hard-deleted all tagihan_kontainer_sewa and pivot rows.\n";
} else {
    echo "Unknown mode: $mode\n";
}
