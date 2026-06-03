11<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$controller = new App\Http\Controllers\ReportTandaTerimaJakartaController;
$ref = new ReflectionMethod($controller, 'getAggregatedData');
$ref->setAccessible(true);
$data = $ref->invoke($controller, '2026-05-20', '2026-06-02');

$row = $data->first(function ($item) {
    return $item['no_tt'] === 'JP0013134';
});

if ($row) {
    echo "Row for JP0013134 found in report data:\n";
    print_r($row);
} else {
    echo "Row for JP0013134 NOT found in report data\n";
}
