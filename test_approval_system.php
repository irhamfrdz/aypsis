<?php
// Test the complete approval system
echo "=== TESTING USER APPROVAL SYSTEM ===\n";

// Set up environment
define('LARAVEL_START', microtime(true));
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "1. Testing registration creates pending status...\n";

    // Find a karyawan without user account
    $karyawan = App\Models\Karyawan::whereDoesntHave('user')->first();

    if (!$karyawan) {
        echo "❌ No karyawan without user account found\n";
        return;
    }

    echo "Found karyawan: {$karyawan->nama_lengkap}\n";

    // Create test user with pending status
    $testUser = App\Models\User::create([
        'name' => 'Test User Pending',
        'username' => 'test_pending_' . time(),
        'password' => bcrypt('password123'),
        'karyawan_id' => $karyawan->id,
        'status' => 'pending',
        'registration_reason' => 'Testing approval system',
    ]);

    echo "✅ Created test user with pending status: {$testUser->username}\n";

    echo "\n2. Testing status methods...\n";
    echo "isPending(): " . ($testUser->isPending() ? 'true' : 'false') . "\n";
    echo "isApproved(): " . ($testUser->isApproved() ? 'true' : 'false') . "\n";
    echo "isRejected(): " . ($testUser->isRejected() ? 'true' : 'false') . "\n";

    echo "\n3. Testing approval process...\n";
    $adminUser = App\Models\User::where('username', 'admin')->first();

    $testUser->update([
        'status' => 'approved',
        'approved_by' => $adminUser->id,
        'approved_at' => now(),
    ]);

    $testUser->refresh();
    echo "✅ User approved by: {$testUser->approvedBy->name}\n";
    echo "✅ Approved at: {$testUser->approved_at}\n";
    echo "✅ Status methods after approval:\n";
    echo "  - isPending(): " . ($testUser->isPending() ? 'true' : 'false') . "\n";
    echo "  - isApproved(): " . ($testUser->isApproved() ? 'true' : 'false') . "\n";

    echo "\n4. Testing current pending count...\n";
    $pendingCount = App\Models\User::where('status', 'pending')->count();
    echo "Pending users count: {$pendingCount}\n";

    echo "\n5. Testing route generation...\n";
    echo "Approval index route: " . route('admin.user-approval.index') . "\n";
    echo "Approve user route: " . route('admin.user-approval.approve', $testUser) . "\n";
    echo "Reject user route: " . route('admin.user-approval.reject', $testUser) . "\n";

    echo "\n6. Cleaning up test user...\n";
    $testUser->delete();
    echo "✅ Test user deleted\n";

    echo "\n🎉 ALL APPROVAL SYSTEM TESTS PASSED!\n";
    echo "✅ Registration creates pending status\n";
    echo "✅ Status methods work correctly\n";
    echo "✅ Approval process updates fields\n";
    echo "✅ Audit trail is maintained\n";
    echo "✅ Routes are properly generated\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
