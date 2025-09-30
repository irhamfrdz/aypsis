<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate authenticated request
$user = \App\Models\User::where('username', 'admin')->first();

if (!$user) {
    echo "Admin user not found\n";
    exit(1);
}

echo "Testing API endpoint with authenticated user: {$user->username}\n\n";

// Create a request instance
$request = \Illuminate\Http\Request::create('/pranota-cat/generate-nomor', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Create controller and call method
$controller = new \App\Http\Controllers\PranotaTagihanCatController();

// Check if user can access
if (!$user->can('pranota-cat-create')) {
    echo "❌ User does not have pranota-cat-create permission\n";
    exit(1);
}

echo "✅ User has pranota-cat-create permission\n";

// Call the method
try {
    $response = $controller->generateNomor();
    $data = json_decode($response->getContent(), true);

    echo "API Response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

    if (isset($data['success']) && $data['success']) {
        $nomorPranota = $data['nomor_pranota'];
        echo "✅ Generated nomor pranota: $nomorPranota\n";

        // Check format
        if (preg_match('/^PMS(\d)(\d{2})(\d{2})(\d{6})$/', $nomorPranota, $matches)) {
            echo "✅ Format breakdown:\n";
            echo "   - Modul: PMS\n";
            echo "   - Cetakan: {$matches[1]}\n";
            echo "   - Bulan: {$matches[2]}\n";
            echo "   - Tahun: {$matches[3]}\n";
            echo "   - Running Number: {$matches[4]}\n";

            if ($matches[4] === '000004') {
                echo "\n🎉 SUCCESS: Nomor pranota menunjukkan 000004 sesuai harapan!\n";
            } else {
                echo "\n❌ FAIL: Nomor pranota menunjukkan {$matches[4]}, expected 000004\n";
            }
        } else {
            echo "❌ FAIL: Format nomor pranota tidak sesuai\n";
        }
    } else {
        echo "❌ FAIL: API returned error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== END TEST ===\n";
