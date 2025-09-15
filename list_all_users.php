<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LIST ALL USERS ===\n\n";

$users = User::all();

if ($users->isEmpty()) {
    echo "❌ Tidak ada user ditemukan\n";
    exit(1);
}

echo "Ditemukan " . $users->count() . " user(s):\n\n";
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Status: {$user->status}\n";
    echo "Created: {$user->created_at}\n";
    echo "---\n";
}
