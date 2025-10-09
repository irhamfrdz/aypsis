<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Cek struktur tabel users
    $userColumns = DB::select("DESCRIBE users");
    echo "Users table structure:\n";
    foreach ($userColumns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    echo "\n";

    // Cek tabel apa saja yang ada
    $tables = DB::select("SHOW TABLES");
    echo "Available tables:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- {$tableName}\n";
    }
    echo "\n";

    // Cek user permission table structure jika ada
    try {
        $userPermColumns = DB::select("DESCRIBE user_permissions");
        echo "User_permissions table structure:\n";
        foreach ($userPermColumns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "User_permissions table tidak ada atau error: " . $e->getMessage() . "\n\n";
    }

    // Lihat beberapa user yang ada
    $users = DB::table('users')->select('id', 'name', 'email')->limit(10)->get();
    echo "Sample users:\n";
    foreach ($users as $user) {
        echo "- ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
