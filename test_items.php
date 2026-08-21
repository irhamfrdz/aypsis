<?php
require 'vendor/autoload.php'; 
$app = require_once 'bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

$tt2 = \App\Models\TandaTerimaTanpaSuratJalan::where('no_tanda_terima', 'LIKE', '%15344%')->first(); 
if($tt2) {
    echo "Tanda Terima ID: " . $tt2->id . "\n";
    echo "Jenis Barang: " . $tt2->jenis_barang . "\n";
    echo "Updated At: " . $tt2->updated_at . "\n";
}

$prospek = \App\Models\Prospek::where('no_surat_jalan', 'LIKE', '%15344%')->first();
if($prospek) {
    echo "Prospek ID: " . $prospek->id . "\n";
    echo "Barang: " . $prospek->barang . "\n";
    echo "Created At: " . $prospek->created_at . "\n";
    echo "Updated At: " . $prospek->updated_at . "\n";
}
