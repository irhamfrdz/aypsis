<?php
$host='127.0.0.1'; $db='aypsis'; $user='root'; $pass='';
try{
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e){
    echo "DB_CONNECT_ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
// find returned containers in August 2025 or with status 'dikembalikan'
$sql = "SELECT id, nomor_seri_gabungan, nomor_seri_kontainer, ukuran, tanggal_masuk_sewa, tanggal_selesai_sewa, status, harga_satuan FROM kontainers WHERE status = 'dikembalikan' OR (tanggal_selesai_sewa BETWEEN '2025-08-01' AND '2025-08-31') ORDER BY tanggal_selesai_sewa DESC LIMIT 50";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo "NO_RETURNED_ROWS\n";
    exit(0);
}
$out = [];
foreach ($rows as $r) {
    $uk = $r['ukuran'];
    $masters = $pdo->prepare("SELECT id,vendor,tarif,harga,tanggal_harga_awal,tanggal_harga_akhir FROM master_pricelist_sewa_kontainers WHERE ukuran_kontainer = :u ORDER BY tanggal_harga_awal DESC LIMIT 10");
    $masters->execute([':u'=>$uk]);
    $mrows = $masters->fetchAll(PDO::FETCH_ASSOC);
    $r['masters'] = $mrows;
    $out[] = $r;
}
echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
