<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Quick check untuk memastikan semua data master tersedia
echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║         QUICK CHECK - DATA MASTER DATABASE                 ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$checks = [
    ['table' => 'master_kegiatans', 'label' => 'Master Kegiatan', 'min' => 10],
    ['table' => 'divisis', 'label' => 'Divisi', 'min' => 9],
    ['table' => 'pekerjaans', 'label' => 'Pekerjaan', 'min' => 50],
    ['table' => 'banks', 'label' => 'Bank', 'min' => 5],
    ['table' => 'cabangs', 'label' => 'Cabang', 'min' => 3],
    ['table' => 'akun_coa', 'label' => 'Akun COA', 'min' => 400],
    ['table' => 'pajaks', 'label' => 'Pajak', 'min' => 10],
    ['table' => 'master_pricelist_sewa_kontainers', 'label' => 'Pricelist Sewa', 'min' => 5],
];

$allOk = true;

foreach ($checks as $check) {
    $count = DB::table($check['table'])->count();
    $status = $count >= $check['min'] ? '✓ OK' : '✗ WARNING';
    $color = $count >= $check['min'] ? '' : '⚠ ';
    
    echo sprintf("%-30s : %s%5d record  [%s]\n", 
        $check['label'], 
        $color,
        $count, 
        $status
    );
    
    if ($count < $check['min']) {
        $allOk = false;
    }
}

echo "\n";
echo str_repeat("─", 60) . "\n";

if ($allOk) {
    echo "✓ Semua data master tersedia dan lengkap!\n";
} else {
    echo "⚠ Beberapa tabel memiliki data kurang dari yang diharapkan.\n";
}

echo str_repeat("─", 60) . "\n";
echo "\n";
