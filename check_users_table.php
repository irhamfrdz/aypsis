<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CEK STRUKTUR TABEL USERS ===\n\n";

try {
    if (Schema::hasTable('users')) {
        echo "✅ Tabel users ditemukan\n\n";
        
        $columns = DB::select("SHOW COLUMNS FROM users");
        echo "📋 Kolom yang tersedia:\n";
        foreach ($columns as $column) {
            echo "   - {$column->Field} ({$column->Type}) " . 
                 ($column->Null == 'YES' ? 'NULL' : 'NOT NULL') . 
                 ($column->Key ? " [{$column->Key}]" : '') . "\n";
        }
        
        echo "\n📊 Sample data:\n";
        $sampleUsers = DB::table('users')->limit(3)->get();
        foreach ($sampleUsers as $user) {
            echo "   - ID: {$user->id}, Username: " . ($user->username ?? 'N/A') . ", Name: " . ($user->name ?? 'N/A') . "\n";
        }
        
    } else {
        echo "❌ Tabel users tidak ditemukan!\n";
    }
    
} catch (\Exception $e) {
    echo "💥 Error: " . $e->getMessage() . "\n";
}