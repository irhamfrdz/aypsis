<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$svc = app(\App\Services\GpsIdService::class);
$imei = \App\Models\Mobil::whereNotNull('imei_gps')->where('imei_gps', '!=', '')->first()->imei_gps;
$res = $svc->getLatestLocation($imei);
echo json_encode(array_keys($res));
echo "\n";
if(isset($res['message'])) {
    if (is_array($res['message'])) {
        echo json_encode(array_keys($res['message']));
    } else {
        echo gettype($res['message']);
    }
}
