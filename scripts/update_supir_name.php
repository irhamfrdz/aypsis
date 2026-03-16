<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalan;

echo "Memulai pembaruan nama supir...\n";

// Update Supir 1
$count1 = SuratJalan::where('supir', 'nur')->update(['supir' => 'nur cece']);
echo "Berhasil memperbarui supir 'nur' menjadi 'nur cece' di: " . $count1 . " baris.\n";

// Update Supir 2
$count2 = SuratJalan::where('supir2', 'nur')->update(['supir2' => 'nur cece']);
echo "Berhasil memperbarui supir2 'nur' menjadi 'nur cece' di: " . $count2 . " baris.\n";

echo "Selesai.\n";
