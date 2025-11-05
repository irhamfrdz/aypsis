<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$app = app();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING DATABASE FOR KARYAWAN/SUPIR TABLES ===\n\n";

try {
    // Get all tables in database
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    
    echo "📋 ALL TABLES IN DATABASE:\n";
    echo "==========================\n";
    $allTableNames = [];
    foreach($tables as $table) {
        foreach($table as $key => $value) {
            $allTableNames[] = $value;
            echo "- $value\n";
        }
    }
    
    echo "\n🔍 SEARCHING FOR KARYAWAN/SUPIR RELATED TABLES:\n";
    echo "===============================================\n";
    
    $karyawanTables = [];
    foreach($allTableNames as $tableName) {
        if(stripos($tableName, 'karyawan') !== false || 
           stripos($tableName, 'supir') !== false ||
           stripos($tableName, 'driver') !== false ||
           stripos($tableName, 'employee') !== false) {
            $karyawanTables[] = $tableName;
            echo "✅ Found: $tableName\n";
        }
    }
    
    if(empty($karyawanTables)) {
        echo "❌ No karyawan/supir tables found directly.\n";
        echo "\n🔍 Checking for tables that might contain employee/driver data:\n";
        
        $possibleTables = [];
        foreach($allTableNames as $tableName) {
            if(stripos($tableName, 'master') !== false ||
               stripos($tableName, 'user') !== false ||
               stripos($tableName, 'staff') !== false ||
               stripos($tableName, 'person') !== false) {
                $possibleTables[] = $tableName;
                echo "🔍 Possible: $tableName\n";
            }
        }
        
        // Check if there are columns with 'divisi' or 'position' in these tables
        echo "\n🔍 Checking for divisi/position columns in possible tables:\n";
        foreach($possibleTables as $table) {
            try {
                $columns = \Illuminate\Support\Facades\DB::select("DESCRIBE $table");
                echo "\n📊 Columns in $table:\n";
                foreach($columns as $column) {
                    echo "   - {$column->Field} ({$column->Type})\n";
                    if(stripos($column->Field, 'divisi') !== false ||
                       stripos($column->Field, 'position') !== false ||
                       stripos($column->Field, 'department') !== false ||
                       stripos($column->Field, 'role') !== false) {
                        echo "      ⭐ POTENTIAL MATCH!\n";
                    }
                }
            } catch(\Exception $e) {
                echo "   ❌ Error reading $table: {$e->getMessage()}\n";
            }
        }
    } else {
        // Show structure of found tables
        echo "\n📊 STRUCTURE OF FOUND TABLES:\n";
        echo "=============================\n";
        
        foreach($karyawanTables as $table) {
            try {
                echo "\n🏗️  Table: $table\n";
                echo "------------------------\n";
                $columns = \Illuminate\Support\Facades\DB::select("DESCRIBE $table");
                foreach($columns as $column) {
                    echo "   - {$column->Field} ({$column->Type})\n";
                }
                
                // Show sample data
                echo "\n📄 Sample data (first 3 rows):\n";
                $sampleData = \Illuminate\Support\Facades\DB::table($table)->limit(3)->get();
                foreach($sampleData as $index => $row) {
                    echo "   Row " . ($index + 1) . ": ";
                    $rowData = (array) $row;
                    $displayData = [];
                    foreach($rowData as $key => $value) {
                        $displayData[] = "$key: " . (is_null($value) ? 'NULL' : $value);
                    }
                    echo implode(', ', array_slice($displayData, 0, 3)) . "...\n";
                }
                
            } catch(\Exception $e) {
                echo "   ❌ Error reading $table: {$e->getMessage()}\n";
            }
        }
    }
    
    echo "\n🔍 CHECKING FOR SUPIR DATA IN PROSPEK TABLE:\n";
    echo "==========================================\n";
    
    try {
        $prospekColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE prospeks");
        echo "📊 Prospek table columns:\n";
        foreach($prospekColumns as $column) {
            echo "   - {$column->Field} ({$column->Type})\n";
            if(stripos($column->Field, 'supir') !== false ||
               stripos($column->Field, 'driver') !== false) {
                echo "      ⭐ SUPIR FIELD FOUND!\n";
            }
        }
        
        // Check unique supir names in prospek
        echo "\n👥 Unique supir names in prospek table:\n";
        $supirNames = \Illuminate\Support\Facades\DB::table('prospeks')
            ->select('nama_supir')
            ->distinct()
            ->whereNotNull('nama_supir')
            ->where('nama_supir', '!=', '')
            ->limit(10)
            ->get();
            
        foreach($supirNames as $supir) {
            echo "   - {$supir->nama_supir}\n";
        }
        
    } catch(\Exception $e) {
        echo "❌ Error checking prospek table: {$e->getMessage()}\n";
    }
    
} catch(\Exception $e) {
    echo "❌ Database connection error: {$e->getMessage()}\n";
}

echo "\n=== SEARCH COMPLETE ===\n";

?>