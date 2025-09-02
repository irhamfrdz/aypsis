<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tagihan = DB::table('tagihan_kontainer_sewa')->get()->toArray();
file_put_contents(__DIR__ . '/../backups/tagihan_kontainer_sewa.json', json_encode($tagihan));
$pivot = DB::table('tagihan_kontainer_sewa_kontainers')->get()->toArray();
file_put_contents(__DIR__ . '/../backups/tagihan_kontainer_sewa_kontainers.json', json_encode($pivot));

echo 'COUNT_TAGIHAN: ' . DB::table('tagihan_kontainer_sewa')->count() . PHP_EOL;
echo 'COUNT_PIVOT: ' . DB::table('tagihan_kontainer_sewa_kontainers')->count() . PHP_EOL;
