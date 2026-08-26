<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = 0;
foreach (\App\Models\StockBan::whereNotNull('keterangan')->get() as $ban) {
    if (preg_match_all('/(P\d{9}|P-?\d{2}-?\d{2}-?\d{4})/i', $ban->keterangan, $matches)) {
        $lastMatch = end($matches[1]);
        if ($ban->nomor_pakai != $lastMatch) {
            $ban->nomor_pakai = $lastMatch;
            $ban->save();
            $count++;
        }
    }
}
echo "Updated: $count\n";
