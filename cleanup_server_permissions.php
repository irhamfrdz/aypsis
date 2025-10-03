<?php
/**
 * Script pembersihan permission untuk server Ubuntu
 * Menangani 691+ permissions dengan aman
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║     PEMBERSIHAN PERMISSION SERVER UBUNTU                              ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

// 1. Identifikasi permission yang TIDAK assigned ke user/role
echo "🔍 Mengidentifikasi permission yang tidak digunakan...\n";

$activePermissionIds = DB::table('user_permissions')
    ->select('permission_id')
    ->distinct()
    ->pluck('permission_id')
    ->toArray();

$activePermissionIdsFromRoles = DB::table('permission_role')
    ->select('permission_id')
    ->distinct()
    ->pluck('permission_id')
    ->toArray();

$allActiveIds = array_unique(array_merge($activePermissionIds, $activePermissionIdsFromRoles));

echo "   ✅ Permission assigned ke users: " . count($activePermissionIds) . "\n";
echo "   ✅ Permission assigned ke roles: " . count($activePermissionIdsFromRoles) . "\n";
echo "   ✅ Total permission AKTIF: " . count($allActiveIds) . "\n\n";

// Get permissions yang TIDAK assigned
$unusedPermissions = Permission::whereNotIn('id', $allActiveIds)->get();

echo "❌ Permission yang TIDAK assigned: " . $unusedPermissions->count() . "\n\n";

if ($unusedPermissions->count() == 0) {
    echo "✅ Tidak ada permission yang bisa dihapus dengan aman.\n";
    echo "   Semua permission sudah assigned ke user atau role.\n\n";

    echo "💡 Untuk pembersihan lebih lanjut:\n";
    echo "   1. Review permission duplikat (dot vs dash notation)\n";
    echo "   2. Hapus permission yang tidak digunakan di routes\n";
    echo "   3. Jalankan: php analyze_server_permissions.php untuk detail\n\n";
    exit(0);
}

// 2. Group by module untuk review
echo "📂 Breakdown by module:\n";
echo str_repeat("─", 75) . "\n";

$modules = [];
foreach ($unusedPermissions as $perm) {
    $prefix = explode('-', $perm->name)[0];
    if (strpos($perm->name, '.') !== false) {
        $prefix = explode('.', $perm->name)[0];
    }

    if (!isset($modules[$prefix])) {
        $modules[$prefix] = 0;
    }
    $modules[$prefix]++;
}

arsort($modules);

foreach ($modules as $module => $count) {
    echo "   " . str_pad($module, 30) . " : " . str_pad($count, 4, ' ', STR_PAD_LEFT) . " permissions\n";
}

echo str_repeat("─", 75) . "\n\n";

// 3. Preview permissions yang akan dihapus
echo "🔍 Preview permission yang akan dihapus (20 pertama):\n";
echo str_repeat("─", 75) . "\n";

foreach ($unusedPermissions->take(20) as $perm) {
    echo "   ❌ ID: " . str_pad($perm->id, 4) . " │ " . $perm->name . "\n";
}

if ($unusedPermissions->count() > 20) {
    echo "   ... dan " . ($unusedPermissions->count() - 20) . " permission lainnya\n";
}

echo str_repeat("─", 75) . "\n\n";

// 4. Buat backup
echo "💾 Membuat backup...\n";

$backupFile = base_path('backup_server_unused_permissions_' . date('Y-m-d_His') . '.json');
file_put_contents($backupFile, json_encode($unusedPermissions->toArray(), JSON_PRETTY_PRINT));

echo "   ✅ Backup disimpan: " . basename($backupFile) . "\n";
echo "   📊 Total permission di-backup: " . $unusedPermissions->count() . "\n\n";

// 5. Konfirmasi
$totalPermissions = Permission::count();
$afterDelete = $totalPermissions - $unusedPermissions->count();

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                    RINGKASAN PEMBERSIHAN                              ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 Status:\n";
echo "   Total permission saat ini:           " . $totalPermissions . "\n";
echo "   Permission yang akan dihapus:        " . $unusedPermissions->count() . "\n";
echo "   Permission yang akan tersisa:        " . $afterDelete . "\n";
echo "   Persentase yang dihapus:             " . round(($unusedPermissions->count() / $totalPermissions) * 100, 2) . "%\n\n";

echo "⚠️  PERINGATAN:\n";
echo "   - Script ini akan menghapus " . $unusedPermissions->count() . " permissions!\n";
echo "   - Hanya permission yang TIDAK assigned ke user/role yang dihapus\n";
echo "   - Backup sudah dibuat: " . basename($backupFile) . "\n";
echo "   - Operasi ini AMAN karena tidak mempengaruhi user/role yang ada\n\n";

echo "Lanjutkan pembersihan? (yes/no): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if (strtolower($input) !== 'yes' && strtolower($input) !== 'y') {
    echo "\n❌ Pembersihan dibatalkan.\n";
    echo "💾 Backup tetap tersimpan: " . basename($backupFile) . "\n\n";
    exit;
}

echo "\n🗑️  Memulai pembersihan...\n\n";

DB::beginTransaction();

try {
    $permissionIds = $unusedPermissions->pluck('id')->toArray();

    // Hapus dari permissions table
    // (Tidak perlu hapus dari user_permissions/permission_role karena sudah tidak ada)
    echo "1️⃣  Menghapus permissions dari database...\n";
    $deleted = Permission::whereIn('id', $permissionIds)->delete();
    echo "   ✅ Dihapus: $deleted permissions\n\n";

    DB::commit();

    echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    PEMBERSIHAN SELESAI                                ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

    $newTotal = Permission::count();

    echo "📊 Hasil:\n";
    echo "   ✅ Permission dihapus:               $deleted\n";
    echo "   📈 Total permission sekarang:        $newTotal\n";
    echo "   💾 Backup tersimpan:                 " . basename($backupFile) . "\n\n";

    echo "✅ Database telah dibersihkan!\n\n";

    // Rekomendasi selanjutnya
    if ($newTotal > 300) {
        echo "💡 REKOMENDASI LANJUTAN:\n";
        echo "   Database masih memiliki $newTotal permissions.\n";
        echo "   Untuk pembersihan lebih lanjut:\n\n";

        echo "   1. Review permission duplikat:\n";
        echo "      php analyze_server_permissions.php\n\n";

        echo "   2. Review file yang dibuat:\n";
        echo "      - server_duplicate_permissions.json\n";
        echo "      - server_unassigned_permissions.json\n\n";

        echo "   3. Hapus permission duplikat secara manual atau gunakan:\n";
        echo "      php cleanup_duplicate_permissions.php\n\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "🔄 Rollback dilakukan. Tidak ada perubahan pada database.\n";
    echo "💾 Backup tetap tersimpan: " . basename($backupFile) . "\n\n";
}
