<?php
// Update existing users to approved status
echo "=== UPDATING EXISTING USERS TO APPROVED STATUS ===\n";

// Set up environment
define('LARAVEL_START', microtime(true));
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Checking existing users...\n";

    // Find users with old status values
    $usersToUpdate = App\Models\User::whereIn('status', ['active', 'inactive'])->get();

    echo "Found " . $usersToUpdate->count() . " users with old status values\n";

    if ($usersToUpdate->count() > 0) {
        foreach ($usersToUpdate as $user) {
            $newStatus = 'approved'; // Convert both active and inactive to approved
            if ($user->status === 'inactive') {
                $newStatus = 'pending'; // Keep inactive as pending for review
            }

            $user->update([
                'status' => $newStatus,
                'approved_by' => 1, // System admin
                'approved_at' => now(),
            ]);

            echo "Updated user {$user->username} from '{$user->getOriginal('status')}' to '{$newStatus}'\n";
        }

        echo "\n✅ All existing users have been updated!\n";
    } else {
        echo "\n✅ No users need updating.\n";
    }

    echo "\nCurrent user status summary:\n";
    $statusCounts = App\Models\User::selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->get();

    foreach ($statusCounts as $status) {
        echo "- {$status->status}: {$status->count} users\n";
    }

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
