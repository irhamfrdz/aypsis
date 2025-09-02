<?php
$pdo=new PDO('mysql:host=127.0.0.1;dbname=aypsis;charset=utf8mb4','root','');
$stmt=$pdo->query("SELECT id,nomor_seri_gabungan,ukuran,tanggal_masuk_sewa,tanggal_selesai_sewa,status,harga_satuan FROM kontainers WHERE id=8");
$r=$stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($r, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
