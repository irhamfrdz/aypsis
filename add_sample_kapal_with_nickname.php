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
    // Sample data kapal dengan nickname
    $kapals = [
        [
            'nama_kapal' => 'KM Sunda Kelapa',
            'nickname' => 'SK',
            'no_register' => 'REG001',
            'gt' => '5000',
            'dw' => '3000',
            'loa' => '80',
            'pelayaran' => 'PT Pelayaran Indonesia',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_kapal' => 'KM Batu Ampar',
            'nickname' => 'BA',
            'no_register' => 'REG002',
            'gt' => '4500',
            'dw' => '2800',
            'loa' => '75',
            'pelayaran' => 'PT Pelayaran Nusantara',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama_kapal' => 'KM Tanjung Perak',
            'nickname' => 'TP',
            'no_register' => 'REG003',
            'gt' => '5500',
            'dw' => '3200',
            'loa' => '85',
            'pelayaran' => 'PT Pelayaran Jaya',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    // Check if data already exists
    $existingCount = Capsule::table('master_kapals')->count();

    if ($existingCount == 0) {
        Capsule::table('master_kapals')->insert($kapals);
        echo "✅ Sample data kapal dengan nickname berhasil ditambahkan!\n";
    } else {
        echo "ℹ️  Data kapal sudah ada, tidak menambahkan data baru.\n";

        // Update existing data to add nickname if not exists
        $existingKapals = Capsule::table('master_kapals')->whereNull('nickname')->get();

        foreach ($existingKapals as $kapal) {
            $nickname = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $kapal->nama_kapal), 0, 2));
            Capsule::table('master_kapals')
                ->where('id', $kapal->id)
                ->update(['nickname' => $nickname, 'updated_at' => now()]);
        }

        echo "✅ Nickname ditambahkan untuk kapal yang belum memiliki nickname.\n";
    }

    // Display current data
    echo "\n📋 Data Master Kapal:\n";
    $currentKapals = Capsule::table('master_kapals')->get();
    foreach ($currentKapals as $kapal) {
        echo "- {$kapal->nama_kapal} (Nickname: {$kapal->nickname})\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

function now() {
    return date('Y-m-d H:i:s');
}
