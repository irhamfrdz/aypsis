<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('username', 'admin')->first();
if ($user) {
    echo "Testing user->can('tagihan-cat-view') for admin user:\n";

    $canView = $user->can('tagihan-cat-view');
    echo "user->can('tagihan-cat-view'): " . ($canView ? 'true' : 'false') . "\n";

    if ($canView) {
        echo "✅ Admin user has permission to view Tagihan CAT menu\n";
        echo "Menu should appear in sidebar\n";
    } else {
        echo "❌ Admin user does NOT have permission to view Tagihan CAT menu\n";
        echo "Menu will NOT appear in sidebar\n";
    }

    echo "\nAll tagihan-cat related permissions:\n";
    foreach($user->permissions as $perm) {
        if (strpos($perm->name, 'tagihan-cat') !== false) {
            echo "- {$perm->name}\n";
        }
    }
} else {
    echo 'Admin user not found' . PHP_EOL;
}
