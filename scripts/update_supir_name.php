<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalan;

echo "Memulai pembaruan nama supir...\n";

// Update Supir 1
$variants = ['nur', 'NUR', 'nur cece', 'NUR CECE', 'Nur Cece', 'Nur', 'NUR  CECE'];
$count1 = 0;
$supir1Items = SuratJalan::whereIn('supir', $variants)->get();

foreach ($supir1Items as $item) {
    if ($item->supir !== 'NUR CECE') {
        $item->supir = 'NUR CECE';
        $item->save();
        $count1++;
    }
}
echo "Berhasil memperbarui supir variants menjadi 'NUR CECE' di: " . $count1 . " baris (force update).\n";

// Update Supir 2
$count2 = 0;
$supir2Items = SuratJalan::whereIn('supir2', $variants)->get();
foreach ($supir2Items as $item) {
    if ($item->supir2 !== 'NUR CECE') {
        $item->supir2 = 'NUR CECE';
        $item->save();
        $count2++;
    }
}
echo "Berhasil memperbarui supir2 variants menjadi 'NUR CECE' di: " . $count2 . " baris (force update).\n";



echo "Selesai.\n";
