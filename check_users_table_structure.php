<?php
/**
 * Script untuk memeriksa struktur tabel users
 */

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STRUKTUR TABEL USERS ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Cek kolom yang ada di tabel users
    $columns = DB::select("SHOW COLUMNS FROM users");
    
    echo "Kolom yang tersedia di tabel users:\n";
    foreach ($columns as $column) {
        echo sprintf(
            "- %s (%s) %s %s %s\n",
            $column->Field,
            $column->Type,
            $column->Null === 'YES' ? 'NULL' : 'NOT NULL',
            $column->Key ? "KEY: {$column->Key}" : '',
            $column->Default ? "DEFAULT: {$column->Default}" : ''
        );
    }
    
    echo "\n=== SAMPLE DATA USERS ===\n";
    
    // Ambil sample data dari tabel users
    $users = DB::table('users')
        ->limit(5)
        ->get();
    
    if ($users->isEmpty()) {
        echo "Tidak ada data user.\n";
    } else {
        echo "Sample 5 user pertama:\n";
        foreach ($users as $user) {
            $userArray = (array) $user;
            echo "User ID {$user->id}:\n";
            foreach ($userArray as $field => $value) {
                echo "  {$field}: " . ($value ?? 'NULL') . "\n";
            }
            echo "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "=== SELESAI ===\n";