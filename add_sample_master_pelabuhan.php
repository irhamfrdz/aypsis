<?php

require_once 'vendor/autoload.php';

use App\Models\MasterPelabuhan;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create sample master pelabuhan data
$pelabuhanData = [
    [
        'nama_pelabuhan' => 'Pelabuhan Tanjung Priok',
        'kota' => 'Jakarta',
        'status' => 'aktif',
        'keterangan' => 'Pelabuhan terbesar di Indonesia dan menjadi pintu gerbang utama perdagangan internasional.'
    ],
    [
        'nama_pelabuhan' => 'Pelabuhan Tanjung Perak',
        'kota' => 'Surabaya',
        'status' => 'aktif',
        'keterangan' => 'Pelabuhan utama di Jawa Timur yang melayani perdagangan domestik dan internasional.'
    ],
    [
        'nama_pelabuhan' => 'Pelabuhan Belawan',
        'kota' => 'Medan',
        'status' => 'aktif',
        'keterangan' => 'Pelabuhan utama di Sumatera Utara, pintu gerbang perdagangan internasional.'
    ],
    [
        'nama_pelabuhan' => 'Pelabuhan Makassar',
        'kota' => 'Makassar',
        'status' => 'aktif',
        'keterangan' => 'Pelabuhan utama di Sulawesi Selatan dengan aktivitas perdagangan regional.'
    ],
    [
        'nama_pelabuhan' => 'Pelabuhan Bitung',
        'kota' => 'Bitung',
        'status' => 'aktif',
        'keterangan' => 'Pelabuhan strategis di Sulawesi Utara dengan koneksi ke Asia Pasifik.'
    ],
    [
        'nama_pelabuhan' => 'Pelabuhan Balikpapan',
        'kota' => 'Balikpapan',
        'status' => 'aktif',
        'keterangan' => 'Pelabuhan utama di Kalimantan Timur, melayani industri minyak dan gas.'
    ],
    [
        'nama_pelabuhan' => 'Pelabuhan Pontianak',
        'kota' => 'Pontianak',
        'status' => 'nonaktif',
        'keterangan' => 'Pelabuhan di Kalimantan Barat yang sedang dalam tahap renovasi.'
    ],
    [
        'nama_pelabuhan' => 'Pelabuhan Banjarmasin',
        'kota' => 'Banjarmasin',
        'status' => 'aktif',
        'keterangan' => 'Pelabuhan sungai terbesar di Kalimantan Selatan.'
    ]
];

foreach ($pelabuhanData as $data) {
    MasterPelabuhan::create($data);
    echo "Created: {$data['nama_pelabuhan']} - {$data['kota']}\n";
}

echo "\nSample Master Pelabuhan data created successfully!\n";
echo "Total: " . count($pelabuhanData) . " pelabuhan created.\n";
