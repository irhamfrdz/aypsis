<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "Admin user not found!\n";
    exit(1);
}

$perms = $user->permissions()->where('name', 'like', '%pricelist%')->pluck('name')->toArray();
echo "Admin user pricelist permissions:\n";
echo json_encode($perms, JSON_PRETTY_PRINT) . "\n";

// Check if admin has the correct format permissions
$hasDashFormat = false;
$hasDotFormat = false;

foreach ($perms as $perm) {
    if (strpos($perm, 'master-pricelist-sewa-kontainer-view') !== false) {
        $hasDashFormat = true;
    }
    if (strpos($perm, 'master-pricelist-sewa-kontainer.view') !== false) {
        $hasDotFormat = true;
    }
}

echo "\nPermission format check:\n";
echo "Has dash format (-): " . ($hasDashFormat ? "YES" : "NO") . "\n";
echo "Has dot format (.): " . ($hasDotFormat ? "YES" : "NO") . "\n";

if ($hasDashFormat && !$hasDotFormat) {
    echo "\n✅ Admin user has CORRECT permission format!\n";
} elseif (!$hasDashFormat && $hasDotFormat) {
    echo "\n❌ Admin user has WRONG permission format!\n";
} elseif ($hasDashFormat && $hasDotFormat) {
    echo "\n⚠️  Admin user has BOTH formats (duplication issue)!\n";
} else {
    echo "\n❌ Admin user has NO pricelist permissions!\n";
}
