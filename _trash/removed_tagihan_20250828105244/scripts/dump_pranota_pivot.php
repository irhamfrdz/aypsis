<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$pranotas = $db->query("SELECT id, group_code, nomor_kontainer, tarif, created_at FROM tagihan_kontainer_sewa WHERE tarif = 'Pranota' ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
echo "PRANOTAS:\n";
foreach ($pranotas as $p) echo json_encode($p, JSON_UNESCAPED_UNICODE) . PHP_EOL;
$last = $pranotas ? $pranotas[0]['id'] : null;
echo "\nPIVOTS for last pranota id: " . ($last ?: 'NONE') . "\n";
if ($last) {
    $pivs = $db->prepare("SELECT * FROM tagihan_kontainer_sewa_kontainers WHERE tagihan_id = :id");
    $pivs->execute([':id' => $last]);
    $rows = $pivs->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo json_encode($r, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
?>
