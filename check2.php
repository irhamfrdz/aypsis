<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prospeks = App\Models\Prospek::where("status", "sudah_muat")
    ->whereHas("naikKapal", function($q) {
        $q->where("sudah_ob", false)
          ->orWhereNull("sudah_ob");
    })
    ->with("manifests", "bls", "naikKapal")
    ->get();

$result = [];
foreach ($prospeks as $p) {
    $hasManifest = count($p->manifests) > 0;
    $hasBl = count($p->bls) > 0;
    $isSudahOb = false;
    foreach ($p->naikKapal as $nk) {
        if ($nk->sudah_ob) $isSudahOb = true;
    }
    
    if (!$hasManifest && !$hasBl && !$isSudahOb) {
        $result[] = [
            "id" => $p->id,
            "no_surat_jalan" => $p->no_surat_jalan,
            "naik_kapal" => $p->naikKapal->map(function($nk) {
                return [
                    "id" => $nk->id,
                    "no_voyage" => $nk->no_voyage,
                    "nomor_kontainer" => $nk->nomor_kontainer
                ];
            })
        ];
    }
}

echo json_encode($result, JSON_PRETTY_PRINT);
