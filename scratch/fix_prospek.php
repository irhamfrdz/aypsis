<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;

echo "Searching for mismatched Prospek records...\n";

$mismatched = [];
$prospeks = Prospek::whereNotNull('tanda_terima_id')->whereNotNull('surat_jalan_id')->get();

foreach ($prospeks as $p) {
    if ($p->tandaTerima && $p->tandaTerima->surat_jalan_id !== $p->surat_jalan_id) {
        $mismatched[] = $p;
        echo "Mismatch Found:\n";
        echo " - Prospek ID: {$p->id}\n";
        echo " - Prospek No SJ: {$p->no_surat_jalan} (SJ ID: {$p->surat_jalan_id})\n";
        echo " - Prospek Destination: {$p->tujuan_pengiriman}\n";
        echo " - Linked Tanda Terima ID: {$p->tanda_terima_id} (SJ ID on TT: {$p->tandaTerima->surat_jalan_id}, TT No SJ: {$p->tandaTerima->no_surat_jalan})\n";
        echo "----------------------------------------\n";
    }
}

echo 'Total mismatched records: '.count($mismatched)."\n";

// Fix Prospek ID 5704
$p5704 = Prospek::find(5704);
if ($p5704) {
    echo "Fixing Prospek ID 5704...\n";
    $p5704->tanda_terima_id = 3872;
    $p5704->no_seal = '0020840';
    $p5704->tujuan_pengiriman = 'TANJUNG PINANG';
    $p5704->save();
    echo "Prospek ID 5704 has been successfully corrected!\n";
} else {
    echo "Prospek ID 5704 not found.\n";
}
