<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Adding Prospek Edit Permission ===\n";

try {
    // Buat permission prospek-edit jika belum ada
    $prospekEditPermission = DB::table('permissions')
        ->where('name', 'prospek-edit')
        ->first();
        
    if (!$prospekEditPermission) {
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'prospek-edit',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Permission 'prospek-edit' created with ID: {$permissionId}\n";
    } else {
        $permissionId = $prospekEditPermission->id;
        echo "✅ Permission 'prospek-edit' already exists with ID: {$permissionId}\n";
    }

    // Ambil ID permission prospek-view
    $prospekViewPermission = DB::table('permissions')
        ->where('name', 'prospek-view')
        ->first();
        
    if (!$prospekViewPermission) {
        echo "❌ Permission 'prospek-view' tidak ditemukan!\n";
        exit;
    }
    
    // Ambil semua user yang memiliki prospek-view
    $usersWithProspekView = DB::table('user_permissions')
        ->where('permission_id', $prospekViewPermission->id)
        ->get();
    
    $addedCount = 0;
    
    echo "Found " . count($usersWithProspekView) . " users with prospek-view permission\n";
    
    foreach ($usersWithProspekView as $userPermission) {
        // Cek apakah user sudah memiliki prospek-edit
        $existingEditPermission = DB::table('user_permissions')
            ->where('user_id', $userPermission->user_id)
            ->where('permission_id', $permissionId)
            ->first();
            
        if (!$existingEditPermission) {
            // Tambahkan permission prospek-edit
            DB::table('user_permissions')->insert([
                'user_id' => $userPermission->user_id,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $addedCount++;
            echo "✅ Added prospek-edit permission for user ID: {$userPermission->user_id}\n";
        } else {
            echo "ℹ️ User ID {$userPermission->user_id} already has prospek-edit permission\n";
        }
    }
    
    echo "\n📊 Summary:\n";
    echo "- Total permissions added: {$addedCount}\n";
    echo "- Total users with prospek-view: " . count($usersWithProspekView) . "\n";
    echo "- Current users with prospek-edit: " . DB::table('user_permissions')->where('permission_id', $permissionId)->count() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ Selesai!\n";