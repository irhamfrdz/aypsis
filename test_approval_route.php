<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Route Approval Surat Jalan..." . PHP_EOL;

// Test 1: Cek apakah controller class exists
if (class_exists('\App\Http\Controllers\SuratJalanApprovalController')) {
    echo "✓ SuratJalanApprovalController exists" . PHP_EOL;
} else {
    echo "✗ SuratJalanApprovalController NOT found" . PHP_EOL;
}

// Test 2: Cek apakah model exists
if (class_exists('\App\Models\SuratJalan')) {
    echo "✓ SuratJalan model exists" . PHP_EOL;
} else {
    echo "✗ SuratJalan model NOT found" . PHP_EOL;
}

if (class_exists('\App\Models\SuratJalanApproval')) {
    echo "✓ SuratJalanApproval model exists" . PHP_EOL;
} else {
    echo "✗ SuratJalanApproval model NOT found" . PHP_EOL;
}

// Test 3: Cek apakah view exists
$viewPath = 'resources/views/approval/surat-jalan/index.blade.php';
if (file_exists($viewPath)) {
    echo "✓ View file exists: $viewPath" . PHP_EOL;
} else {
    echo "✗ View file NOT found: $viewPath" . PHP_EOL;
}

// Test 4: Cek method pada controller
try {
    $controller = new \App\Http\Controllers\SuratJalanApprovalController();
    if (method_exists($controller, 'index')) {
        echo "✓ Controller index method exists" . PHP_EOL;
    } else {
        echo "✗ Controller index method NOT found" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "✗ Error creating controller: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Testing complete." . PHP_EOL;
