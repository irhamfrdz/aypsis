<?php

require_once 'vendor/autolo        echo "✅ Created test perbaikan kontainer with ID: {$perbaikan->id}\n";
        echo "   - Estimasi Biaya: " . ($perbaikan->estimasi_biaya_perbaikan ?? 'NULL') . "\n";
        echo "   - Realisasi Biaya: " . ($perbaikan->realisasi_biaya_perbaikan ?? 'NULL') . "\n";
    } else {
        echo "✅ Using existing perbaikan kontainer with ID: {$perbaikan->id}\n";
        echo "   - Estimasi Biaya: " . ($perbaikan->estimasi_biaya_perbaikan ?? 'NULL') . "\n";
        echo "   - Realisasi Biaya: " . ($perbaikan->realisasi_biaya_perbaikan ?? 'NULL') . "\n";
    }

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\PranotaPerbaikanKontainerController;
use App\Models\PerbaikanKontainer;

echo "=== Testing Complete Pranota Popup Submission Flow ===\n\n";

try {
    // Get existing perbaikan kontainer or create a simple one
    $perbaikan = PerbaikanKontainer::first();
    if (!$perbaikan) {
        echo "❌ No existing perbaikan kontainer found. Using simplified test data...\n";

        // Use raw SQL to create a minimal record
        $perbaikanId = DB::table('perbaikan_kontainers')->insertGetId([
            'nomor_kontainer' => 'TEST_POPUP_001',
            'tanggal_perbaikan' => now()->format('Y-m-d'),
            'estimasi_kerusakan_kontainer' => 'Test damage for popup',
            'deskripsi_perbaikan' => 'Test repair description',
            'estimasi_biaya_perbaikan' => 80000,  // Estimasi biaya
            'realisasi_biaya_perbaikan' => 100000, // Realisasi biaya (total biaya)
            'status_perbaikan' => 'belum_masuk_pranota',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $perbaikan = PerbaikanKontainer::find($perbaikanId);
        echo "✅ Created test perbaikan kontainer with ID: {$perbaikan->id}\n";
        echo "   - Estimasi Biaya: {$perbaikan->estimasi_biaya_perbaikan}\n";
        echo "   - Realisasi Biaya: {$perbaikan->realisasi_biaya_perbaikan}\n";
    } else {
        echo "✅ Using existing perbaikan kontainer with ID: {$perbaikan->id}\n";
        echo "   - Estimasi Biaya: {$perbaikan->estimasi_biaya_perbaikan ?? 'NULL'}\n";
        echo "   - Realisasi Biaya: {$perbaikan->realisasi_biaya_perbaikan ?? 'NULL'}\n";
    }

    // Simulate the popup form submission data
    $popupData = [
        'perbaikan_ids' => json_encode([$perbaikan->id]),
        'nomor_pranota' => 'POPUP' . date('ymd') . '0001',
        'tanggal_pranota' => now()->format('Y-m-d'),
        'supplier' => 'Test Vendor Popup',
        'catatan' => 'Test notes from popup',
    ];

    echo "\n📝 Simulating popup form data:\n";
    foreach ($popupData as $key => $value) {
        echo "   - $key: " . ($value ?? 'NULL') . "\n";
    }

    // Create a mock request
    $request = new Request();
    $request->merge($popupData);

    // Mock authenticated user by setting the global auth
    $user = DB::table('users')->first();
    if (!$user) {
        throw new Exception("No users found in database. Please create a user first.");
    }

    // Set the authenticated user using Auth facade
    $userModel = \App\Models\User::find($user->id);
    \Illuminate\Support\Facades\Auth::login($userModel);
    echo "\n✅ Authenticated as user: {$userModel->name} (ID: {$userModel->id})\n";

    // Create controller instance and call store method
    $controller = new PranotaPerbaikanKontainerController();
    echo "\n🔄 Calling controller store method...\n";

    // Capture the response
    ob_start();
    $response = $controller->store($request);
    $output = ob_get_clean();

    echo "Controller response captured\n";

    // Check if pranota was created
    $createdPranota = DB::table('pranota_perbaikan_kontainers')
        ->where('nomor_pranota', $popupData['nomor_pranota'])
        ->first();

    if ($createdPranota) {
        echo "✅ Pranota created successfully!\n";
        echo "   - ID: {$createdPranota->id}\n";
        echo "   - Nomor Pranota: {$createdPranota->nomor_pranota}\n";
        echo "   - Status: {$createdPranota->status}\n";
        echo "   - Total Biaya: {$createdPranota->total_biaya} (type: " . gettype($createdPranota->total_biaya) . ")\n";
        echo "   - Created By: {$createdPranota->created_by}\n";
        echo "   - Updated By: {$createdPranota->updated_by}\n";

        // Check if perbaikan status was updated
        $updatedPerbaikan = PerbaikanKontainer::find($perbaikan->id);
        echo "\n🔄 Perbaikan status update check:\n";
        echo "   - Original status: belum_masuk_pranota\n";
        echo "   - Updated status: {$updatedPerbaikan->status_perbaikan}\n";

        if ($updatedPerbaikan->status_perbaikan === 'sudah_masuk_pranota') {
            echo "✅ Perbaikan status updated correctly\n";
        } else {
            echo "❌ Perbaikan status not updated correctly\n";
        }

        // Clean up
        DB::table('pranota_perbaikan_kontainers')->where('id', $createdPranota->id)->delete();
        echo "\n🧹 Cleaned up test pranota\n";

    } else {
        echo "❌ Pranota was not created!\n";
        echo "Checking for any pranota records...\n";
        $allPranota = DB::table('pranota_perbaikan_kontainers')->get();
        echo "Total pranota records: " . $allPranota->count() . "\n";
    }

    // Clean up test perbaikan
    $perbaikan->delete();
    echo "✅ Cleaned up test perbaikan\n";

    echo "\n🎉 Popup submission test completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
