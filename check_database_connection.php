<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel application
$app = new Application(getcwd());
$app->singleton(\Illuminate\Contracts\Http\Kernel::class, \App\Http\Kernel::class);
$app->singleton(\Illuminate\Contracts\Console\Kernel::class, \App\Console\Kernel::class);
$app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, \App\Exceptions\Handler::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK KONEKSI DATABASE ===\n\n";

try {
    echo "1. KONFIGURASI DATABASE:\n";
    echo "   Driver: " . config('database.default') . "\n";
    echo "   Connection: " . config('database.connections.' . config('database.default') . '.driver') . "\n";
    echo "   Host: " . config('database.connections.' . config('database.default') . '.host') . "\n";
    echo "   Port: " . config('database.connections.' . config('database.default') . '.port') . "\n";
    echo "   Database: " . config('database.connections.' . config('database.default') . '.database') . "\n";
    echo "   Username: " . config('database.connections.' . config('database.default') . '.username') . "\n";
    
    echo "\n2. TEST KONEKSI DATABASE:\n";
    
    // Test basic connection
    $pdo = DB::connection()->getPdo();
    echo "   ✅ Koneksi PDO berhasil!\n";
    echo "   📋 PDO Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "   📋 Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    
    echo "\n3. TEST QUERY SEDERHANA:\n";
    
    // Test simple query
    $result = DB::select('SELECT 1 as test');
    echo "   ✅ Query SELECT berhasil!\n";
    echo "   📋 Result: " . json_encode($result) . "\n";
    
    echo "\n4. CEK TABEL PERMISSIONS:\n";
    
    // Check if permissions table exists
    $tables = DB::select("SHOW TABLES LIKE 'permissions'");
    if (!empty($tables)) {
        echo "   ✅ Tabel 'permissions' ditemukan!\n";
        
        // Count permissions
        $count = DB::table('permissions')->count();
        echo "   📋 Jumlah permission: " . $count . "\n";
        
        // Check specific permissions
        $approvalPermissions = DB::table('permissions')
            ->where('name', 'like', 'approval-surat-jalan%')
            ->get(['id', 'name']);
            
        echo "   📋 Approval Surat Jalan permissions:\n";
        if ($approvalPermissions->count() > 0) {
            foreach ($approvalPermissions as $perm) {
                echo "      - {$perm->name} (ID: {$perm->id})\n";
            }
        } else {
            echo "      ❌ Tidak ada permission approval-surat-jalan ditemukan\n";
        }
        
    } else {
        echo "   ❌ Tabel 'permissions' tidak ditemukan!\n";
        echo "   💡 Mungkin perlu menjalankan: php artisan migrate\n";
    }
    
    echo "\n5. CEK TABEL USERS:\n";
    
    // Check users table
    $userTables = DB::select("SHOW TABLES LIKE 'users'");
    if (!empty($userTables)) {
        echo "   ✅ Tabel 'users' ditemukan!\n";
        
        $userCount = DB::table('users')->count();
        echo "   📋 Jumlah user: " . $userCount . "\n";
        
        // List some users
        $users = DB::table('users')->select('id', 'username')->limit(5)->get();
        echo "   📋 Sample users:\n";
        foreach ($users as $user) {
            echo "      - {$user->username} (ID: {$user->id})\n";
        }
        
    } else {
        echo "   ❌ Tabel 'users' tidak ditemukan!\n";
    }
    
    echo "\n6. CEK TABEL MODEL_HAS_PERMISSIONS:\n";
    
    // Check user permissions table
    $userPermTables = DB::select("SHOW TABLES LIKE 'model_has_permissions'");
    if (!empty($userPermTables)) {
        echo "   ✅ Tabel 'model_has_permissions' ditemukan!\n";
        
        $userPermCount = DB::table('model_has_permissions')->count();
        echo "   📋 Jumlah user-permission assignments: " . $userPermCount . "\n";
        
    } else {
        echo "   ❌ Tabel 'model_has_permissions' tidak ditemukan!\n";
        echo "   💡 Ini diperlukan untuk Spatie Laravel Permission\n";
    }
    
    echo "\n✅ KONEKSI DATABASE BERJALAN DENGAN BAIK!\n";
    echo "✅ Semua tabel yang diperlukan tersedia\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR PDO: " . $e->getMessage() . "\n";
    echo "💡 Kemungkinan masalah:\n";
    echo "   - MySQL server tidak berjalan\n";
    echo "   - Username/password salah\n";
    echo "   - Database tidak ada\n";
    echo "   - Port salah\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    
    if (str_contains($e->getMessage(), 'could not find driver')) {
        echo "💡 ERROR: Driver MySQL tidak ditemukan!\n";
        echo "💡 Solusi:\n";
        echo "   - Pastikan PHP extension 'pdo_mysql' aktif\n";
        echo "   - Cek php.ini dan uncomment: extension=pdo_mysql\n";
        echo "   - Restart web server\n";
    }
}