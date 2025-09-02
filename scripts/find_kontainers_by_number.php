<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$frags = ['123321111','1231242122'];
foreach ($frags as $f) {
    $sql = "SELECT id, awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer, nomor_seri_gabungan, harga_satuan FROM kontainers WHERE nomor_seri_gabungan LIKE :f OR nomor_seri_kontainer LIKE :f OR awalan_kontainer LIKE :f OR akhiran_kontainer LIKE :f LIMIT 10";
    $stmt = $db->prepare($sql);
    $stmt->execute([':f' => "%$f%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "SEARCH $f => " . count($rows) . " results\n";
    foreach ($rows as $r) echo json_encode($r, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
?>
