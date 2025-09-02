<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
foreach ($db->query('SELECT tagihan_id, COUNT(*) as c FROM tagihan_kontainer_sewa_kontainers GROUP BY tagihan_id ORDER BY c DESC LIMIT 20') as $r) {
    echo json_encode($r) . PHP_EOL;
}
?>
