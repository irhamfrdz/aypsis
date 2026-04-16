<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaLcl;

echo "--- TandaTerima ---" . PHP_EOL;
$tts = TandaTerima::where('no_kontainer', 'LIKE', '%AYPU2100277%')->get();
foreach($tts as $t) echo "ID: {$t->id} | No: {$t->no_kontainer} | Images: " . json_encode($t->gambar_checkpoint) . PHP_EOL;

echo "--- TandaTerimaTanpaSuratJalan ---" . PHP_EOL;
$tttsjs = TandaTerimaTanpaSuratJalan::where('no_kontainer', 'LIKE', '%AYPU2100277%')->get();
foreach($tttsjs as $t) echo "ID: {$t->id} | No: {$t->no_kontainer} | Images: " . json_encode($t->gambar_tanda_terima) . PHP_EOL;

echo "--- TandaTerimaLcl ---" . PHP_EOL;
// TandaTerimaLcl has nomor_kontainer via pivot or accessor
$lcls = TandaTerimaLcl::all()->filter(function($item) {
    return str_contains($item->nomor_kontainer, 'AYPU2100277');
});
foreach($lcls as $t) echo "ID: {$t->id} | No: {$t->nomor_kontainer} | Images: " . json_encode($t->gambar_surat_jalan) . PHP_EOL;
