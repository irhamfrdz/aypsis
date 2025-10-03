<?php
/**
 * Script untuk memperbaiki permission dengan multiple dots
 * Mengubah format master.karyawan.import.store menjadi master.karyawan.import
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING MULTI-DOT PERMISSIONS ===\n\n";

try {
    // 1. Find all permissions with multiple dots (more than 2)
    echo "🔍 ANALYZING MULTI-DOT PERMISSIONS:\n";
    $multiDotPermissions = DB::table('permissions')
        ->whereRaw("LENGTH(name) - LENGTH(REPLACE(name, '.', '')) > 2")
        ->get();
    
    if ($multiDotPermissions->count() === 0) {
        echo "✅ No multi-dot permissions found.\n";
        exit(0);
    }
    
    echo "Found " . $multiDotPermissions->count() . " permissions with multiple dots:\n";
    foreach ($multiDotPermissions as $perm) {
        echo "  - ID: {$perm->id}, Name: {$perm->name}\n";
    }
    echo "\n";

    // 2. Analyze and propose fixes
    echo "💡 PROPOSED FIXES:\n";
    $fixes = [];
    
    foreach ($multiDotPermissions as $perm) {
        $parts = explode('.', $perm->name);
        
        if (count($parts) == 4) {
            // Handle 4-part permissions like master.karyawan.import.store
            $base = $parts[0]; // master
            $module = $parts[1]; // karyawan
            $action = $parts[2]; // import
            $subaction = $parts[3]; // store
            
            // Determine the correct simplified name
            if ($action === 'import' && $subaction === 'store') {
                $newName = "{$base}.{$module}.import";
                $description = "Import " . ucfirst($module);
            } elseif ($action === 'print' && $subaction === 'single') {
                $newName = "{$base}.{$module}.print";
                $description = "Print " . ucfirst($module);
            } else {
                // Generic case: combine action and subaction
                $newName = "{$base}.{$module}.{$action}_{$subaction}";
                $description = ucfirst($action) . " " . ucfirst($subaction) . " " . ucfirst($module);
            }
            
            $fixes[] = [
                'id' => $perm->id,
                'old_name' => $perm->name,
                'new_name' => $newName,
                'description' => $description
            ];
            
            echo "  {$perm->name} → {$newName}\n";
        }
    }
    echo "\n";

    // 3. Check if new names already exist
    echo "🔍 CHECKING FOR NAME CONFLICTS:\n";
    $conflicts = [];
    foreach ($fixes as $fix) {
        $existing = DB::table('permissions')->where('name', $fix['new_name'])->first();
        if ($existing) {
            $conflicts[] = $fix;
            echo "  ⚠️  Conflict: {$fix['new_name']} already exists (ID: {$existing->id})\n";
        }
    }
    
    if (empty($conflicts)) {
        echo "  ✅ No naming conflicts found.\n";
    }
    echo "\n";

    // 4. Apply fixes
    echo "🔧 APPLYING FIXES:\n";
    $fixedCount = 0;
    
    DB::beginTransaction();
    
    try {
        foreach ($fixes as $fix) {
            // Skip if there's a conflict
            if (in_array($fix, $conflicts)) {
                echo "  ⏭️  Skipping {$fix['old_name']} due to conflict\n";
                continue;
            }
            
            // Update the permission name
            DB::table('permissions')
                ->where('id', $fix['id'])
                ->update([
                    'name' => $fix['new_name'],
                    'description' => $fix['description'],
                    'updated_at' => now()
                ]);
            
            echo "  ✅ Updated: {$fix['old_name']} → {$fix['new_name']}\n";
            $fixedCount++;
        }
        
        DB::commit();
        echo "\n🎉 Successfully updated {$fixedCount} permissions!\n";
        
    } catch (Exception $e) {
        DB::rollback();
        echo "❌ Error during update: " . $e->getMessage() . "\n";
        echo "All changes have been rolled back.\n";
    }

    // 5. Handle conflicts if any
    if (!empty($conflicts)) {
        echo "\n🤔 HANDLING CONFLICTS:\n";
        echo "For conflicting permissions, you have these options:\n\n";
        
        foreach ($conflicts as $conflict) {
            $existing = DB::table('permissions')->where('name', $conflict['new_name'])->first();
            
            echo "Conflict: {$conflict['old_name']} → {$conflict['new_name']}\n";
            echo "  Option 1: Delete the old permission (ID: {$conflict['id']})\n";
            echo "  Option 2: Rename to alternative like {$conflict['new_name']}_detailed\n";
            echo "  Option 3: Merge user assignments to existing permission (ID: {$existing->id})\n";
            echo "\n";
            
            // Show which users have the conflicting permissions
            $usersWithOld = DB::table('user_permissions')
                ->join('users', 'user_permissions.user_id', '=', 'users.id')
                ->where('user_permissions.permission_id', $conflict['id'])
                ->select('users.id', 'users.username')
                ->get();
                
            $usersWithNew = DB::table('user_permissions')
                ->join('users', 'user_permissions.user_id', '=', 'users.id')
                ->where('user_permissions.permission_id', $existing->id)
                ->select('users.id', 'users.username')
                ->get();
            
            if ($usersWithOld->count() > 0) {
                echo "  Users with old permission ({$conflict['old_name']}):\n";
                foreach ($usersWithOld as $user) {
                    echo "    - {$user->username} (ID: {$user->id})\n";
                }
            }
            
            if ($usersWithNew->count() > 0) {
                echo "  Users with new permission ({$conflict['new_name']}):\n";
                foreach ($usersWithNew as $user) {
                    echo "    - {$user->username} (ID: {$user->id})\n";
                }
            }
            echo "\n";
        }
        
        // Generate conflict resolution script
        echo "📝 CONFLICT RESOLUTION COMMANDS:\n";
        echo "-- Run these SQL commands to resolve conflicts:\n\n";
        
        foreach ($conflicts as $conflict) {
            $existing = DB::table('permissions')->where('name', $conflict['new_name'])->first();
            
            echo "-- For permission: {$conflict['old_name']}\n";
            echo "-- Option A: Merge users to existing permission and delete old\n";
            echo "INSERT IGNORE INTO user_permissions (user_id, permission_id)\n";
            echo "SELECT user_id, {$existing->id} FROM user_permissions WHERE permission_id = {$conflict['id']};\n";
            echo "DELETE FROM user_permissions WHERE permission_id = {$conflict['id']};\n";
            echo "DELETE FROM permissions WHERE id = {$conflict['id']};\n\n";
            
            echo "-- Option B: Rename old permission to alternative name\n";
            echo "UPDATE permissions SET name = '{$conflict['new_name']}_legacy' WHERE id = {$conflict['id']};\n\n";
        }
    }

    // 6. Final verification
    echo "🔍 FINAL VERIFICATION:\n";
    $remainingMultiDot = DB::table('permissions')
        ->whereRaw("LENGTH(name) - LENGTH(REPLACE(name, '.', '')) > 2")
        ->count();
    
    if ($remainingMultiDot === 0) {
        echo "✅ All multi-dot permissions have been resolved!\n";
    } else {
        echo "⚠️  {$remainingMultiDot} multi-dot permissions still remain (likely due to conflicts)\n";
    }

} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "📝 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== MULTI-DOT PERMISSION FIX COMPLETE ===\n";