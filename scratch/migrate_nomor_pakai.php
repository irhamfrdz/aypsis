<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = 0;
foreach (\App\Models\StockBan::whereNull('nomor_pakai')->get() as $ban) {
    if (preg_match('/(P-?\d{2}-?\d{2}-?\d{4}|P26\d{7})/i', $ban->keterangan, $matches)) {
        $ban->nomor_pakai = $matches[1];
        $ban->save();
        $count++;
    }
}
echo "Updated: $count\n";
