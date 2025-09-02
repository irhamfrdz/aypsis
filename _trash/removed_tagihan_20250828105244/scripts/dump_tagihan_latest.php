<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
foreach ($db->query("SELECT id, tarif, nomor_kontainer, dpp, ppn, pph, grand_total, nomor_pranota FROM tagihan_kontainer_sewa ORDER BY id DESC LIMIT 5") as $r) echo json_encode($r) . PHP_EOL;
?>
