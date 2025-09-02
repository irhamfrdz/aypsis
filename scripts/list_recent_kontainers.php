<?php
$db=new PDO('sqlite:C:\\folder_kerjaan\\aypsis\\database\\database.sqlite');
$rows = $db->query('SELECT id, nomor_seri_gabungan, nomor_seri_kontainer, ukuran, tanggal_masuk_sewa, tanggal_selesai_sewa, status, harga_satuan FROM kontainers ORDER BY id DESC LIMIT 50');
foreach($rows as $r){
    echo implode(' | ', array_values($r)) . "\n";
}
