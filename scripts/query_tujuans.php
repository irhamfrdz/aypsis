<?php
$db = new PDO('sqlite:database/database.sqlite');
$stmt = $db->query("SELECT id, cabang, wilayah, rute, uang_jalan_20, uang_jalan_40, antar_20, antar_40 FROM tujuans ORDER BY id");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
