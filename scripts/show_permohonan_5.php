<?php
$pdo=new PDO('mysql:host=127.0.0.1;dbname=aypsis;charset=utf8mb4','root','');
$stmt=$pdo->prepare('SELECT id,vendor_perusahaan,tanggal_memo,total_harga_setelah_adj,jumlah_uang_jalan FROM permohonans WHERE id=?');
$stmt->execute([5]);
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
