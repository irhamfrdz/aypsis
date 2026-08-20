<?php
require 'vendor/autoload.php'; 
$app = require_once 'bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

$tt = \App\Models\TandaTerimaTanpaSuratJalan::where('no_tanda_terima', 'LIKE', '%15344%')->first();
if($tt) {
    $prospeks = \App\Models\Prospek::where('no_surat_jalan', 'LIKE', '%15344%')->get();
    foreach($prospeks as $prospek) {
        $prospek->update([
            'barang' => $tt->jenis_barang ?? $tt->nama_barang ?? 'Barang'
        ]);
        echo "Updated Prospek ID: " . $prospek->id . " to " . $prospek->barang . "\n";
        
        // Update NaikKapal as well if exists
        $nks = \App\Models\NaikKapal::where('prospek_id', $prospek->id)->get();
        foreach($nks as $nk) {
            $nk->update([
                'jenis_barang' => $prospek->barang
            ]);
            echo "Updated NaikKapal ID: " . $nk->id . "\n";
        }
    }
}
