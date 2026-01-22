<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Columns in 'tanda_terimas' table ===\n";
print_r(Schema::getColumnListing('tanda_terimas'));


echo "\n=== Sample Prospek (CARGO) ===\n";
$prospek = DB::table('prospek')->where('tipe', 'CARGO')->first();
if ($prospek) {
    print_r($prospek);
    
    if ($prospek->tanda_terima_id) {
        echo "\nLinked Tanda Terima ID: " . $prospek->tanda_terima_id . "\n";
        // Try to find in tanda_terimas
        $tt = DB::table('tanda_terimas')->where('id', $prospek->tanda_terima_id)->first();
        if ($tt) echo "Found in tanda_terimas\n";
        
        // Try to find in tanda_terima_tanpa_surat_jalan (assuming ID match, which is risky but maybe implied?)
        $tttsj = DB::table('tanda_terima_tanpa_surat_jalan')->where('id', $prospek->tanda_terima_id)->first();
        if ($tttsj) {
            echo "Found in tanda_terima_tanpa_surat_jalan\n";
            print_r($tttsj);
        }
    }
} else {
    echo "No Prospek with tipe='CARGO' found.\n";
    // Try finding one with nomor_kontainer like 'CARGO'
    $prospek = DB::table('prospek')->where('nomor_kontainer', 'like', '%CARGO%')->first();
    if ($prospek) {
        echo "Found Prospek with nomor_kontainer like CARGO:\n";
        print_r($prospek);
    }
}
