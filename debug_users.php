<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use App\Models\User;

echo "🔍 Memeriksa Struktur Tabel Users\n";
echo "=================================\n\n";

try {
    $columns = Schema::getColumnListing('users');
    echo "📋 Kolom yang ada di tabel users:\n";
    foreach ($columns as $column) {
        echo "   - {$column}\n";
    }
    echo "\n";

    echo "👥 Daftar user yang ada:\n";
    $users = User::all();
    if ($users->count() > 0) {
        foreach ($users as $user) {
            echo "   - ID: {$user->id}\n";
            echo "     Email: {$user->email}\n";
            if (isset($user->name)) {
                echo "     Name: {$user->name}\n";
            }
            if (isset($user->username)) {
                echo "     Username: {$user->username}\n";
            }
            echo "\n";
        }
    } else {
        echo "   ❌ Tidak ada user di database\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
