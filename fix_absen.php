<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
\Illuminate\Support\Facades\DB::statement("UPDATE absensis SET tipe = 'Masuk' WHERE HOUR(waktu) >= 4 AND HOUR(waktu) < 12 AND tipe = 'Pulang'");
echo 'Done';
