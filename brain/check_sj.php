<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;

$no = 'SS0002744';

echo "SEARCH BY LIKE\n";
$sj = SuratJalan::where('no_surat_jalan', 'like', "%$no%")->get();
foreach($sj as $s) {
    echo "SJ: [" . $s->no_surat_jalan . "] ID: " . $s->id . " SUPIR: " . $s->supir . " KENEK: " . $s->kenek . "\n";
}

$sjb = SuratJalanBongkaran::where('nomor_surat_jalan', 'like', "%$no%")->get();
foreach($sjb as $s) {
    echo "SJB: [" . $s->nomor_surat_jalan . "] ID: " . $s->id . " SUPIR: " . $s->supir . " KENEK: " . $s->kenek . "\n";
}
