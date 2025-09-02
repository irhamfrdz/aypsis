<?php
$db=new PDO('sqlite:C:\\folder_kerjaan\\aypsis\\database\\database.sqlite');
$rows=[];
foreach($db->query('SELECT id, nomor_seri_gabungan, nomor_seri_kontainer, ukuran, tanggal_masuk_sewa, tanggal_selesai_sewa, harga_satuan FROM kontainers WHERE harga_satuan IS NOT NULL LIMIT 10') as $r){
    $rows[]=$r;
}
echo json_encode($rows, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
