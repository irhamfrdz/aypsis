<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING USER STATUS ===" . PHP_EOL;

$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    echo 'User: ' . $user->username . ' (ID: ' . $user->id . ')' . PHP_EOL;
    echo 'Status: ' . ($user->status ?: 'NULL/EMPTY') . PHP_EOL;

    if ($user->status !== 'approved') {
        echo PHP_EOL . '❌ PROBLEM FOUND: User status bukan "approved"!' . PHP_EOL;
        echo 'Middleware EnsureUserApproved akan memblokir akses.' . PHP_EOL;
        echo 'Current status: ' . ($user->status ?: 'NULL') . PHP_EOL;
        echo PHP_EOL . 'SOLUSI: Set status = "approved" untuk user admin.' . PHP_EOL;
    } else {
        echo '✅ Status OK: approved' . PHP_EOL;
    }
} else {
    echo '❌ User admin tidak ditemukan' . PHP_EOL;
}

echo PHP_EOL . "=== END CHECK ===" . PHP_EOL;
