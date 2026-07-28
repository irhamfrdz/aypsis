<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tujuan = App\Models\TujuanKegiatanUtama::whereNotNull('ke')->get(['ke']);
foreach($tujuan as $t) {
    echo $t->ke . "\n";
}
