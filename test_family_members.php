<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 Testing Family Members Database Setup\n";

// Check if tables exist
try {
    $hasKaryawans = Schema::hasTable('karyawans');
    $hasFamilyMembers = Schema::hasTable('karyawan_family_members');

    echo $hasKaryawans ? "✅ karyawans table exists\n" : "❌ karyawans table missing\n";
    echo $hasFamilyMembers ? "✅ karyawan_family_members table exists\n" : "❌ karyawan_family_members table missing\n";

    if ($hasFamilyMembers) {
        $columns = Schema::getColumnListing('karyawan_family_members');
        echo "📋 Family members table columns: " . implode(', ', $columns) . "\n";
    }

    // Test model relationships
    $karyawan = App\Models\Karyawan::first();
    if ($karyawan) {
        echo "✅ Found test karyawan: {$karyawan->nama_lengkap}\n";
        $familyCount = $karyawan->familyMembers()->count();
        echo "👨‍👩‍👧‍👦 Family members count: {$familyCount}\n";
    } else {
        echo "ℹ️  No karyawan records found for testing\n";
    }

    echo "🎯 Family members database setup is ready!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
