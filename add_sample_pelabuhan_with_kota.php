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
    // Sample data pelabuhan dengan kota yang jelas
    $pelabuhans = [
        [
            'nama_pelabuhan' => 'Pelabuhan Sunda Kelapa',
            'kota' => 'Jakarta',
            'keterangan' => 'Pelabuhan utama di Jakarta',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_pelabuhan' => 'Pelabuhan Tanjung Priok',
            'kota' => 'Jakarta',
            'keterangan' => 'Pelabuhan kontainer utama Indonesia',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_pelabuhan' => 'Pelabuhan Batu Ampar',
            'kota' => 'Batam',
            'keterangan' => 'Pelabuhan utama di Batam',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_pelabuhan' => 'Pelabuhan Tanjung Perak',
            'kota' => 'Surabaya',
            'keterangan' => 'Pelabuhan utama di Surabaya',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_pelabuhan' => 'Pelabuhan Belawan',
            'kota' => 'Medan',
            'keterangan' => 'Pelabuhan utama di Medan',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_pelabuhan' => 'Pelabuhan Makassar',
            'kota' => 'Makassar',
            'keterangan' => 'Pelabuhan utama di Makassar',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_pelabuhan' => 'Pelabuhan Soekarno Hatta',
            'kota' => 'Makassar',
            'keterangan' => 'Pelabuhan internasional Makassar',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    // Check if data already exists
    $existingCount = Capsule::table('master_pelabuhans')->count();

    if ($existingCount == 0) {
        Capsule::table('master_pelabuhans')->insert($pelabuhans);
        echo "✅ Sample data pelabuhan berhasil ditambahkan!\n";
    } else {
        echo "ℹ️  Data pelabuhan sudah ada, tidak menambahkan data baru.\n";
    }

    // Display current data
    echo "\n📋 Data Master Pelabuhan:\n";
    $currentPelabuhans = Capsule::table('master_pelabuhans')->get();
    foreach ($currentPelabuhans as $pelabuhan) {
        echo "- {$pelabuhan->nama_pelabuhan} (Kota: {$pelabuhan->kota})\n";
    }

    echo "\n📝 Format Nomor Voyage yang akan digenerate:\n";
    echo "Format: [Nickname Kapal 2 digit][No Urut 01][Kode Kota Asal 1 digit][Kode Kota Tujuan 1 digit][Tahun 2 digit]\n";
    echo "Contoh: SK01JB25 = SK (Sunda Kelapa) + 01 (urut Jakarta) + J (Jakarta) + B (Batam) + 25 (tahun 2025)\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

function now() {
    return date('Y-m-d H:i:s');
}
