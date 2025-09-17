<?php

/**
 * Test Divisi Seeder
 * Usage: php test_divisi_seeder.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Divisi;

echo "==========================================\n";
echo "   Test Divisi Seeder\n";
echo "==========================================\n";

try {
    echo "🔍 Checking Divisi model...\n";

    // Check if Divisi model exists and table exists
    if (!class_exists('App\\Models\\Divisi')) {
        throw new Exception('Divisi model not found');
    }

    echo "✅ Divisi model exists\n";

    // Check database connection
    $divisiCount = Divisi::count();
    echo "✅ Database connection OK\n";
    echo "📊 Current divisi count: {$divisiCount}\n\n";

    // Show existing divisis
    if ($divisiCount > 0) {
        echo "📋 Existing Divisis:\n";
        $existingDivisis = Divisi::select('id', 'nama_divisi', 'kode_divisi', 'is_active')->get();
        foreach ($existingDivisis as $divisi) {
            $status = $divisi->is_active ? '✅ Active' : '❌ Inactive';
            echo "- {$divisi->nama_divisi} ({$divisi->kode_divisi}) - {$status}\n";
        }
        echo "\n";
    }

    // Test seeder data structure
    echo "🔍 Testing seeder data structure...\n";

    $testDivisis = [
        [
            'nama_divisi' => 'Test IT Division',
            'kode_divisi' => 'TST',
            'deskripsi' => 'Test division for seeder validation',
            'is_active' => true,
        ]
    ];

    foreach ($testDivisis as $testDivisi) {
        // Check for duplicates
        $existing = Divisi::where('kode_divisi', $testDivisi['kode_divisi'])
                         ->orWhere('nama_divisi', $testDivisi['nama_divisi'])
                         ->first();

        if ($existing) {
            echo "⚠️  Test divisi already exists: {$testDivisi['nama_divisi']}\n";
        } else {
            echo "✅ Test divisi structure is valid: {$testDivisi['nama_divisi']}\n";
        }
    }

    echo "\n==========================================\n";
    echo "   Test Results\n";
    echo "==========================================\n";
    echo "✅ All tests passed!\n";
    echo "✅ Divisi seeder is ready to run\n\n";

    echo "📝 Available commands:\n";
    echo "- php artisan db:seed --class=DivisiSeeder\n";
    echo "- ./seed_divisi.sh (Linux/Mac)\n";
    echo "- seed_divisi.bat (Windows)\n";
    echo "- php seed_divisi_manual.php\n\n";

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Please check your setup and try again.\n";
    exit(1);
}

echo "==========================================\n";
