<?php
$env = parse_ini_file(__DIR__ . '/../.env');
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
$stmt = $pdo->query("select id, vendor, tarif, tanggal_harga_awal from tagihan_kontainer_sewa");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo sprintf("%s | %s | %s | %s\n", $r['id'], $r['vendor'] ?? 'NULL', $r['tarif'] ?? 'NULL', $r['tanggal_harga_awal'] ?? 'NULL');
}
