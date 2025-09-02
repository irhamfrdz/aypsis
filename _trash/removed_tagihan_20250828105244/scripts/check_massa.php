<?php
require __DIR__ . '/../vendor/autoload.php';
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$pdo = new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
$rows = $pdo->query("select id, tanggal_harga_awal, tanggal_harga_akhir from tagihan_kontainer_sewa order by id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    $masaStart = '-';
    $masaEnd = '-';
    try {
        if (!empty($r['tanggal_harga_awal'])) {
            $start = Carbon::parse($r['tanggal_harga_awal']);
            $masaStart = $start->locale('id')->isoFormat('D MMMM');
        }
        if (!empty($r['tanggal_harga_akhir'])) {
            $end = Carbon::parse($r['tanggal_harga_akhir'])->subDay();
            $masaEnd = $end->locale('id')->isoFormat('D MMMM');
        }
        if ($masaStart === '-' && $masaEnd === '-') $massa = '-';
        elseif ($masaEnd === '-') $massa = $masaStart;
        else $massa = $masaStart . ' - ' . $masaEnd;
    } catch (Exception $e) {
        $massa = 'ERR';
    }
    echo sprintf("%s | %s\n", $r['id'], $massa);
}
