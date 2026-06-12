<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

class TestController extends \App\Http\Controllers\RekapBiayaKapalController
{
    public function test($id, $kapal, $voyage)
    {
        $item = BiayaKapal::with([
            'barangDetails',
            'airDetails',
            'tkbmDetails',
            'truckingDetails',
            'stuffingDetails',
            'perlengkapanDetails',
            'labuhTambatDetails',
            'oppOptDetails',
            'thcDetails',
            'loloDetails',
            'storageDetails',
            'freightDetails',
            'perijinanDetails',
            'meratusDetails',
            'temasDetails',
            'tantoDetails',
            'demurrageDetails',
            'tenagaKerjaDetails',
        ])->find($id);

        $reflection = new \ReflectionClass($this);
        $method = $reflection->getMethod('getApportionedCostForRecord');
        $method->setAccessible(true);

        return $method->invoke($this, $item, $kapal, $voyage);
    }
}

$test = new TestController;
$res = $test->test(346, 'KM MERATUS KAMPAR', 'MERATUSWS114N');
print_r($res);
