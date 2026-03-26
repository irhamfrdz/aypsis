<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = App\Models\TandaTerimaTanpaSuratJalan::select('tipe_kontainer')->distinct()->pluck('tipe_kontainer');
echo json_encode($data);
