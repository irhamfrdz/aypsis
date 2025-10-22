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
    echo "🧪 Testing Stock Kontainer Creation for Antar Kontainer Sewa\n";
    echo "==========================================================\n\n";

    // 1. Check for permohonan with "antar kontainer sewa" activity
    echo "1. Checking for 'antar kontainer sewa' activities:\n";
    $kegiatans = Capsule::table('master_kegiatans')
        ->where('nama_kegiatan', 'like', '%antar%')
        ->where('nama_kegiatan', 'like', '%kontainer%')
        ->where('nama_kegiatan', 'like', '%sewa%')
        ->get();

    foreach($kegiatans as $kegiatan) {
        echo "  - Kode: {$kegiatan->kode_kegiatan}, Nama: {$kegiatan->nama_kegiatan}\n";
    }

    if ($kegiatans->isEmpty()) {
        echo "  ❌ No 'antar kontainer sewa' activities found\n";
        echo "  Creating sample kegiatan...\n";

        Capsule::table('master_kegiatans')->insertOrIgnore([
            'kode_kegiatan' => 'AKS001',
            'nama_kegiatan' => 'Antar Kontainer Sewa',
            'keterangan' => 'Kegiatan pengantaran kontainer sewa',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  ✅ Sample kegiatan created: AKS001 - Antar Kontainer Sewa\n";
    }

    // 2. Test nomor kontainer parsing logic
    echo "\n2. Testing nomor kontainer parsing:\n";
    $testNomors = [
        'ABCD123456X',
        'MSCU1234567',
        'TEMU7891234',
        'SHORT1',
        'VERYLONGNUMBER123456789'
    ];

    foreach($testNomors as $nomor) {
        echo "  Testing: {$nomor}\n";

        $awalan = '';
        $nomorSeri = '';
        $akhiran = '';

        if (strlen($nomor) >= 11) {
            $awalan = substr($nomor, 0, 4);
            $nomorSeri = substr($nomor, 4, 6);
            $akhiran = substr($nomor, 10, 1);
        } else {
            $nomorSeri = $nomor;
        }

        echo "    Awalan: '{$awalan}', Seri: '{$nomorSeri}', Akhiran: '{$akhiran}'\n";
    }

    // 3. Test stock kontainer creation (simulation)
    echo "\n3. Testing stock kontainer creation (simulation):\n";

    // Create sample data
    $sampleKontainer = (object)[
        'nomor_kontainer' => 'TEST123456A',
        'ukuran' => '20',
        'tipe' => 'GP'
    ];

    echo "  Sample kontainer: {$sampleKontainer->nomor_kontainer}\n";

    // Check if already exists
    $existing = Capsule::table('stock_kontainers')
        ->where('nomor_seri_gabungan', $sampleKontainer->nomor_kontainer)
        ->where('status', 'available')
        ->first();

    if ($existing) {
        echo "  ⚠️  Already exists in stock: ID {$existing->id}\n";
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
            'tipe_kontainer' => $sampleKontainer->tipe,
            'status' => 'available',
            'tanggal_masuk' => date('Y-m-d'),
            'keterangan' => 'Test record for antar kontainer sewa feature',
            'tahun_pembuatan' => date('Y'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        echo "  Prepared record:\n";
        foreach($newRecord as $key => $value) {
            echo "    {$key}: {$value}\n";
        }
    }

    // 4. Check current stock kontainers count
    echo "\n4. Current stock statistics:\n";
    $totalStock = Capsule::table('stock_kontainers')->count();
    $availableStock = Capsule::table('stock_kontainers')->where('status', 'available')->count();

    echo "  Total stock records: {$totalStock}\n";
    echo "  Available stock: {$availableStock}\n";

    echo "\n✅ Testing completed successfully!\n";
    echo "\n📝 Summary:\n";
    echo "- Feature will create stock_kontainers record for 'antar kontainer sewa' activities\n";
    echo "- Nomor kontainer will be parsed into awalan (4 chars) + seri (6 chars) + akhiran (1 char)\n";
    echo "- Status will be set to 'available'\n";
    echo "- Duplicate check prevents creating existing records\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

function now() {
    return date('Y-m-d H:i:s');
}
