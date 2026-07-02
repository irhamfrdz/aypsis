<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Prospek::where('status', 'sudah_muat')
    ->whereHas('naikKapal', function ($q) {
        $q->where('no_voyage', 'AP006JP26');
    })
    ->with('manifests', 'bls', 'naikKapal')
    ->first();

if ($p) {
    echo $p->toJson(JSON_PRETTY_PRINT);
} else {
    echo 'No prospek found';
}
