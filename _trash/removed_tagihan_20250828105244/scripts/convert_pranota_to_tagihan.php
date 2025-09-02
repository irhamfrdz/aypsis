<?php
// Robust converter: read .env manually, support sqlite or mysql, update pranota rows per-row
function parseDotEnv($path)
{
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) return [];
    $out = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        // strip surrounding quotes
        if ((strlen($v) >= 2) && (($v[0] === '"' && substr($v, -1) === '"') || ($v[0] === "'" && substr($v, -1) === "'"))) {
            $v = substr($v, 1, -1);
        }
        $out[$k] = $v;
    }
    return $out;
}

$env = parseDotEnv(__DIR__ . '/../.env');
$dbConn = $env['DB_CONNECTION'] ?? 'mysql';
if ($dbConn === 'sqlite') {
    $dbPath = __DIR__ . '/../' . ($env['DB_DATABASE'] ?? 'database/database.sqlite');
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} else {
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_DATABASE'] ?? '';
    $user = $env['DB_USERNAME'] ?? '';
    $pass = $env['DB_PASSWORD'] ?? '';
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

$rows = $pdo->query("select id, tarif, group_code from tagihan_kontainer_sewa where tarif = 'Pranota'")->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) { echo "No pranota rows found.\n"; exit(0); }

// Some DB schemas require 'tarif' not null; set to empty string which will be treated as non-'Pranota'
$upd = $pdo->prepare("update tagihan_kontainer_sewa set tarif = '', group_code = ? where id = ?");
foreach ($rows as $r) {
    $id = (int)$r['id'];
    // nomor_pranota removed; keep null
    $nomor = null;
    $group = $r['group_code'] ?? null;
    if (empty($group)) {
        $group = 'A' . str_pad((string)$id, 3, '0', STR_PAD_LEFT);
    }
    echo "Updating id={$id} (nomor_pranota=" . ($nomor ?? 'NULL') . ") -> group_code={$group}\n";
    $upd->execute([$group, $id]);
}
echo "Done.\n";
