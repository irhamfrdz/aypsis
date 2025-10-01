<?php
require_once __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

echo "=== PERBAIKAN CEPAT PERMISSION ===\n";

// Clear semua cache
Artisan::call("cache:clear");
Artisan::call("config:clear"); 
Artisan::call("route:clear");
echo "✅ Cache cleared\n";

// Reload permission untuk semua user
$users = User::with("permissions")->get();
foreach ($users as $user) {
    $user->touch(); // Update timestamp untuk force reload
}
echo "✅ User permissions reloaded\n";

echo "\n🎉 Perbaikan selesai! Coba akses menu lagi.\n";
