<?php
// Script untuk test data surat jalan approval

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load .env file
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_DATABASE'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔍 Testing Surat Jalan Approval Data...\n";
echo "======================================\n\n";

// Check surat jalan approvals
$approvals = Capsule::table('surat_jalan_approvals')
    ->join('surat_jalans', 'surat_jalan_approvals.surat_jalan_id', '=', 'surat_jalans.id')
    ->where('surat_jalan_approvals.status', 'pending')
    ->where('surat_jalan_approvals.approval_level', 'approval')
    ->select(
        'surat_jalan_approvals.id as approval_id',
        'surat_jalan_approvals.status as approval_status',
        'surat_jalans.id as surat_jalan_id',
        'surat_jalans.no_surat_jalan',
        'surat_jalans.no_kontainer',
        'surat_jalans.no_seal',
        'surat_jalans.tipe_kontainer'
    )
    ->limit(5)
    ->get();

echo "📋 Pending Approvals Count: " . $approvals->count() . "\n\n";

if ($approvals->count() > 0) {
    echo "Sample pending approvals:\n";
    echo "-------------------------\n";
    foreach ($approvals as $approval) {
        echo "Approval ID: {$approval->approval_id}\n";
        echo "Surat Jalan ID: {$approval->surat_jalan_id}\n";
        echo "No. Surat Jalan: {$approval->no_surat_jalan}\n";
        echo "Tipe Kontainer: " . ($approval->tipe_kontainer ?: 'N/A') . "\n";
        echo "No. Kontainer: " . ($approval->no_kontainer ?: 'Belum diisi') . "\n";
        echo "No. Seal: " . ($approval->no_seal ?: 'Belum diisi') . "\n";
        echo "Status: {$approval->approval_status}\n";
        echo "---\n";
    }
} else {
    echo "❌ No pending approvals found\n\n";
    
    // Check if there are any approvals at all
    $totalApprovals = Capsule::table('surat_jalan_approvals')->count();
    echo "Total approvals in database: {$totalApprovals}\n";
    
    if ($totalApprovals === 0) {
        echo "\n🔧 Creating a sample pending approval...\n";
        
        // Get a surat jalan without approval
        $suratJalan = Capsule::table('surat_jalans')
            ->whereNotIn('id', function($query) {
                $query->select('surat_jalan_id')->from('surat_jalan_approvals');
            })
            ->first();
        
        if ($suratJalan) {
            Capsule::table('surat_jalan_approvals')->insert([
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => 'approval',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "✅ Created approval for Surat Jalan ID: {$suratJalan->id} ({$suratJalan->no_surat_jalan})\n";
        } else {
            echo "❌ No surat jalan available for creating approval\n";
        }
    }
}

echo "\n📊 Stock Kontainer Status:\n";
echo "==========================\n";
$stockStats = Capsule::table('stock_kontainers')
    ->select('status', Capsule::raw('COUNT(*) as count'))
    ->groupBy('status')
    ->get();

foreach ($stockStats as $stat) {
    echo "- {$stat->status}: {$stat->count}\n";
}

echo "\n🎯 Ready for testing inline edit functionality!\n";
echo "\n💡 Access: /approval/surat-jalan to test the edit features\n";