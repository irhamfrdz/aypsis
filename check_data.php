<?php

require_once 'vendor/autoload.php';

use App\Models\PranotaTagihanKontainerSewa;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pranota = \App\Models\PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')
    ->where('status', 'belum_dibayar')
    ->with('perbaikanKontainers')
    ->first();

if ($pranota) {
    $perbaikans = $pranota->perbaikanKontainers;
    foreach ($perbaikans as $p) {
        echo 'Perbaikan ID: ' . $p->id . PHP_EOL;
        echo 'Nomor Kontainer (field): ' . ($p->nomor_kontainer ?? 'null') . PHP_EOL;
        echo 'Kontainer ID: ' . ($p->kontainer_id ?? 'null') . PHP_EOL;
        if ($p->kontainer) {
            echo 'Kontainer Nomor: ' . $p->kontainer->nomor_kontainer . PHP_EOL;
        } else {
            echo 'Kontainer not found' . PHP_EOL;
        }
        echo '---' . PHP_EOL;
    }
} else {
    echo "No pranota found" . PHP_EOL;
}

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking pranota_tagihan_kontainer_sewa table...\n";

$pranota = \App\Models\PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')
    ->where('status', 'belum_dibayar')
    ->with('perbaikanKontainers')
    ->first();

try {
    $count = PranotaTagihanKontainerSewa::count();
    echo "Total records: " . $count . "\n";

    if ($pranota) {
        if ($count > 0) {
            $perbaikans = $pranota->perbaikanKontainers;
            $first = PranotaTagihanKontainerSewa::first();
            echo "First record: " . json_encode($first->toArray()) . "\n";
            foreach ($perbaikans as $p) {
                echo 'Perbaikan ID: ' . $p->id . PHP_EOL;
                echo 'Nomor Kontainer (field): ' . ($p->nomor_kontainer ?? 'null') . PHP_EOL;
                echo 'Kontainer ID: ' . ($p->kontainer_id ?? 'null') . PHP_EOL;
                if ($p->kontainer) {
                    echo 'Kontainer Nomor: ' . $p->kontainer->nomor_kontainer . PHP_EOL;
                } else {
                    echo 'Kontainer not found' . PHP_EOL;
                }
                echo '---' . PHP_EOL;
            }
        } else {
            echo "No records found in the table.\n";
        }
    } else {
        echo "No pranota found" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
