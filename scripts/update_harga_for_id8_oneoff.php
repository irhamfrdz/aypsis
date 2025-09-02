<?php
$pdo=new PDO('mysql:host=127.0.0.1;dbname=aypsis;charset=utf8mb4','root','');
$id=8;
$k = $pdo->query("SELECT * FROM kontainers WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
$pid = $pdo->prepare('SELECT permohonan_id FROM permohonan_kontainers WHERE kontainer_id = ? LIMIT 1'); $pid->execute([$id]); $permId = $pid->fetchColumn();
$perm = null; if ($permId) { $stmt=$pdo->prepare('SELECT * FROM permohonans WHERE id=?'); $stmt->execute([$permId]); $perm=$stmt->fetch(PDO::FETCH_ASSOC); }
$days = null;
if (!empty($k['tanggal_masuk_sewa']) && !empty($k['tanggal_selesai_sewa'])){
    $start = new DateTime($k['tanggal_masuk_sewa']); $end = new DateTime($k['tanggal_selesai_sewa']); $days = $start->diff($end)->days + 1;
} else if (empty($k['tanggal_masuk_sewa']) && !empty($perm['tanggal_memo'])){
    $start = new DateTime($perm['tanggal_memo']); $end = !empty($k['tanggal_selesai_sewa']) ? new DateTime($k['tanggal_selesai_sewa']) : new DateTime(); $days = $start->diff($end)->days + 1;
}
$uk = $k['ukuran']; $vendor = $perm['vendor_perusahaan'] ?? null;
$m = $pdo->prepare("SELECT * FROM master_pricelist_sewa_kontainers WHERE vendor = :v AND ukuran_kontainer = :u AND tarif='Harian' ORDER BY tanggal_harga_awal DESC LIMIT 1"); $m->execute([':v'=>$vendor,':u'=>$uk]); $md = $m->fetch(PDO::FETCH_ASSOC);
if ($days !== null && $days < 30) {
    if ($md) {
        $unit = $md['harga'] * $days;
        echo "Found master harian: {$md['harga']}, days={$days}, unit={$unit}\n";
    } else {
        // try monthly master
        $mm = $pdo->prepare("SELECT * FROM master_pricelist_sewa_kontainers WHERE vendor = :v AND ukuran_kontainer = :u AND tarif='Bulanan' ORDER BY tanggal_harga_awal DESC LIMIT 1"); $mm->execute([':v'=>$vendor,':u'=>$uk]); $m2=$mm->fetch(PDO::FETCH_ASSOC);
        if ($m2) {
            $unit = ($m2['harga'] / 30) * $days;
            echo "Derived from monthly {$m2['harga']} -> daily " . ($m2['harga']/30) . ", days={$days}, unit={$unit}\n";
        } else { echo "No monthly/pricelist found to derive daily\n"; exit; }
    }
    $upd = $pdo->prepare('UPDATE kontainers SET harga_satuan = ? WHERE id = ?'); $upd->execute([round($unit,2), $id]);
    echo "Updated kontainer id=$id harga_satuan to " . round($unit,2) . "\n";
} else { echo "No update: days={$days}\n"; }
