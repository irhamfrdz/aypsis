<?php
/**
 * Script untuk restore permission dari backup JSON
 *
 * Usage: php restore_permissions_from_backup.php <backup_file.json>
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║          RESTORE PERMISSION DARI BACKUP                               ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

// Check if backup file is provided
if ($argc < 2) {
    echo "❌ Error: Backup file tidak ditemukan!\n\n";
    echo "Usage: php restore_permissions_from_backup.php <backup_file.json>\n\n";
    echo "Contoh:\n";
    echo "  php restore_permissions_from_backup.php backup_permissions_2025-10-03_123456.json\n\n";

    // List available backup files
    $backupFiles = glob(base_path('backup_permissions_*.json'));
    if (!empty($backupFiles)) {
        echo "📁 Backup files yang tersedia:\n";
        foreach ($backupFiles as $file) {
            echo "   - " . basename($file) . "\n";
        }
    }
    exit(1);
}

$backupFile = $argv[1];

// Check if file is absolute path or relative
if (!file_exists($backupFile)) {
    $backupFile = base_path($backupFile);
}

if (!file_exists($backupFile)) {
    echo "❌ Error: File backup tidak ditemukan: $backupFile\n";
    exit(1);
}

echo "📁 Backup file: " . basename($backupFile) . "\n";
echo "📍 Path: $backupFile\n\n";

// Read backup file
$backupContent = file_get_contents($backupFile);
$backupData = json_decode($backupContent, true);

if (!$backupData) {
    echo "❌ Error: Gagal membaca file backup atau format JSON tidak valid!\n";
    exit(1);
}

echo "📊 Total permission di backup: " . count($backupData) . "\n\n";

// Preview permissions to restore
echo "🔍 Preview permission yang akan di-restore (10 pertama):\n";
echo str_repeat("─", 75) . "\n";

foreach (array_slice($backupData, 0, 10) as $perm) {
    echo "   ✅ ID: " . str_pad($perm['id'], 4) . " │ " . $perm['name'] . "\n";
}

if (count($backupData) > 10) {
    echo "   ... dan " . (count($backupData) - 10) . " permission lainnya\n";
}

echo str_repeat("─", 75) . "\n\n";

// Konfirmasi
echo "⚠️  PERINGATAN: Script ini akan me-restore " . count($backupData) . " permissions!\n";
echo "   Permission yang sudah ada dengan ID yang sama akan di-skip.\n\n";

echo "Lanjutkan restore? (yes/no): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if (strtolower($input) !== 'yes' && strtolower($input) !== 'y') {
    echo "\n❌ Restore dibatalkan.\n";
    exit;
}

echo "\n♻️  Memulai restore...\n\n";

DB::beginTransaction();

try {
    $restored = 0;
    $skipped = 0;
    $errors = 0;

    foreach ($backupData as $permData) {
        // Check if permission already exists
        $exists = Permission::where('id', $permData['id'])->exists();

        if ($exists) {
            echo "⏭️  Skip ID {$permData['id']}: {$permData['name']} (sudah ada)\n";
            $skipped++;
            continue;
        }

        try {
            // Create permission
            Permission::create([
                'id' => $permData['id'],
                'name' => $permData['name'],
                'description' => $permData['description'] ?? '',
            ]);

            $restored++;

            if ($restored % 10 == 0) {
                echo "✅ Restored $restored permissions...\n";
            }

        } catch (\Exception $e) {
            echo "❌ Error restoring ID {$permData['id']}: {$e->getMessage()}\n";
            $errors++;
        }
    }

    DB::commit();

    echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    RESTORE SELESAI                                    ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

    echo "📊 Ringkasan:\n";
    echo "   ✅ Permission di-restore:  $restored\n";
    echo "   ⏭️  Permission di-skip:     $skipped\n";
    echo "   ❌ Error:                  $errors\n\n";

    if ($errors > 0) {
        echo "⚠️  Terdapat $errors error. Periksa log di atas.\n\n";
    }

    // Tampilkan statistik akhir
    $totalPermissions = Permission::count();
    echo "📈 Total permission sekarang: $totalPermissions\n";

    echo "\n✅ Restore selesai!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "🔄 Rollback dilakukan. Tidak ada perubahan pada database.\n";
}
