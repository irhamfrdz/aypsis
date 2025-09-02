<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
foreach ($db->query("PRAGMA table_info('tagihan_kontainer_sewa')") as $col) {
    echo $col['name'] . PHP_EOL;
}
?>
