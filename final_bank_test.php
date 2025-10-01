<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

echo "=== FINAL BANK ACCESS TEST ===\n";

$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found\n";
    exit(1);
}

echo "✅ Admin user: " . $admin->username . "\n";

// Test permission
$canAccess = $admin->can('master-bank-view');
echo "Can access master-bank-view: " . ($canAccess ? 'YES' : 'NO') . "\n";

// Clear additional cache
try {
    Artisan::call('config:clear');
    echo "✅ Config cache cleared\n";
} catch (Exception $e) {
    echo "⚠️  Config clear failed\n";
}

try {
    Artisan::call('view:clear');
    echo "✅ View cache cleared\n";
} catch (Exception $e) {
    echo "⚠️  View clear failed\n";
}

if ($canAccess) {
    echo "\n🎉 SUCCESS! Bank access is working correctly.\n";
    echo "\n📝 NEXT STEPS:\n";
    echo "1. Log out from the application\n";
    echo "2. Clear browser cache and cookies\n";
    echo "3. Log back in as admin\n";
    echo "4. Check if Bank menu appears in sidebar\n";
    echo "5. Try accessing: /master/bank\n";
    echo "\n✅ All permissions are correctly configured!\n";
} else {
    echo "\n❌ Permission still not working. Please check:\n";
    echo "1. User permissions in database\n";
    echo "2. Gate definitions in AppServiceProvider\n";
    echo "3. Application logs for errors\n";
}

?>
