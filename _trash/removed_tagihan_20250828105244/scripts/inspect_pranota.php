<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$nomor = 'PTK1250800001';
$sql = "SELECT id, nomor_kontainer, dpp, ppn, pph, grand_total, nomor_pranota, is_pranota, tarif FROM tagihan_kontainer_sewa WHERE nomor_pranota = :nomor OR nomor_kontainer LIKE :frag1 OR nomor_kontainer LIKE :frag2";
$stmt = $db->prepare($sql);
$stmt->execute([':nomor' => $nomor, ':frag1' => '%123321111%', ':frag2' => '%1231242122%']);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo json_encode($r, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
if (empty($rows)) echo "NO_ROWS\n";
?>
