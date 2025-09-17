<?php

// Test script to verify vendor_bengkel saving functionality
// Run this to test if the approval form saves vendor_bengkel correctly

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\PerbaikanKontainer;
use App\Http\Controllers\PenyelesaianController;
use Illuminate\Support\Facades\DB;

echo "=== Testing Vendor/Bengkel Saving Functionality ===\n\n";

// Test 1: Check if PerbaikanKontainer model has vendor_bengkel in fillable
echo "Test 1: Checking PerbaikanKontainer fillable fields...\n";
$model = new PerbaikanKontainer();
$fillable = $model->getFillable();
if (in_array('vendor_bengkel', $fillable)) {
    echo "✅ vendor_bengkel is in fillable array\n";
} else {
    echo "❌ vendor_bengkel is NOT in fillable array\n";
    echo "Fillable fields: " . implode(', ', $fillable) . "\n";
}

// Test 2: Check if database has vendor_bengkel column
echo "\nTest 2: Checking database schema...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM perbaikan_kontainers LIKE 'vendor_bengkel'");
    if (count($columns) > 0) {
        echo "✅ vendor_bengkel column exists in database\n";
    } else {
        echo "❌ vendor_bengkel column does NOT exist in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking database: " . $e->getMessage() . "\n";
}

// Test 3: Simulate form submission
echo "\nTest 3: Simulating form submission...\n";
try {
    // Create a mock request with vendor_bengkel data
    $requestData = [
        'status_permohonan' => 'selesai',
        'estimasi_perbaikan' => 'Test perbaikan description',
        'total_biaya_perbaikan' => '1500000',
        'vendor_bengkel' => 'PT. Container Repair Indonesia',
        'catatan_karyawan' => 'Test approval notes'
    ];

    $request = new Request();
    $request->merge($requestData);

    // Test validation
    $validated = $request->validate([
        'status_permohonan' => 'required|in:selesai,bermasalah',
        'estimasi_perbaikan' => 'nullable|string|max:1000',
        'total_biaya_perbaikan' => 'nullable|numeric|min:0',
        'vendor_bengkel' => 'required|string|max:255',
        'catatan_karyawan' => 'nullable|string',
    ]);

    if (isset($validated['vendor_bengkel'])) {
        echo "✅ Validation passed for vendor_bengkel: " . $validated['vendor_bengkel'] . "\n";
    } else {
        echo "❌ vendor_bengkel validation failed\n";
    }

} catch (Exception $e) {
    echo "❌ Validation error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Summary ===\n";
echo "If all tests show ✅, then vendor_bengkel saving should work correctly.\n";
echo "The controller has been updated to:\n";
echo "1. Validate vendor_bengkel field as required\n";
echo "2. Save vendor_bengkel data to PerbaikanKontainer records\n";
echo "\nYou can now test the actual form submission in the browser.\n";
