<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prospeks = App\Models\Prospek::where('status', 'sudah_muat')
    ->whereHas('naikKapal', function ($q) {
        $q->where('sudah_ob', false)
            ->orWhereNull('sudah_ob');
    })
    ->with('manifests', 'bls', 'naikKapal')
    ->get();

$count = 0;
foreach ($prospeks as $p) {
    $hasManifest = count($p->manifests) > 0;
    $hasBl = count($p->bls) > 0;
    $isSudahOb = false;
    foreach ($p->naikKapal as $nk) {
        if ($nk->sudah_ob) {
            $isSudahOb = true;
        }
    }

    if (! $hasManifest && ! $hasBl && ! $isSudahOb) {
        $p->update(['status' => 'aktif']);
        $count++;
    }
}

echo 'Restored '.$count.' prospeks to aktif status.';
