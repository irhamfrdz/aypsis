<?php
$serial = $argv[1] ?? '1111111111';
$db=new PDO('sqlite:C:\\folder_kerjaan\\aypsis\\database\\database.sqlite');
// find container by nomor_seri_gabungan or nomor_seri_kontainer
$stmt = $db->prepare("SELECT * FROM kontainers WHERE nomor_seri_gabungan = :s OR nomor_seri_kontainer = :s LIMIT 1");
$stmt->execute([':s'=>$serial]);
$k = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$k) {
    echo "Container not found for serial={$serial}\n";
    exit(0);
}
echo "-- Container row --\n";
foreach ($k as $col=>$val) echo "$col: $val\n";
$ukuran = $k['ukuran'] ?? null;
// find potential vendors from tagihan_kontainer_sewa that match groups - try to find any master pricelist entries for this ukuran
$stmt2 = $db->prepare("SELECT * FROM master_pricelist_sewa_kontainers WHERE ukuran_kontainer = :u ORDER BY tanggal_harga_awal DESC LIMIT 5");
$stmt2->execute([':u'=>$ukuran]);
$masters = $stmt2->fetchAll(PDO::FETCH_ASSOC);
if (empty($masters)) {
    echo "\nNo master pricelist entries found for ukuran={$ukuran}\n";
} else {
    echo "\n-- Master pricelist sample for ukuran={$ukuran} --\n";
    foreach ($masters as $m) {
        echo "id={$m['id']} vendor={$m['vendor']} tarif={$m['tarif']} harga={$m['harga']} tanggal_awal={$m['tanggal_harga_awal']} tanggal_akhir={$m['tanggal_harga_akhir']}\n";
    }
}
"";
