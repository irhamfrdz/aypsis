<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$app = app();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING KARYAWAN DIVISI SUPIR ===\n\n";

try {
    // Check divisi values in karyawans table
    echo "🔍 Available divisi in karyawans table:\n";
    echo "===================================\n";
    
    $divisis = \Illuminate\Support\Facades\DB::table('karyawans')
        ->select('divisi')
        ->distinct()
        ->whereNotNull('divisi')
        ->where('divisi', '!=', '')
        ->get();
    
    foreach($divisis as $divisi) {
        echo "- {$divisi->divisi}\n";
    }
    
    echo "\n👥 Karyawan with 'supir' in divisi:\n";
    echo "==================================\n";
    
    $supirKaryawan = \Illuminate\Support\Facades\DB::table('karyawans')
        ->select('id', 'nama_lengkap', 'nama_panggilan', 'divisi', 'status')
        ->where(function($query) {
            $query->where('divisi', 'LIKE', '%supir%')
                  ->orWhere('divisi', 'LIKE', '%SUPIR%')
                  ->orWhere('divisi', 'LIKE', '%driver%')
                  ->orWhere('divisi', 'LIKE', '%DRIVER%');
        })
        ->whereIn('status', ['aktif', 'active'])
        ->get();
    
    if($supirKaryawan->count() > 0) {
        foreach($supirKaryawan as $karyawan) {
            echo "ID: {$karyawan->id} | Nama: {$karyawan->nama_lengkap} | Panggilan: {$karyawan->nama_panggilan} | Divisi: {$karyawan->divisi}\n";
        }
    } else {
        echo "❌ No karyawan found with 'supir' in divisi\n";
        
        echo "\n🔍 Let's check all active karyawan (first 10):\n";
        echo "============================================\n";
        
        $allKaryawan = \Illuminate\Support\Facades\DB::table('karyawans')
            ->select('id', 'nama_lengkap', 'nama_panggilan', 'divisi', 'status')
            ->whereIn('status', ['aktif', 'active'])
            ->limit(10)
            ->get();
            
        foreach($allKaryawan as $karyawan) {
            echo "ID: {$karyawan->id} | Nama: {$karyawan->nama_lengkap} | Panggilan: {$karyawan->nama_panggilan} | Divisi: {$karyawan->divisi}\n";
        }
    }
    
    echo "\n📊 Count of karyawan by status:\n";
    echo "==============================\n";
    
    $statusCounts = \Illuminate\Support\Facades\DB::table('karyawans')
        ->select('status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
        
    foreach($statusCounts as $status) {
        echo "Status: {$status->status} | Count: {$status->count}\n";
    }
    
} catch(\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo "\n=== COMPLETE ===\n";

?>