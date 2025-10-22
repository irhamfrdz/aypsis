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
    echo "🧪 Testing Auto Increment with Sample Data\n";
    echo "==========================================\n\n";

    // Insert sample voyage data for testing
    $sampleVoyages = [
        [
            'nama_kapal' => 'KM SEKAR PERMATA',
            'voyage' => 'SP01JB25',
            'pelabuhan_asal' => 'Sunda Kelapa',
            'pelabuhan_tujuan' => 'Batu Ampar',
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nama_kapal' => 'KM SEKAR PERMATA',
            'voyage' => 'SP02JT25',
            'pelabuhan_asal' => 'Sunda Kelapa',
            'pelabuhan_tujuan' => 'Sri Bintan Pura',
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nama_kapal' => 'KM ALEXINDO 1',
            'voyage' => 'A101JB25',
            'pelabuhan_asal' => 'Sunda Kelapa',
            'pelabuhan_tujuan' => 'Batu Ampar',
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];

    // Clear existing test data
    Capsule::table('pergerakan_kapal')->where('voyage', 'like', '%25')->delete();

    // Insert sample data
    Capsule::table('pergerakan_kapal')->insert($sampleVoyages);
    echo "✅ Sample data inserted\n\n";

    // Test voyage count for different ships
    $ships = ['KM SEKAR PERMATA', 'KM ALEXINDO 1'];

    foreach ($ships as $shipName) {
        $count = Capsule::table('pergerakan_kapal')
            ->where('nama_kapal', $shipName)
            ->whereYear('created_at', date('Y'))
            ->count();

        $nextSequence = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        echo "Ship: {$shipName}\n";
        echo "  Current voyages in " . date('Y') . ": {$count}\n";
        echo "  Next sequence number: {$nextSequence}\n\n";
    }

    // Test the actual generation logic
    echo "Testing generation for KM SEKAR PERMATA (should be 03):\n";
    $currentYear = date('Y');
    $lastVoyageCount = Capsule::table('pergerakan_kapal')
        ->where('nama_kapal', 'KM SEKAR PERMATA')
        ->whereYear('created_at', $currentYear)
        ->count();
    $noUrut = str_pad($lastVoyageCount + 1, 2, '0', STR_PAD_LEFT);
    echo "  Calculated sequence: {$noUrut}\n";

    echo "\n✅ Auto increment test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

function now() {
    return date('Y-m-d H:i:s');
}
