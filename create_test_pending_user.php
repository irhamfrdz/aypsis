<?php
// Create a test pending user to see badge
echo "=== CREATING TEST PENDING USER ===\n";

define('LARAVEL_START', microtime(true));
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Find a karyawan without user account
    $karyawan = App\Models\Karyawan::whereDoesntHave('user')->first();

    if (!$karyawan) {
        echo "No karyawan available for test user\n";
        return;
    }

    // Create test pending user
    $testUser = App\Models\User::create([
        'name' => 'Test Pending User for Badge',
        'username' => 'test_pending_badge_' . time(),
        'password' => bcrypt('password123'),
        'karyawan_id' => $karyawan->id,
        'status' => 'pending',
        'registration_reason' => 'Test to see approval badge in sidebar',
    ]);

    echo "✅ Created test pending user: {$testUser->username}\n";
    echo "✅ Status: {$testUser->status}\n";
    echo "✅ Karyawan: {$karyawan->nama_lengkap}\n";

    $pendingCount = App\Models\User::where('status', 'pending')->count();
    echo "✅ Total pending users now: {$pendingCount}\n";

    echo "\nNow refresh your browser and check the sidebar!\n";
    echo "You should see 'Persetujuan User' menu with a red badge showing '{$pendingCount}'\n";

    echo "\nTo clean up later, run: php artisan tinker\n";
    echo "Then: App\\Models\\User::where('username', '{$testUser->username}')->delete();\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
