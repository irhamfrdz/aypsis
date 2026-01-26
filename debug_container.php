<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

$container = 'EMCU6063235';
$records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)
    ->orderBy('periode', 'asc')
    ->get();

echo "Data for container: " . $container . "\n";
echo "Total records: " . $records->count() . "\n";
foreach ($records as $record) {
    echo "ID: {$record->id}, Vendor: '{$record->vendor}', Periode: {$record->periode}, Tgl Awal: {$record->tanggal_awal->toDateString()}, Tgl Akhir: {$record->tanggal_akhir->toDateString()}\n";
}
