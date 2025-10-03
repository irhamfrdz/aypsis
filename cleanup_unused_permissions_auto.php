<?php
/**
 * Script otomatis untuk membersihkan permission yang tidak digunakan
 * Berdasarkan analisis yang sudah dilakukan
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║     PEMBERSIHAN OTOMATIS PERMISSION YANG TIDAK DIGUNAKAN             ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

// Daftar ID permission yang tidak digunakan (dari hasil analisis)
$unusedPermissionIds = [
    // Admin module
    1369, 1368, 1370,

    // Master module
    471, 469, 749, 626, 631, 629, 1382, 628, 630, 643, 1043, 1042, 1039, 477,
    46, 44, 40, 74, 72, 68, 884, 889, 1383, 888, 60, 58, 54, 90, 88, 84,
    1324, 1329, 1327, 1384, 1326, 1325, 1328, 621, 620, 619, 463, 461, 395,
    457, 394, 83, 81, 77, 97, 95, 91, 902, 904, 1385, 903, 62, 330, 67, 65,
    61, 66, 560, 558, 562,

    // Pembayaran module
    1371, 1214, 1216, 1218, 1217, 1215, 1372, 360, 1373, 411, 308, 310,

    // Perbaikan module
    397,

    // Pranota module
    351, 1375, 999, 429, 353, 424, 1376, 1211, 1377, 405, 276, 278, 1225,

    // Tagihan module
    1236, 1235, 1234, 925, 1232, 924, 271, 270, 1378, 1380, 1379, 265, 1319, 1321
];

echo "📊 Total permission yang akan dihapus: " . count($unusedPermissionIds) . "\n\n";

// 1. Buat backup terlebih dahulu
echo "💾 Membuat backup permission...\n";

$backupData = Permission::whereIn('id', $unusedPermissionIds)->get();
$backupFile = base_path('backup_permissions_' . date('Y-m-d_His') . '.json');
file_put_contents($backupFile, json_encode($backupData->toArray(), JSON_PRETTY_PRINT));

echo "✅ Backup disimpan di: " . basename($backupFile) . "\n";
echo "   Total data di-backup: " . $backupData->count() . " permissions\n\n";

// 2. Tampilkan preview permission yang akan dihapus
echo "🔍 Preview permission yang akan dihapus:\n";
echo str_repeat("─", 75) . "\n";

foreach ($backupData->take(10) as $perm) {
    echo "   ❌ ID: " . str_pad($perm->id, 4) . " │ " . $perm->name . "\n";
}

if ($backupData->count() > 10) {
    echo "   ... dan " . ($backupData->count() - 10) . " permission lainnya\n";
}

echo str_repeat("─", 75) . "\n\n";

// 3. Konfirmasi
echo "⚠️  PERINGATAN: Script ini akan menghapus " . count($unusedPermissionIds) . " permissions!\n";
echo "   Pastikan Anda sudah backup database.\n\n";

echo "Lanjutkan? (yes/no): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if (strtolower($input) !== 'yes' && strtolower($input) !== 'y') {
    echo "\n❌ Pembersihan dibatalkan.\n";
    exit;
}

echo "\n🗑️  Memulai pembersihan...\n\n";

DB::beginTransaction();

try {
    // 1. Hapus relasi di user_permissions
    echo "1️⃣  Menghapus relasi di user_permissions...\n";
    $deletedUserPerms = DB::table('user_permissions')
        ->whereIn('permission_id', $unusedPermissionIds)
        ->delete();
    echo "   ✅ Dihapus: $deletedUserPerms relasi\n\n";

    // 2. Hapus relasi di permission_role
    echo "2️⃣  Menghapus relasi di permission_role...\n";
    $deletedRolePerms = DB::table('permission_role')
        ->whereIn('permission_id', $unusedPermissionIds)
        ->delete();
    echo "   ✅ Dihapus: $deletedRolePerms relasi\n\n";

    // 3. Hapus permissions
    echo "3️⃣  Menghapus permissions...\n";
    $deletedPermissions = Permission::whereIn('id', $unusedPermissionIds)->delete();
    echo "   ✅ Dihapus: $deletedPermissions permissions\n\n";

    DB::commit();

    echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    PEMBERSIHAN SELESAI                                ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

    echo "📊 Ringkasan:\n";
    echo "   ✅ User permissions dihapus:  $deletedUserPerms relasi\n";
    echo "   ✅ Role permissions dihapus:  $deletedRolePerms relasi\n";
    echo "   ✅ Permissions dihapus:       $deletedPermissions permissions\n\n";

    echo "💾 Backup disimpan di: " . basename($backupFile) . "\n";
    echo "✅ Database telah dibersihkan!\n\n";

    // Tampilkan statistik akhir
    $totalPermissions = Permission::count();
    echo "📈 Total permission yang tersisa: $totalPermissions\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "🔄 Rollback dilakukan. Tidak ada perubahan pada database.\n";
    echo "💾 Backup tetap tersimpan di: " . basename($backupFile) . "\n";
}
