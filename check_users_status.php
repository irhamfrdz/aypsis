<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking users and permissions tables status...\n\n";

try {
    // Check if tables exist
    $tables = DB::select("SHOW TABLES");
    $databaseName = DB::connection()->getDatabaseName();
    $tableColumn = "Tables_in_" . $databaseName;
    
    $existingTables = [];
    foreach ($tables as $table) {
        $existingTables[] = $table->$tableColumn;
    }
    
    // Check each critical table
    $criticalTables = ['users', 'permissions', 'user_permissions'];
    
    foreach ($criticalTables as $tableName) {
        echo "📋 Table: {$tableName}\n";
        
        if (in_array($tableName, $existingTables)) {
            try {
                $count = DB::table($tableName)->count();
                echo "   ✅ EXISTS with {$count} records\n";
                
                // Show sample data for verification
                if ($count > 0) {
                    if ($tableName == 'users') {
                        $sample = DB::table($tableName)->select('id', 'username')->take(3)->get();
                        echo "   📝 Sample users:\n";
                        foreach ($sample as $user) {
                            echo "      - ID {$user->id}: {$user->username}\n";
                        }
                    } elseif ($tableName == 'permissions') {
                        $sample = DB::table($tableName)->select('id', 'name')->take(3)->get();
                        echo "   📝 Sample permissions:\n";
                        foreach ($sample as $perm) {
                            echo "      - ID {$perm->id}: {$perm->name}\n";
                        }
                    }
                }
            } catch (Exception $e) {
                echo "   ❌ ERROR accessing table: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ❌ TABLE NOT FOUND!\n";
        }
        echo "\n";
    }
    
    // Check if user 'kiky' still exists
    echo "👤 Checking specific user 'kiky':\n";
    try {
        $kiky = DB::table('users')->where('username', 'kiky')->first();
        if ($kiky) {
            echo "   ✅ User kiky still exists (ID: {$kiky->id})\n";
            
            // Check kiky's permissions
            $permCount = DB::table('user_permissions')->where('user_id', $kiky->id)->count();
            echo "   🔑 Kiky has {$permCount} permissions\n";
        } else {
            echo "   ❌ User kiky not found!\n";
        }
    } catch (Exception $e) {
        echo "   ❌ ERROR checking kiky: " . $e->getMessage() . "\n";
    }
    
    // Check what happened during import
    echo "\n🔍 Import Analysis:\n";
    echo "   • The import script was designed to EXCLUDE users/permissions\n";
    echo "   • Error messages showed 'Table not found' for these tables\n";
    echo "   • This suggests the SQL file tried to access them but they were preserved\n";
    
} catch (Exception $e) {
    echo "❌ Critical error: " . $e->getMessage() . "\n";
}

echo "\n✅ Analysis completed!\n";