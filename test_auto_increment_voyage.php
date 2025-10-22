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
    echo "🚢 Testing Voyage Number Generation with Auto-Increment\n";
    echo "======================================================\n\n";

    // Get test data
    $kapal = Capsule::table('master_kapals')
        ->where('nama_kapal', 'KM SEKAR PERMATA')
        ->first();

    $pelabuhanJakarta = Capsule::table('master_pelabuhans')
        ->where('kota', 'Jakarta')
        ->first();

    $pelabuhanBatam = Capsule::table('master_pelabuhans')
        ->where('kota', 'Batam')
        ->first();

    if (!$kapal || !$pelabuhanJakarta || !$pelabuhanBatam) {
        echo "❌ Test data tidak lengkap\n";
        exit;
    }

    echo "Test data:\n";
    echo "- Kapal: {$kapal->nama_kapal} (Nickname: {$kapal->nickname})\n";
    echo "- Asal: {$pelabuhanJakarta->nama_pelabuhan} ({$pelabuhanJakarta->kota})\n";
    echo "- Tujuan: {$pelabuhanBatam->nama_pelabuhan} ({$pelabuhanBatam->kota})\n\n";

    // Check existing voyage count for this ship in current year
    $currentYear = date('Y');
    $existingCount = Capsule::table('pergerakan_kapal')
        ->where('nama_kapal', $kapal->nama_kapal)
        ->whereYear('created_at', $currentYear)
        ->count();

    echo "Existing voyages for {$kapal->nama_kapal} in {$currentYear}: {$existingCount}\n";
    echo "Next voyage number should have sequence: " . str_pad($existingCount + 1, 2, '0', STR_PAD_LEFT) . "\n\n";

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

    $nicknameKapal = strtoupper(substr($kapal->nickname, 0, 2));
    $noUrut = str_pad($existingCount + 1, 2, '0', STR_PAD_LEFT);
    $kodeAsal = $kotaCodes[$pelabuhanJakarta->kota] ?? strtoupper(substr($pelabuhanJakarta->kota, 0, 1));
    $kodeTujuan = $kotaCodes[$pelabuhanBatam->kota] ?? strtoupper(substr($pelabuhanBatam->kota, 0, 1));
    $tahun = date('y');

    $voyageNumber = "{$nicknameKapal}{$noUrut}{$kodeAsal}{$kodeTujuan}{$tahun}";

    echo "🎯 Generated Voyage Number: {$voyageNumber}\n";
    echo "Breakdown:\n";
    echo "- Nickname Kapal: {$nicknameKapal} (2 digit)\n";
    echo "- No Urut: {$noUrut} (2 digit - auto increment)\n";
    echo "- Kode Asal: {$kodeAsal} (1 digit dari kota {$pelabuhanJakarta->kota})\n";
    echo "- Kode Tujuan: {$kodeTujuan} (1 digit dari kota {$pelabuhanBatam->kota})\n";
    echo "- Tahun: {$tahun} (2 digit)\n\n";

    // Test multiple generations
    echo "Testing multiple generations:\n";
    for ($i = 1; $i <= 3; $i++) {
        $testNoUrut = str_pad($existingCount + $i, 2, '0', STR_PAD_LEFT);
        $testVoyage = "{$nicknameKapal}{$testNoUrut}{$kodeAsal}{$kodeTujuan}{$tahun}";
        echo "  Generation {$i}: {$testVoyage}\n";
    }

    echo "\n✅ Testing completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
