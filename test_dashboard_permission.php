<?php

// Script untuk test akses dashboard permission secara langsung
echo "=== TEST DASHBOARD PERMISSION GATE ===\n\n";

require_once 'bootstrap/app.php';

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Test user admin
    $user = \App\Models\User::where('username', 'admin')->first();

    if ($user) {
        echo "Testing user: {$user->username}\n";
        echo "User ID: {$user->id}\n";
        echo "Karyawan ID: " . ($user->karyawan_id ?? 'NULL') . "\n";
        echo "Status: " . ($user->status ?? 'NULL') . "\n\n";

        // Auth::setUser untuk simulasi login
        \Illuminate\Support\Facades\Auth::setUser($user);

        // Test permission dashboard
        echo "Testing permission 'dashboard':\n";

        // Cek langsung via user->can()
        $canDashboard = $user->can('dashboard');
        echo "user->can('dashboard'): " . ($canDashboard ? 'TRUE ✅' : 'FALSE ❌') . "\n";

        // Cek via Gate facade
        $gateCan = \Illuminate\Support\Facades\Gate::allows('dashboard');
        echo "Gate::allows('dashboard'): " . ($gateCan ? 'TRUE ✅' : 'FALSE ❌') . "\n";

        // Cek permission langsung dari database
        $hasPermission = $user->permissions()->where('name', 'dashboard')->exists();
        echo "Has permission in database: " . ($hasPermission ? 'TRUE ✅' : 'FALSE ❌') . "\n";

        // List semua permission user
        echo "\nAll user permissions:\n";
        $permissions = $user->permissions()->pluck('name')->toArray();
        if (count($permissions) > 0) {
            $dashboardExists = in_array('dashboard', $permissions);
            echo "Total permissions: " . count($permissions) . "\n";
            echo "Dashboard in list: " . ($dashboardExists ? 'YES ✅' : 'NO ❌') . "\n";

            // Show first few permissions
            echo "First 10 permissions: " . implode(', ', array_slice($permissions, 0, 10)) . "\n";

            if ($dashboardExists) {
                echo "\n🎉 PERMISSION DASHBOARD DITEMUKAN!\n";
                echo "User admin memiliki permission dashboard.\n";

                // Test middleware yang mungkin memblokir
                echo "\n🔍 Testing middleware requirements:\n";

                // 1. Auth check
                echo "1. Auth check: " . (Auth::check() ? 'PASS ✅' : 'FAIL ❌') . "\n";

                // 2. Karyawan check
                $karyawan = $user->karyawan;
                echo "2. Karyawan exists: " . ($karyawan ? 'PASS ✅' : 'FAIL ❌') . "\n";

                if ($karyawan) {
                    echo "   Karyawan name: {$karyawan->nama_lengkap}\n";
                }

                // 3. Status check
                echo "3. User approved: " . ($user->status === 'approved' ? 'PASS ✅' : 'FAIL ❌') . "\n";

                if ($user->status !== 'approved') {
                    echo "   Current status: {$user->status}\n";
                }

                echo "\n💡 KEMUNGKINAN PENYEBAB JIKA MASIH DITOLAK:\n";
                echo "1. Session tidak ter-update setelah perubahan permission\n";
                echo "2. Cache route/config perlu di-clear\n";
                echo "3. Browser cache perlu di-refresh\n";
                echo "4. Ada middleware custom yang memblokir\n";

            }
        } else {
            echo "❌ User tidak punya permission sama sekali!\n";
        }

    } else {
        echo "❌ User admin tidak ditemukan!\n";
    }

    echo "\n🔧 QUICK TROUBLESHOOTING:\n";
    echo "1. Clear semua cache: php artisan optimize:clear\n";
    echo "2. Logout dan login ulang\n";
    echo "3. Clear browser cache (Ctrl+F5)\n";
    echo "4. Cek middleware di route dashboard\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
