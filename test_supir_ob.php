<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$app = app();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING SUPIR OB IMPLEMENTATION ===\n\n";

try {
    // Test 1: Check karyawan model and data
    echo "🔍 Test 1: Checking Karyawan model\n";
    echo "================================\n";
    
    $supirOB = \App\Models\Karyawan::where('divisi', 'SUPIR')
        ->whereIn('status', ['aktif', 'active'])
        ->orderBy('nama_lengkap', 'asc')
        ->get();
    
    echo "Found " . $supirOB->count() . " supir karyawan:\n";
    foreach($supirOB->take(5) as $supir) {
        echo "ID: {$supir->id} | Nama: {$supir->nama_lengkap} | Panggilan: {$supir->nama_panggilan}\n";
    }
    
    // Test 2: Check prospek table structure for supir_ob field
    echo "\n🔍 Test 2: Checking prospek table structure\n";
    echo "==========================================\n";
    
    $prospekColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE prospek");
    $hasSupirObField = false;
    
    foreach($prospekColumns as $column) {
        if($column->Field === 'supir_ob') {
            $hasSupirObField = true;
            echo "✅ supir_ob field found: {$column->Field} ({$column->Type})\n";
            break;
        }
    }
    
    if(!$hasSupirObField) {
        echo "❌ supir_ob field not found in prospek table\n";
        echo "📝 Available fields with 'supir' in name:\n";
        foreach($prospekColumns as $column) {
            if(stripos($column->Field, 'supir') !== false) {
                echo "   - {$column->Field} ({$column->Type})\n";
            }
        }
    }
    
    // Test 3: Check if we can create a sample dropdown
    echo "\n🔍 Test 3: Generate sample dropdown HTML\n";
    echo "=======================================\n";
    
    echo "<select name=\"supir_ob_test\">\n";
    echo "    <option value=\"\">--Pilih Supir OB--</option>\n";
    foreach($supirOB->take(3) as $supir) {
        echo "    <option value=\"{$supir->id}\">{$supir->nama_lengkap} ({$supir->nama_panggilan})</option>\n";
    }
    echo "</select>\n";
    
    echo "\n✅ All tests completed successfully!\n";
    
} catch(\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Stack trace: {$e->getTraceAsString()}\n";
}

echo "\n=== TEST COMPLETE ===\n";

?>