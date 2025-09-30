<?php

echo "=== TEST API ENDPOINT /pranota-cat/generate-nomor ===\n\n";

try {
    // Test API endpoint
    $url = 'http://localhost:8000/pranota-cat/generate-nomor';

    // Use file_get_contents for simple GET request
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Accept: application/json'
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        echo "❌ Gagal mengakses API endpoint\n";
        echo "Pastikan Laravel server sedang berjalan dengan: php artisan serve\n";
        exit(1);
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ Response bukan JSON valid: $response\n";
        exit(1);
    }

    echo "✅ API Response berhasil:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

    if (isset($data['success']) && $data['success'] === true) {
        $nomorPranota = $data['nomor_pranota'];
        echo "📋 Analisis Nomor Pranota: $nomorPranota\n";

        // Breakdown format
        if (preg_match('/^PMS(\d)(\d{2})(\d{2})(\d{6})$/', $nomorPranota, $matches)) {
            echo "   - Modul: PMS\n";
            echo "   - Cetakan: {$matches[1]}\n";
            echo "   - Bulan: {$matches[2]}\n";
            echo "   - Tahun: {$matches[3]}\n";
            echo "   - Nomor Terakhir: {$matches[4]}\n";
            echo "✅ Format sesuai spesifikasi!\n";
        } else {
            echo "❌ Format tidak sesuai pola PMS{cetakan}{bulan}{tahun}{nomor_terakhir}\n";
        }
    } else {
        echo "❌ API mengembalikan error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
