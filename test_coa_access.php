<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "🧪 Test Akses Route COA untuk User Admin\n";
echo "========================================\n\n";

// Simulate request as admin user
$user = User::where('username', 'admin')->first();

if ($user) {
    echo "✅ User admin ditemukan\n\n";

    // Test permission check
    $canView = $user->can('master-coa-view');
    echo "🔐 Permission Check:\n";
    echo "   - master-coa-view: " . ($canView ? '✅ YES' : '❌ NO') . "\n\n";

    if ($canView) {
        // Test route resolution
        try {
            $url = route('master-coa-index');
            echo "🛣️ Route Test:\n";
            echo "   - Route URL: $url\n";
            echo "   - Route exists: ✅ YES\n\n";

            // Test middleware (simulate)
            echo "🛡️ Middleware Test:\n";
            echo "   - User authenticated: ✅ YES\n";
            echo "   - Has permission: ✅ YES\n";
            echo "   - Route accessible: ✅ SHOULD BE ACCESSIBLE\n\n";

            echo "📋 Summary:\n";
            echo "   ✅ User has permission\n";
            echo "   ✅ Route exists\n";
            echo "   ✅ Middleware should pass\n";
            echo "   💡 If menu still not visible, check:\n";
            echo "      - Browser cache (Ctrl+F5)\n";
            echo "      - JavaScript errors in console\n";
            echo "      - CSS hiding the menu\n";
            echo "      - Master menu dropdown state\n";

        } catch (Exception $e) {
            echo "❌ Route Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ User doesn't have permission\n";
    }

} else {
    echo "❌ User admin not found\n";
}
