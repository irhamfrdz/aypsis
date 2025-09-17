<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Divisi;

echo "🔧 Menambahkan Data Sample Divisi\n";
echo "=================================\n\n";

$sampleDivisis = [
    [
        'nama_divisi' => 'IT Development',
        'kode_divisi' => 'ITD',
        'deskripsi' => 'Divisi pengembangan sistem dan teknologi informasi',
        'is_active' => true
    ],
    [
        'nama_divisi' => 'Human Resources',
        'kode_divisi' => 'HRD',
        'deskripsi' => 'Divisi sumber daya manusia dan manajemen karyawan',
        'is_active' => true
    ],
    [
        'nama_divisi' => 'Finance',
        'kode_divisi' => 'FIN',
        'deskripsi' => 'Divisi keuangan dan akuntansi',
        'is_active' => true
    ],
    [
        'nama_divisi' => 'Operations',
        'kode_divisi' => 'OPS',
        'deskripsi' => 'Divisi operasional dan pelayanan',
        'is_active' => true
    ],
    [
        'nama_divisi' => 'Marketing',
        'kode_divisi' => 'MKT',
        'deskripsi' => 'Divisi pemasaran dan penjualan',
        'is_active' => false
    ]
];

$created = 0;
$skipped = 0;

foreach ($sampleDivisis as $divisiData) {
    $existing = Divisi::where('kode_divisi', $divisiData['kode_divisi'])->first();

    if ($existing) {
        echo "  ⏭️  {$divisiData['nama_divisi']} - SUDAH ADA (skip)\n";
        $skipped++;
    } else {
        Divisi::create($divisiData);
        echo "  ✅ {$divisiData['nama_divisi']} - DITAMBAHKAN\n";
        $created++;
    }
}

echo "\n📊 Ringkasan:\n";
echo "=============\n";
echo "  ✅ Divisi baru: {$created}\n";
echo "  ⏭️  Divisi yang dilewati: {$skipped}\n";
echo "  📋 Total divisi sekarang: " . Divisi::count() . "\n";

echo "\n🎉 Proses selesai!\n";
echo "\n💡 Anda dapat mengakses master divisi di: /master/divisi\n";
