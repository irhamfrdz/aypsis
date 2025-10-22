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
    echo "🧪 Testing Updated Stock Kontainer Creation Settings\n";
    echo "==================================================\n\n";

    // 1. Test the new configuration
    echo "1. New Configuration Test:\n";

    $sampleKontainer = (object)[
        'nomor_kontainer' => 'TEST999888Z',
        'ukuran' => '40'
    ];

    echo "  Sample kontainer: {$sampleKontainer->nomor_kontainer}\n";

    // Check if already exists with new status criteria
    $existing = Capsule::table('stock_kontainers')
        ->where('nomor_seri_gabungan', $sampleKontainer->nomor_kontainer)
        ->whereIn('status', ['tersedia', 'available'])
        ->first();

    if ($existing) {
        echo "  ⚠️  Already exists in stock: ID {$existing->id}, Status: {$existing->status}\n";
    } else {
        echo "  ✅ Ready to create new stock record\n";

        // Parse components
        $nomor = $sampleKontainer->nomor_kontainer;
        if (strlen($nomor) >= 11) {
            $awalan = substr($nomor, 0, 4);
            $nomorSeri = substr($nomor, 4, 6);
            $akhiran = substr($nomor, 10, 1);
        } else {
            $awalan = '';
            $nomorSeri = $nomor;
            $akhiran = '';
        }

        $newRecord = [
            'awalan_kontainer' => $awalan,
            'nomor_seri_kontainer' => $nomorSeri,
            'akhiran_kontainer' => $akhiran,
            'nomor_seri_gabungan' => $nomor,
            'ukuran' => $sampleKontainer->ukuran,
            'tipe_kontainer' => 'dry kontainer', // NEW: Updated type
            'status' => 'tersedia',              // NEW: Updated status
            'tanggal_masuk' => date('Y-m-d'),
            'keterangan' => 'Test record with new settings - dry kontainer & tersedia status',
            'tahun_pembuatan' => date('Y'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        echo "  📋 NEW Record Configuration:\n";
        echo "    Status: '{$newRecord['status']}' (updated from 'available')\n";
        echo "    Tipe: '{$newRecord['tipe_kontainer']}' (updated from 'GP')\n";
        echo "    Ukuran: '{$newRecord['ukuran']}'\n";
        echo "    Nomor: '{$newRecord['nomor_seri_gabungan']}'\n";
        echo "    Components: Awalan='{$awalan}', Seri='{$nomorSeri}', Akhiran='{$akhiran}'\n";
    }

    // 2. Check existing records with different statuses
    echo "\n2. Current Status Distribution:\n";
    $statusCounts = Capsule::table('stock_kontainers')
        ->select('status', Capsule::raw('count(*) as count'))
        ->groupBy('status')
        ->get();

    foreach($statusCounts as $stat) {
        echo "  - {$stat->status}: {$stat->count} records\n";
    }

    // 3. Check tipe_kontainer distribution
    echo "\n3. Current Tipe Distribution:\n";
    $tipeCounts = Capsule::table('stock_kontainers')
        ->select('tipe_kontainer', Capsule::raw('count(*) as count'))
        ->groupBy('tipe_kontainer')
        ->get();

    foreach($tipeCounts as $tipe) {
        $tipeDisplay = $tipe->tipe_kontainer ?: '(null)';
        echo "  - {$tipeDisplay}: {$tipe->count} records\n";
    }

    // 4. Test the duplicate check logic
    echo "\n4. Testing Duplicate Check Logic:\n";
    echo "  Checking for nomor: TEST999888Z\n";

    // Using the new logic (tersedia OR available)
    $duplicateCheck = Capsule::table('stock_kontainers')
        ->where('nomor_seri_gabungan', 'TEST999888Z')
        ->whereIn('status', ['tersedia', 'available'])
        ->count();

    echo "  Found {$duplicateCheck} matching records with status 'tersedia' or 'available'\n";

    echo "\n✅ Configuration Update Testing Completed!\n";
    echo "\n📝 Changes Summary:\n";
    echo "- Status: 'available' → 'tersedia'\n";
    echo "- Tipe: 'GP' → 'dry kontainer'\n";
    echo "- Duplicate check: Now includes both 'tersedia' and 'available' status\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

function now() {
    return date('Y-m-d H:i:s');
}
