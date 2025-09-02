<?php
$pdo=new PDO('mysql:host=127.0.0.1;dbname=aypsis;charset=utf8mb4','root','');
$id=8;
$k = $pdo->query("SELECT * FROM kontainers WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
if (!$k) { echo "not found\n"; exit; }
// find permohonan
$stmt = $pdo->prepare('SELECT permohonan_id FROM permohonan_kontainers WHERE kontainer_id = ? LIMIT 1'); $stmt->execute([$id]); $pid = $stmt->fetchColumn();
$perm = $pid ? $pdo->prepare('SELECT * FROM permohonans WHERE id = ?') : null;
if ($perm) { $perm->execute([$pid]); $perm = $perm->fetch(PDO::FETCH_ASSOC); }
$uk = $k['ukuran'];
// find daily master for same vendor as perm
$vendor = $perm['vendor_perusahaan'] ?? null;
$masters = $pdo->prepare("SELECT * FROM master_pricelist_sewa_kontainers WHERE vendor = :v AND ukuran_kontainer = :u AND tarif='Harian' ORDER BY tanggal_harga_awal DESC LIMIT 1");
$masters->execute([':v'=>$vendor, ':u'=>$uk]); $m = $masters->fetch(PDO::FETCH_ASSOC);
$days = null;
if (!empty($k['tanggal_masuk_sewa']) && !empty($k['tanggal_selesai_sewa'])){
    $start = new DateTime($k['tanggal_masuk_sewa']); $end = new DateTime($k['tanggal_selesai_sewa']); $diff = $start->diff($end); $days = $diff->days + 1;
} else if (empty($k['tanggal_masuk_sewa']) && !empty($perm['tanggal_memo'])){
    $start = new DateTime($perm['tanggal_memo']); $end = !empty($k['tanggal_selesai_sewa']) ? new DateTime($k['tanggal_selesai_sewa']) : new DateTime(); $diff = $start->diff($end); $days = $diff->days + 1;
}
if ($days !== null && $days < 30 && $m){
    $unit = $m['harga'] * $days;
    echo "Will set harga_satuan = $unit (daily {$m['harga']} * days $days)\n";
    $upd = $pdo->prepare('UPDATE kontainers SET harga_satuan = ? WHERE id = ?'); $upd->execute([round($unit,2), $id]);
    echo "Updated.\n";
} else {
    echo "No update conditions met. days=$days, master=".json_encode($m)."\n";
}
