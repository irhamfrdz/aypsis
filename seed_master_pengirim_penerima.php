<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MasterPengirimPenerima;
use App\Models\User;

$admin = User::where('role', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "Creating test data for Master Pengirim/Penerima...\n\n";

$data = [
    [
        'nama' => 'PT Maju Jaya',
        'alamat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
        'npwp' => '01.234.567.8-901.000',
    ],
    [
        'nama' => 'CV Berkah Sejahtera',
        'alamat' => 'Jl. Gatot Subroto No. 45, Surabaya',
        'npwp' => '02.345.678.9-012.000',
    ],
    [
        'nama' => 'PT Sentosa Makmur',
        'alamat' => 'Jl. Ahmad Yani No. 78, Bandung',
        'npwp' => '03.456.789.0-123.000',
    ],
    [
        'nama' => 'Toko Sumber Rezeki',
        'alamat' => 'Jl. Pahlawan No. 90, Semarang',
        'npwp' => null,
    ],
    [
        'nama' => 'PT Global Trading',
        'alamat' => 'Jl. Veteran No. 12, Medan',
        'npwp' => '05.678.901.2-345.000',
    ],
];

foreach ($data as $index => $item) {
    $pengirimPenerima = MasterPengirimPenerima::create([
        'kode' => MasterPengirimPenerima::generateKode(),
        'nama' => $item['nama'],
        'alamat' => $item['alamat'],
        'npwp' => $item['npwp'],
        'status' => 'active',
        'created_by' => $admin->id,
        'updated_by' => $admin->id,
    ]);
    
    echo "✓ Created: {$pengirimPenerima->kode} - {$pengirimPenerima->nama}\n";
}

echo "\n✅ Test data created successfully!\n";
echo "Total records: " . MasterPengirimPenerima::count() . "\n";
