<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Sample NaikKapal (CARGO) ===\n";
$nk = DB::table('naik_kapal')->where('tipe_kontainer', 'CARGO')->first();
if ($nk) {
    print_r($nk);
    echo "\nProspek ID: " . $nk->prospek_id . "\n";
    if ($nk->prospek_id) {
        $prospek = DB::table('prospek')->where('id', $nk->prospek_id)->first();
        print_r($prospek);
        
        if ($prospek->tanda_terima_id) {
             echo "\nLinked Tanda Terima ID: " . $prospek->tanda_terima_id . "\n";
             $tttsj = DB::table('tanda_terima_tanpa_surat_jalan')->where('id', $prospek->tanda_terima_id)->first();
             if ($tttsj) {
                 echo "FOUND in tanda_terima_tanpa_surat_jalan (Assuming link is possible via ID?)\n";
                 print_r($tttsj);
             } else {
                 $tt = DB::table('tanda_terimas')->where('id', $prospek->tanda_terima_id)->first();
                 if ($tt) echo "FOUND in tanda_terimas\n";
             }
        }
    }
} else {
    echo "No NaikKapal with tipe_kontainer='CARGO' found.\n";
}
