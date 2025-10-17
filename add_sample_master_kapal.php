<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Menambahkan sample data Master Kapal...\n\n";

$sampleKapals = [
    [
        'kode' => 'MK001',
        'kode_kapal' => 'SHP001',
        'nama_kapal' => 'MV SINAR JAYA',
        'nickname' => 'SINAR',
        'pelayaran' => 'PT. Pelayaran Nusantara',
        'catatan' => 'Kapal kargo reguler',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'kode' => 'MK002',
        'kode_kapal' => 'SHP002',
        'nama_kapal' => 'MV SAMUDRA RAYA',
        'nickname' => 'SAMUDRA',
        'pelayaran' => 'PT. Samudra Lines',
        'catatan' => 'Kapal kontainer besar',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'kode' => 'MK003',
        'kode_kapal' => 'SHP003',
        'nama_kapal' => 'MV NUSANTARA MAKMUR',
        'nickname' => 'NUSA',
        'pelayaran' => 'PT. Indonesia Shipping',
        'catatan' => 'Kapal ro-ro',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

DB::beginTransaction();
try {
    foreach ($sampleKapals as $kapal) {
        // Check if exists
        $exists = DB::table('master_kapals')
            ->where('kode', $kapal['kode'])
            ->exists();

        if (!$exists) {
            DB::table('master_kapals')->insert($kapal);
            echo "✅ {$kapal['nama_kapal']} berhasil ditambahkan\n";
        } else {
            echo "⚠️  {$kapal['nama_kapal']} sudah ada, skip\n";
        }
    }

    DB::commit();
    echo "\n✅ Selesai! Total kapal aktif: " . DB::table('master_kapals')->where('status', 'aktif')->count() . "\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
