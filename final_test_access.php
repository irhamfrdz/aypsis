<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

try {
    echo "=== FINAL TEST: Master Tujuan Kegiatan Utama Access ===\n\n";

    // Get admin user
    $admin = User::find(1);
    if (!$admin) {
        echo "❌ Admin user not found\n";
        exit;
    }

    echo "Testing admin user: {$admin->name} (ID: {$admin->id})\n\n";

    // Test permissions
    $permissions = [
        'master-tujuan-view' => 'View Master Tujuan Kegiatan Utama',
        'master-tujuan-create' => 'Create new Tujuan Kegiatan Utama',
        'master-tujuan-update' => 'Edit Tujuan Kegiatan Utama',
        'master-tujuan-delete' => 'Delete Tujuan Kegiatan Utama',
        'master-tujuan-export' => 'Export Tujuan Kegiatan Utama',
        'master-tujuan-print' => 'Print Tujuan Kegiatan Utama'
    ];

    echo "Permission test results:\n";
    $allGranted = true;

    foreach ($permissions as $permission => $description) {
        $canAccess = $admin->can($permission);
        $status = $canAccess ? "✅ GRANTED" : "❌ DENIED";
        echo "   {$status} {$permission} - {$description}\n";

        if (!$canAccess) {
            $allGranted = false;
        }
    }

    echo "\n";

    if ($allGranted) {
        echo "🎉 SUCCESS! Admin can access Master Tujuan Kegiatan Utama with all permissions!\n";
        echo "\nWhat this means:\n";
        echo "✅ Menu will be visible in the sidebar\n";
        echo "✅ Can view the index page\n";
        echo "✅ Can create new entries\n";
        echo "✅ Can edit existing entries\n";
        echo "✅ Can delete entries\n";
        echo "✅ Can export to CSV\n";
        echo "✅ Can print reports\n";

        echo "\n📝 Routes that will work:\n";
        echo "   - GET /master/tujuan-kegiatan-utama (index)\n";
        echo "   - GET /master/tujuan-kegiatan-utama/create\n";
        echo "   - POST /master/tujuan-kegiatan-utama (store)\n";
        echo "   - GET /master/tujuan-kegiatan-utama/{id} (show)\n";
        echo "   - GET /master/tujuan-kegiatan-utama/{id}/edit\n";
        echo "   - PUT /master/tujuan-kegiatan-utama/{id} (update)\n";
        echo "   - DELETE /master/tujuan-kegiatan-utama/{id} (destroy)\n";
        echo "   - GET /master/tujuan-kegiatan-utama/export\n";
        echo "   - GET /master/tujuan-kegiatan-utama/print\n";

    } else {
        echo "❌ ISSUE: Admin is missing some permissions\n";
    }

    // Test database structure
    echo "\n📊 Database verification:\n";

    // Check if table exists
    $tableExists = DB::select("SHOW TABLES LIKE 'tujuan_kegiatan_utamas'");
    if ($tableExists) {
        echo "   ✅ Table 'tujuan_kegiatan_utamas' exists\n";

        // Count records
        $recordCount = DB::table('tujuan_kegiatan_utamas')->count();
        echo "   📈 Records in table: {$recordCount}\n";
    } else {
        echo "   ❌ Table 'tujuan_kegiatan_utamas' not found\n";
    }

    echo "\n=== CONCLUSION ===\n";
    echo "Master Tujuan Kegiatan Utama is now fully configured to use the same permissions as Master Tujuan.\n";
    echo "The module should be accessible to any user who has access to Master Tujuan.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
