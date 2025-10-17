<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Status User Admin ===\n";

$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan\n";
    exit;
}

echo "Username: {$admin->username}\n";
echo "Status: " . ($admin->status ?? 'null') . "\n";
echo "is_approved: " . ($admin->is_approved ?? 'null') . "\n";

// Update status to approved if needed
if ($admin->status !== 'approved') {
    echo "\n🔧 Mengupdate status user admin ke 'approved'...\n";
    $admin->status = 'approved';
    $admin->save();
    echo "✅ Status berhasil diupdate\n";
} else {
    echo "✅ Status sudah approved\n";
}

echo "\n=== Selesai ===\n";
