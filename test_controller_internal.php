<?php

require_once __DIR__ . '/vendor/autoload.php';

echo "=== TEST INTERNAL CONTROLLER METHOD ===\n\n";

try {
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Create controller instance
    $controller = new \App\Http\Controllers\PranotaTagihanCatController();

    // Call generateNomor method directly
    $response = $controller->generateNomor();

    // Get response content
    $data = json_decode($response->getContent(), true);

    echo "✅ Controller Response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

    if (isset($data['success']) && $data['success'] === true) {
        $nomorPranota = $data['nomor_pranota'];
        $nextNumber = $data['next_number'];

        echo "📋 Analisis Nomor Pranota: $nomorPranota\n";
        echo "📋 Next Number: $nextNumber\n\n";

        // Breakdown format
        if (preg_match('/^PMS(\d)(\d{2})(\d{2})(\d{6})$/', $nomorPranota, $matches)) {
            echo "   ✅ Modul: PMS\n";
            echo "   ✅ Cetakan: {$matches[1]}\n";
            echo "   ✅ Bulan: {$matches[2]}\n";
            echo "   ✅ Tahun: {$matches[3]}\n";
            echo "   ✅ Nomor Terakhir: {$matches[4]}\n";
            echo "\n🎉 FORMAT SESUAI SPESIFIKASI!\n";
        } else {
            echo "❌ Format tidak sesuai pola PMS{cetakan}{bulan}{tahun}{nomor_terakhir}\n";
        }

        // Verify next number matches the running number
        $currentRunningNumber = (int) $matches[4]; // 000004 -> 4
        if ($nextNumber == $currentRunningNumber) {
            echo "✅ Next number calculation benar: $nextNumber\n";
        } else {
            echo "❌ Next number salah. Expected: $currentRunningNumber, Got: $nextNumber\n";
        }

    } else {
        echo "❌ Controller mengembalikan error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }

    // Check current nomor_terakhir in database
    echo "\n📊 Status Database:\n";
    $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'PMS')->first();
    if ($nomorTerakhir) {
        echo "   - Modul PMS nomor_terakhir saat ini: {$nomorTerakhir->nomor_terakhir}\n";
        echo "   - Setelah generate, next number akan menjadi: " . ($nomorTerakhir->nomor_terakhir + 1) . "\n";
    } else {
        echo "❌ Modul PMS tidak ditemukan di database!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== SELESAI ===\n";
