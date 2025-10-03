<?php
/**
 * Script MASTER untuk membersihkan permission di server Ubuntu
 * Menangani 691+ permissions secara bertahap dan aman
 *
 * Strategi:
 * 1. Hapus permission yang tidak assigned
 * 2. Merge permission duplikat
 * 3. Hapus permission yang tidak digunakan di routes
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║     MASTER CLEANUP - SERVER UBUNTU (691+ PERMISSIONS)                 ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

$totalBefore = Permission::count();
echo "📊 Total permission saat ini: $totalBefore\n\n";

// STEP 1: Hapus permission yang tidak assigned
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║  STEP 1: Hapus Permission yang Tidak Assigned                        ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

$activeIds = DB::table('user_permissions')
    ->select('permission_id')
    ->distinct()
    ->pluck('permission_id')
    ->toArray();

$activeIdsFromRoles = DB::table('permission_role')
    ->select('permission_id')
    ->distinct()
    ->pluck('permission_id')
    ->toArray();

$allActiveIds = array_unique(array_merge($activeIds, $activeIdsFromRoles));
$unassigned = Permission::whereNotIn('id', $allActiveIds)->get();

echo "   Permission assigned: " . count($allActiveIds) . "\n";
echo "   Permission tidak assigned: " . $unassigned->count() . "\n\n";

if ($unassigned->count() > 0) {
    echo "❌ Preview permission yang tidak assigned (10 pertama):\n";
    foreach ($unassigned->take(10) as $perm) {
        echo "   - ID: " . $perm->id . " │ " . $perm->name . "\n";
    }
    if ($unassigned->count() > 10) {
        echo "   ... dan " . ($unassigned->count() - 10) . " lainnya\n";
    }
    echo "\n";
}

// STEP 2: Identifikasi permission duplikat
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║  STEP 2: Identifikasi Permission Duplikat                            ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

$allPerms = Permission::whereIn('id', $allActiveIds)->get();
$duplicateSets = [];
$processed = [];

foreach ($allPerms as $perm) {
    if (in_array($perm->id, $processed)) continue;

    $similars = [];
    foreach ($allPerms as $candidate) {
        if ($candidate->id == $perm->id || in_array($candidate->id, $processed)) continue;

        // Normalize names
        $n1 = str_replace(['.', '-'], '_', strtolower($perm->name));
        $n2 = str_replace(['.', '-'], '_', strtolower($candidate->name));

        if ($n1 === $n2) {
            $similars[] = $candidate;
        }
    }

    if (!empty($similars)) {
        $set = array_merge([$perm], $similars);
        $duplicateSets[] = $set;

        $processed[] = $perm->id;
        foreach ($similars as $s) {
            $processed[] = $s->id;
        }
    }
}

echo "   Set duplikasi ditemukan: " . count($duplicateSets) . "\n\n";

if (!empty($duplicateSets)) {
    echo "📂 Preview duplikasi (5 set pertama):\n";
    foreach (array_slice($duplicateSets, 0, 5) as $idx => $set) {
        echo "\n   Set " . ($idx + 1) . ":\n";
        foreach ($set as $perm) {
            echo "      - ID: " . $perm->id . " │ " . $perm->name . "\n";
        }
    }
    echo "\n";
}

// Calculate total to delete
$toDeleteFromUnassigned = $unassigned->count();
$toDeleteFromDuplicates = 0;

foreach ($duplicateSets as $set) {
    $toDeleteFromDuplicates += count($set) - 1; // Keep 1 from each set
}

$totalToDelete = $toDeleteFromUnassigned + $toDeleteFromDuplicates;
$estimatedRemaining = $totalBefore - $totalToDelete;

// Summary
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                    RINGKASAN PEMBERSIHAN                              ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 Estimasi:\n";
echo "   Total permission sekarang:           $totalBefore\n";
echo "   └─ Tidak assigned (hapus):           $toDeleteFromUnassigned\n";
echo "   └─ Duplikat (hapus):                 $toDeleteFromDuplicates\n";
echo "   Total yang akan dihapus:             $totalToDelete\n";
echo "   Permission yang akan tersisa:        $estimatedRemaining\n";
echo "   Persentase pengurangan:              " . round(($totalToDelete / $totalBefore) * 100, 1) . "%\n\n";

if ($estimatedRemaining > 250) {
    echo "⚠️  PERHATIAN:\n";
    echo "   Setelah pembersihan, masih akan ada ~$estimatedRemaining permissions.\n";
    echo "   Ini menunjukkan sistem Anda memiliki banyak permission yang kompleks.\n\n";
    echo "   Untuk analisis lebih detail:\n";
    echo "   1. php analyze_server_permissions.php\n";
    echo "   2. Review permission yang benar-benar digunakan di routes\n\n";
}

// Konfirmasi
echo "Lanjutkan pembersihan 2-STEP ini? (yes/no): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if (strtolower($input) !== 'yes' && strtolower($input) !== 'y') {
    echo "\n❌ Pembersihan dibatalkan.\n\n";
    echo "💡 Alternatif:\n";
    echo "   - Jalankan STEP 1 saja: php cleanup_server_permissions.php\n";
    echo "   - Jalankan STEP 2 saja: php cleanup_duplicate_permissions_v2.php\n";
    echo "   - Analisis detail: php analyze_server_permissions.php\n\n";
    exit;
}

// Backup
echo "\n💾 Membuat backup lengkap...\n";
$backupData = [
    'unassigned' => $unassigned->toArray(),
    'duplicate_sets' => array_map(function($set) {
        return array_map(function($p) {
            return ['id' => $p->id, 'name' => $p->name, 'description' => $p->description];
        }, $set);
    }, $duplicateSets)
];

$backupFile = base_path('backup_master_cleanup_' . date('Y-m-d_His') . '.json');
file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT));
echo "   ✅ " . basename($backupFile) . "\n\n";

// Execute cleanup
echo "🗑️  MEMULAI PEMBERSIHAN...\n\n";

DB::beginTransaction();

try {
    $stats = [
        'unassigned_deleted' => 0,
        'duplicates_merged' => 0,
        'duplicates_deleted' => 0,
    ];

    // STEP 1: Delete unassigned
    echo "1️⃣  Menghapus permission yang tidak assigned...\n";
    $stats['unassigned_deleted'] = Permission::whereNotIn('id', $allActiveIds)->delete();
    echo "   ✅ Dihapus: " . $stats['unassigned_deleted'] . " permissions\n\n";

    // STEP 2: Merge duplicates
    echo "2️⃣  Merge & hapus permission duplikat...\n";

    foreach ($duplicateSets as $set) {
        // Keep dash notation version, or first one
        $dashPerms = array_filter($set, fn($p) => strpos($p->name, '-') !== false && strpos($p->name, '.') === false);
        $keep = !empty($dashPerms) ? reset($dashPerms) : reset($set);
        $deletes = array_filter($set, fn($p) => $p->id != $keep->id);

        foreach ($deletes as $perm) {
            // Merge user_permissions
            $users = DB::table('user_permissions')
                ->where('permission_id', $perm->id)
                ->pluck('user_id');

            foreach ($users as $userId) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => $userId, 'permission_id' => $keep->id],
                    []
                );
                $stats['duplicates_merged']++;
            }

            // Merge permission_role
            $roles = DB::table('permission_role')
                ->where('permission_id', $perm->id)
                ->pluck('role_id');

            foreach ($roles as $roleId) {
                DB::table('permission_role')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $keep->id],
                    []
                );
                $stats['duplicates_merged']++;
            }

            // Delete old relations & permission
            DB::table('user_permissions')->where('permission_id', $perm->id)->delete();
            DB::table('permission_role')->where('permission_id', $perm->id)->delete();
            Permission::where('id', $perm->id)->delete();
            $stats['duplicates_deleted']++;
        }
    }

    echo "   ✅ Relasi di-merge: " . $stats['duplicates_merged'] . "\n";
    echo "   ✅ Duplikat dihapus: " . $stats['duplicates_deleted'] . "\n\n";

    DB::commit();

    $totalAfter = Permission::count();

    echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    PEMBERSIHAN SELESAI                                ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

    echo "📊 Hasil Akhir:\n";
    echo "   Permission sebelum:                  $totalBefore\n";
    echo "   └─ Tidak assigned dihapus:           " . $stats['unassigned_deleted'] . "\n";
    echo "   └─ Duplikat dihapus:                 " . $stats['duplicates_deleted'] . "\n";
    echo "   └─ Relasi di-merge:                  " . $stats['duplicates_merged'] . "\n";
    echo "   Permission sekarang:                 $totalAfter\n";
    echo "   Total dihapus:                       " . ($totalBefore - $totalAfter) . "\n";
    echo "   Persentase pengurangan:              " . round((($totalBefore - $totalAfter) / $totalBefore) * 100, 1) . "%\n\n";

    echo "💾 Backup: " . basename($backupFile) . "\n";
    echo "✅ Database berhasil dibersihkan!\n\n";

    if ($totalAfter > 250) {
        echo "💡 Rekomendasi Lanjutan:\n";
        echo "   Masih ada $totalAfter permissions.\n";
        echo "   Untuk analisis lebih lanjut:\n";
        echo "   php analyze_server_permissions.php\n\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "🔄 Rollback dilakukan.\n";
    echo "💾 Backup: " . basename($backupFile) . "\n\n";
}
