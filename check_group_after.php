<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$tagihanIds = [842, 863];
$tagihans = \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $tagihanIds)->get();
foreach($tagihans as $tagihan) {
    echo "Tagihan {$tagihan->id}: group = '{$tagihan->group}', status_pranota = '{$tagihan->status_pranota}'\n";
}
?>
