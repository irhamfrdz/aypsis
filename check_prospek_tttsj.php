<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Searching for Prospek from TTTSJ ===\n";
$prospek = DB::table('prospek')
    ->where('keterangan', 'like', '%Auto-generated from Tanda Terima Tanpa Surat Jalan%')
    ->first();

if ($prospek) {
    print_r($prospek);
    
    echo "\nTrying to find matching TandaTerimaTanpaSuratJalan...\n";
    // Parse number from keterangan?
    if (preg_match('/Tanda Terima Tanpa Surat Jalan: (.*?)(\s\||$)/', $prospek->keterangan, $matches)) {
        $noTT = trim($matches[1]);
        echo "Parsed No TT: " . $noTT . "\n";
        
        $tttsj = DB::table('tanda_terima_tanpa_surat_jalan')->where('no_tanda_terima', $noTT)->first();
        if ($tttsj) {
            echo "Found TTTSJ by number!\n";
            print_r($tttsj);
        } else {
            echo "TTTSJ record not found for number $noTT\n";
        }
    }
} else {
    echo "No Prospek found with that description.\n";
}
