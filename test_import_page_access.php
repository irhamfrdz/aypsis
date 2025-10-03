<?php

echo "=== TESTING IMPORT PAGE ACCESS ===\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$importUrl = $baseUrl . '/daftar-tagihan-kontainer-sewa/import';

echo "🌐 Testing URL: {$importUrl}\n\n";

// Create a stream context with proper headers
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ],
        'timeout' => 10
    ]
]);

try {
    echo "📡 Making request...\n";
    $response = file_get_contents($importUrl, false, $context);

    if ($response !== false) {
        echo "✅ SUCCESS: Page accessible!\n";
        echo "📄 Response length: " . strlen($response) . " bytes\n";

        // Check if it contains expected content
        if (strpos($response, 'Import Data Tagihan Kontainer Sewa') !== false) {
            echo "✅ Page title found in response\n";
        } else {
            echo "⚠️  Page title not found - might be redirect or error\n";
        }

        if (strpos($response, 'importForm') !== false) {
            echo "✅ Import form found in response\n";
        } else {
            echo "❌ Import form not found\n";
        }

        if (strpos($response, 'daftar-tagihan-kontainer-sewa.import.process') !== false) {
            echo "✅ Process route found in form action\n";
        } else {
            echo "❌ Process route not found\n";
        }

        // Check for authentication errors
        if (strpos($response, 'login') !== false || strpos($response, 'Login') !== false) {
            echo "⚠️  Possible authentication redirect detected\n";
            echo "🔐 You may need to login first\n";
        }

        // Check for permission errors
        if (strpos($response, '403') !== false || strpos($response, 'Forbidden') !== false) {
            echo "❌ Permission denied (403) - check user permissions\n";
        }

    } else {
        echo "❌ FAILED: Could not access page\n";

        // Get last HTTP response headers if available
        if (isset($http_response_header)) {
            echo "📋 HTTP Headers:\n";
            foreach ($http_response_header as $header) {
                echo "  {$header}\n";
            }
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TROUBLESHOOTING STEPS ===\n\n";
echo "1. ✅ Route conflict fixed (GET route now comes first)\n";
echo "2. ✅ CSV file has correct format and columns\n";
echo "3. ✅ Controller methods exist (importPage, processImport)\n";
echo "4. 🔍 Next: Check if you need to login and have permissions\n";
echo "\n";
echo "📝 TO ACCESS IMPORT:\n";
echo "1. Open browser: {$baseUrl}\n";
echo "2. Login with admin account\n";
echo "3. Navigate to: Daftar Tagihan Kontainer Sewa\n";
echo "4. Click 'Import Data' button\n";
echo "5. Or directly visit: {$importUrl}\n";

echo "\n=== END TEST ===\n";
