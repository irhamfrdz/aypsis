<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
echo "TABLES:\n";
foreach ($db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name") as $t) {
    echo $t['name'] . PHP_EOL;
}

echo "\nCOLUMNS tagihan_kontainer_sewa:\n";
foreach ($db->query("PRAGMA table_info('tagihan_kontainer_sewa')") as $c) echo $c['name'] . PHP_EOL;

echo "\nCOLUMNS tagihan_kontainer_sewa_kontainers (if exists):\n";
foreach ($db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tagihan_kontainer_sewa_kontainers'") as $r) {
    foreach ($db->query("PRAGMA table_info('tagihan_kontainer_sewa_kontainers')") as $col) echo $col['name'] . PHP_EOL;
}

// show last 5 pranota rows
echo "\nLAST PRANOTAS:\n";
foreach ($db->query("SELECT id, tarif, nomor_kontainer, created_at FROM tagihan_kontainer_sewa ORDER BY id DESC LIMIT 5") as $p) {
    echo json_encode($p) . PHP_EOL;
}

// if pivot exists, show counts for latest pranota id
$pr = $db->query("SELECT id FROM tagihan_kontainer_sewa WHERE tarif = 'Pranota' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($pr) {
    $pid = $pr['id'];
    echo "\nLatest pranota id: $pid\n";
    $cnt = $db->query("SELECT COUNT(*) as c FROM tagihan_kontainer_sewa_kontainers WHERE tagihan_id = " . intval($pid))->fetch(PDO::FETCH_ASSOC);
    echo "Pivot count: " . ($cnt['c'] ?? '0') . PHP_EOL;
    foreach ($db->query("SELECT * FROM tagihan_kontainer_sewa_kontainers WHERE tagihan_id = " . intval($pid)) as $row) echo json_encode($row) . PHP_EOL;
} else {
    echo "\nNo pranota rows found\n";
}
?>
