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
    echo "🚢 Testing Voyage Number Generation\n";
    echo "==================================\n\n";

    // Test case 1: Sekar Permata from Jakarta to Batam
    echo "Test Case 1: KM SEKAR PERMATA (Jakarta → Batam)\n";

    // Get kapal data
    $kapal = Capsule::table('master_kapals')
        ->where('nama_kapal', 'KM SEKAR PERMATA')
        ->first();

    if (!$kapal) {
        echo "❌ Kapal KM SEKAR PERMATA tidak ditemukan\n";
        exit;
    }

    // Get pelabuhan data
    $pelabuhanJakarta = Capsule::table('master_pelabuhans')
        ->where('kota', 'Jakarta')
        ->first();

    $pelabuhanBatam = Capsule::table('master_pelabuhans')
        ->where('kota', 'Batam')
        ->first();

    if (!$pelabuhanJakarta || !$pelabuhanBatam) {
        echo "❌ Data pelabuhan tidak lengkap\n";
        exit;
    }

    echo "Kapal: {$kapal->nama_kapal} (Nickname: {$kapal->nickname})\n";
    echo "Asal: {$pelabuhanJakarta->nama_pelabuhan} ({$pelabuhanJakarta->kota})\n";
    echo "Tujuan: {$pelabuhanBatam->nama_pelabuhan} ({$pelabuhanBatam->kota})\n";

    // Generate voyage number
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
    $noUrut = '01';
    $kodeAsal = $kotaCodes[$pelabuhanJakarta->kota] ?? strtoupper(substr($pelabuhanJakarta->kota, 0, 1));
    $kodeTujuan = $kotaCodes[$pelabuhanBatam->kota] ?? strtoupper(substr($pelabuhanBatam->kota, 0, 1));
    $tahun = date('y');

    $voyageNumber = "{$nicknameKapal}{$noUrut}{$kodeAsal}{$kodeTujuan}{$tahun}";

    echo "\n🎯 Generated Voyage Number: {$voyageNumber}\n";
    echo "Breakdown:\n";
    echo "- Nickname Kapal: {$nicknameKapal} (2 digit)\n";
    echo "- No Urut Jakarta: {$noUrut} (2 digit)\n";
    echo "- Kode Asal: {$kodeAsal} (1 digit dari kota {$pelabuhanJakarta->kota})\n";
    echo "- Kode Tujuan: {$kodeTujuan} (1 digit dari kota {$pelabuhanBatam->kota})\n";
    echo "- Tahun: {$tahun} (2 digit)\n";

    echo "\n" . str_repeat("=", 50) . "\n";

    // Test case 2: Different ship and route
    echo "Test Case 2: KM ALEXINDO 1 (Jakarta → Tanjung Pinang)\n";

    $kapal2 = Capsule::table('master_kapals')
        ->where('nama_kapal', 'KM ALEXINDO 1')
        ->first();

    $pelabuhanTanjungPinang = Capsule::table('master_pelabuhans')
        ->where('kota', 'Tanjung Pinang')
        ->first();

    if ($kapal2 && $pelabuhanTanjungPinang) {
        $nicknameKapal2 = strtoupper(substr($kapal2->nickname, 0, 2));
        $kodeAsal2 = $kotaCodes[$pelabuhanJakarta->kota];
        $kodeTujuan2 = $kotaCodes[$pelabuhanTanjungPinang->kota] ?? strtoupper(substr($pelabuhanTanjungPinang->kota, 0, 1));

        $voyageNumber2 = "{$nicknameKapal2}{$noUrut}{$kodeAsal2}{$kodeTujuan2}{$tahun}";

        echo "Kapal: {$kapal2->nama_kapal} (Nickname: {$kapal2->nickname})\n";
        echo "🎯 Generated Voyage Number: {$voyageNumber2}\n";
    }

    echo "\n✅ Testing completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
