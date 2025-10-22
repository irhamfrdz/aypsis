<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'aypsis',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "🔍 Debugging Generate Voyage Issue\n";
    echo "==================================\n\n";

    // Test 1: Check if we have ships with nicknames
    echo "1. Checking ships data:\n";
    $ships = Capsule::table('master_kapals')
        ->select('nama_kapal', 'nickname')
        ->where('status', 'aktif')
        ->limit(3)
        ->get();

    foreach($ships as $ship) {
        echo "   - {$ship->nama_kapal} => Nickname: " . ($ship->nickname ?? 'NULL') . "\n";
    }

    // Test 2: Check if we have ports with cities
    echo "\n2. Checking ports data:\n";
    $ports = Capsule::table('master_pelabuhans')
        ->select('nama_pelabuhan', 'kota')
        ->where('status', 'aktif')
        ->limit(3)
        ->get();

    foreach($ports as $port) {
        echo "   - {$port->nama_pelabuhan} => Kota: {$port->kota}\n";
    }

    // Test 3: Simulate the API call logic
    echo "\n3. Simulating API call:\n";
    $testShip = $ships->first();
    $testPortFrom = $ports->first();
    $testPortTo = $ports->last();

    if ($testShip && $testPortFrom && $testPortTo) {
        echo "   Testing with:\n";
        echo "   - Ship: {$testShip->nama_kapal}\n";
        echo "   - From: {$testPortFrom->nama_pelabuhan}\n";
        echo "   - To: {$testPortTo->nama_pelabuhan}\n";

        // Check if ship has nickname
        if (!$testShip->nickname) {
            echo "   ❌ ERROR: Ship doesn't have nickname!\n";
        } else {
            echo "   ✅ Ship has nickname: {$testShip->nickname}\n";
        }

        // Simulate voyage generation
        $kotaCodes = [
            'Jakarta' => 'J',
            'Surabaya' => 'S',
            'Medan' => 'M',
            'Makassar' => 'K',
            'Bitung' => 'T',
            'Balikpapan' => 'L',
            'Pontianak' => 'P',
            'Banjarmasin' => 'N',
            'Batam' => 'B',
            'Semarang' => 'G',
            'Palembang' => 'A',
            'Denpasar' => 'D',
            'Jayapura' => 'Y',
            'Sorong' => 'O',
            'Ambon' => 'Z',
            'Tanjung Pinang' => 'T'
        ];

        $nicknameKapal = strtoupper(substr($testShip->nickname ?? 'XX', 0, 2));
        $noUrut = '01';
        $kodeAsal = $kotaCodes[$testPortFrom->kota] ?? strtoupper(substr($testPortFrom->kota, 0, 1));
        $kodeTujuan = $kotaCodes[$testPortTo->kota] ?? strtoupper(substr($testPortTo->kota, 0, 1));
        $tahun = date('y');

        $voyageNumber = "{$nicknameKapal}{$noUrut}{$kodeAsal}{$kodeTujuan}{$tahun}";

        echo "   🎯 Generated Voyage: {$voyageNumber}\n";
    }

    echo "\n4. Testing actual API endpoint with cURL:\n";
    if ($testShip && $testPortFrom && $testPortTo) {
        $url = "http://localhost:8000/api/pergerakan-kapal/generate-voyage?" . http_build_query([
            'nama_kapal' => $testShip->nama_kapal,
            'pelabuhan_asal' => $testPortFrom->nama_pelabuhan,
            'pelabuhan_tujuan' => $testPortTo->nama_pelabuhan
        ]);

        echo "   URL: {$url}\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo "   ❌ cURL Error: {$error}\n";
        } else {
            echo "   HTTP Code: {$httpCode}\n";
            echo "   Response:\n" . $response . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
