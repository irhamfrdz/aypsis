<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check all PembayaranOb dp entries
echo "All PembayaranOb with dp_amount > 0:\n";
$pbs = App\Models\PembayaranOb::where('dp_amount', '>', 0)->get();
foreach ($pbs as $pb) {
    $raw = $pb->jumlah_per_supir;
    $data = is_array($raw) ? $raw : json_decode($raw, true);
    echo "\n  PembayaranOb ID={$pb->id}, dp_amount={$pb->dp_amount}\n";
    if (is_array($data)) {
        foreach ($data as $supirId => $jumlah) {
            $karyawan = App\Models\Karyawan::find($supirId);
            echo "    ID=$supirId, jumlah=$jumlah, lengkap=" . ($karyawan ? $karyawan->nama_lengkap : 'null') . ", panggilan=" . ($karyawan ? $karyawan->nama_panggilan : 'null') . "\n";
        }
    }
}

// Now check the pranota that the user is seeing (AP03BJ26)
echo "\n\nPranota for voyage AP03BJ26:\n";
$pranotas = App\Models\PranotaOb::where('no_voyage','AP03BJ26')->get();
foreach ($pranotas as $pranota) {
    echo "  Pranota {$pranota->nomor_pranota}:\n";
    $items = $pranota->getEnrichedItems();
    $supirs = [];
    foreach ($items as $item) {
        $s = strtoupper(trim($item['supir'] ?? '-'));
        if (!isset($supirs[$s])) $supirs[$s] = 0;
        $supirs[$s]++;
    }
    foreach ($supirs as $s => $cnt) {
        echo "    supir: $s ($cnt items)\n";
    }
}
