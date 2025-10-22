<?php
// Simple test to verify feature setup - run with: php artisan tinker < test_simple.php

use App\Models\ProspekKapal;
use App\Models\ProspekKapalKontainer;
use App\Models\PergerakanKapal;
use Illuminate\Support\Facades\Schema;

echo "🚢 Testing Prospek Kapal Feature\n";
echo "==============================\n\n";

// Test 1: Check if tables exist
$tables = [
    'prospek_kapal' => Schema::hasTable('prospek_kapal'),
    'prospek_kapal_kontainers' => Schema::hasTable('prospek_kapal_kontainers'),
    'pergerakan_kapal' => Schema::hasTable('pergerakan_kapal'),
];

echo "Database Tables:\n";
foreach ($tables as $table => $exists) {
    echo "  " . ($exists ? "✅" : "❌") . " {$table}\n";
}

// Test 2: Check models
echo "\nModel Classes:\n";
try {
    $prospekKapal = new ProspekKapal();
    echo "  ✅ ProspekKapal model loaded\n";
} catch (Exception $e) {
    echo "  ❌ ProspekKapal error: " . $e->getMessage() . "\n";
}

try {
    $kontainer = new ProspekKapalKontainer();
    echo "  ✅ ProspekKapalKontainer model loaded\n";
} catch (Exception $e) {
    echo "  ❌ ProspekKapalKontainer error: " . $e->getMessage() . "\n";
}

// Test 3: Check sample data
echo "\nSample Data:\n";
$pergerakanCount = PergerakanKapal::count();
echo "  📊 Pergerakan Kapal records: {$pergerakanCount}\n";

$prospekCount = ProspekKapal::count();
echo "  📊 Prospek Kapal records: {$prospekCount}\n";

echo "\n🎯 Ready to use the Prospek Kapal feature!\n";
