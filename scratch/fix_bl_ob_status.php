<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Bl;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$affected = Bl::where(function($q) {
    $q->where('no_voyage', 'like', '%PJ%')
      ->orWhere('no_voyage', 'like', '%JB%')
      ->orWhere('nomor_bl', 'like', '%PJ%')
      ->orWhere('nomor_bl', 'like', '%JB%');
})
->where('sudah_ob', true)
->get();

echo "Found " . $affected->count() . " records to update.\n";

if ($affected->count() > 0) {
    foreach ($affected as $bl) {
        $bl->sudah_ob = false;
        $bl->supir_id = null;
        $bl->tanggal_ob = null;
        $bl->catatan_ob = null;
        $bl->save();
        echo "Updated BL ID: " . $bl->id . " (Voyage: " . $bl->no_voyage . ")\n";
    }
}

echo "Done.\n";
