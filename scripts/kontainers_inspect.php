<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
echo "COLUMNS kontainers:\n";
foreach ($db->query("PRAGMA table_info('kontainers')") as $c) echo $c['name'] . PHP_EOL;

$frags = ['123321111','1231242122'];
foreach ($frags as $f) {
    $sql = "SELECT * FROM kontainers WHERE nomor_seri_gabungan LIKE :f OR nomor_seri_kontainer LIKE :f OR awalan_kontainer LIKE :f OR akhiran_kontainer LIKE :f LIMIT 5";
    $stmt = $db->prepare($sql);
    $stmt->execute([':f' => "%$f%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nSEARCH $f => " . count($rows) . " results\n";
    foreach ($rows as $r) echo json_encode($r, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
?>
