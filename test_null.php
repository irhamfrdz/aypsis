<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use Illuminate\Support\Facades\DB;

$tagihan = \App\Models\DaftarTagihanKontainerSewa::find(842);
echo 'Before: ' . var_export($tagihan->group, true) . PHP_EOL;

\App\Models\DaftarTagihanKontainerSewa::where('id', 842)->update(['group' => null]);

$tagihan->refresh();
echo 'After: ' . var_export($tagihan->group, true) . PHP_EOL;

// Check raw database value
$raw = DB::select('SELECT `group` FROM daftar_tagihan_kontainer_sewa WHERE id = 842');
echo 'Raw DB value: ' . var_export($raw[0]->group, true) . PHP_EOL;
?>
