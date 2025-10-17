<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== Create Login Session untuk Test ===\n";

// Get admin user
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan\n";
    exit;
}

echo "User ditemukan: {$admin->username}\n";
echo "Password hash: " . substr($admin->password, 0, 20) . "...\n";

// Test password default
$defaultPasswords = ['admin', 'password', '123456', 'admin123'];

echo "\n🔧 Testing password default...\n";
foreach ($defaultPasswords as $pwd) {
    if (Hash::check($pwd, $admin->password)) {
        echo "✅ Password ditemukan: {$pwd}\n";
        echo "\n=== LOGIN CREDENTIALS ===\n";
        echo "Username: admin\n";
        echo "Password: {$pwd}\n";
        echo "URL Login: http://127.0.0.1:8000/login\n";
        echo "URL Approval: http://127.0.0.1:8000/approval/surat-jalan\n";
        echo "=== ================== ===\n";
        exit;
    }
}

echo "❌ Password default tidak ditemukan\n";
echo "Hash password admin: {$admin->password}\n";

echo "\n=== Test Selesai ===\n";
