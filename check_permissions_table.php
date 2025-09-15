<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== STRUKTUR TABEL PERMISSIONS ===\n\n";

// Cek kolom yang ada di tabel permissions
$columns = Schema::getColumnListing('permissions');

echo "Kolom yang tersedia di tabel permissions:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}

echo "\nContoh data dari tabel permissions:\n";
$sampleData = DB::table('permissions')->limit(3)->get();

if ($sampleData->count() > 0) {
    foreach ($sampleData as $permission) {
        echo "- ID: {$permission->id}, Name: {$permission->name}\n";
        // Tampilkan semua kolom yang ada
        foreach ($columns as $column) {
            if ($column !== 'id' && $column !== 'name') {
                echo "  {$column}: " . ($permission->$column ?? 'NULL') . "\n";
            }
        }
        echo "\n";
    }
} else {
    echo "Tidak ada data di tabel permissions\n";
}

?>
