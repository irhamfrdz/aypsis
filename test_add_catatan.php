<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\PerbaikanKontainer;
use App\Models\TagihanCat;
use Illuminate\Support\Facades\Auth;

// Simulate a request
$request = new Request();
$request->merge([
    'perbaikan_id' => 7, // Use an existing ID
    'nomor_tagihan_cat' => 'TEST-001',
    'status_perbaikan' => 'cat_sebagian',
    'teknisi' => 'Test Vendor',
    'catatan' => 'Test catatan',
    'tanggal_catatan' => '2025-09-22',
    'estimasi_biaya_cat' => '1.000.000',
    'estimasi_biaya_cat_numeric' => 1000000,
    'nomor_kontainer' => 'TEST123',
]);

// Simulate validation
$validated = $request->validate([
    'perbaikan_id' => 'required|integer|exists:perbaikan_kontainers,id',
    'nomor_tagihan_cat' => 'required|string|max:255',
    'status_perbaikan' => 'required|in:cat_sebagian,cat_full',
    'teknisi' => 'nullable|string|max:255',
    'catatan' => 'required|string',
    'tanggal_catatan' => 'required|date',
    'estimasi_biaya_cat' => 'nullable|string',
    'estimasi_biaya_cat_numeric' => 'nullable|numeric',
    'nomor_kontainer' => 'nullable|string|max:255',
]);

echo "Validation passed!\n";

$perbaikanKontainer = PerbaikanKontainer::findOrFail($request->perbaikan_id);

echo "Found perbaikan kontainer: " . $perbaikanKontainer->id . "\n";

// Update perbaikan kontainer dengan catatan
$updateData = [
    'catatan' => $request->catatan,
    'teknisi' => $request->teknisi,
    'tanggal_catatan' => $request->tanggal_catatan,
    'tanggal_cat' => $request->tanggal_catatan, // Simpan juga ke kolom tanggal_cat
    'updated_by' => 1, // Simulate user ID
];

echo "Update data: " . json_encode($updateData) . "\n";

$result = $perbaikanKontainer->update($updateData);

echo "Update result: " . ($result ? 'success' : 'failed') . "\n";

// Check the updated record
$updated = PerbaikanKontainer::find($request->perbaikan_id);
echo "Updated tanggal_cat: " . $updated->tanggal_cat . "\n";
echo "Updated tanggal_catatan: " . $updated->tanggal_catatan . "\n";

echo "Test completed!\n";
