<?php

// Script untuk verifikasi komponen audit log sudah lengkap
// File: verify_audit_components.php

echo "🔍 VERIFIKASI KOMPONEN AUDIT LOG\n";
echo "================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Cek file trait Auditable
echo "1. 🔍 Checking Auditable Trait...\n";
$auditableTraitPath = __DIR__ . '/app/Traits/Auditable.php';
if (file_exists($auditableTraitPath)) {
    $content = file_get_contents($auditableTraitPath);
    if (strpos($content, 'trait Auditable') !== false) {
        $success[] = "✅ Trait Auditable tersedia";
    } else {
        $errors[] = "❌ File Auditable.php tidak valid";
    }
} else {
    $errors[] = "❌ Trait Auditable tidak ditemukan";
}

// 2. Cek model AuditLog
echo "2. 🔍 Checking AuditLog Model...\n";
$auditLogModelPath = __DIR__ . '/app/Models/AuditLog.php';
if (file_exists($auditLogModelPath)) {
    $content = file_get_contents($auditLogModelPath);
    if (strpos($content, 'class AuditLog') !== false) {
        $success[] = "✅ Model AuditLog tersedia";
    } else {
        $errors[] = "❌ File AuditLog.php tidak valid";
    }
} else {
    $errors[] = "❌ Model AuditLog tidak ditemukan";
}

// 3. Cek controller AuditLogController
echo "3. 🔍 Checking AuditLogController...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/AuditLogController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    if (strpos($content, 'class AuditLogController') !== false &&
        strpos($content, 'getModelAuditLogs') !== false) {
        $success[] = "✅ AuditLogController tersedia dengan method AJAX";
    } else {
        $errors[] = "❌ AuditLogController tidak lengkap";
    }
} else {
    $errors[] = "❌ AuditLogController tidak ditemukan";
}

// 4. Cek komponen audit-log-button
echo "4. 🔍 Checking Audit Log Button Component...\n";
$buttonComponentPath = __DIR__ . '/resources/views/components/audit-log-button.blade.php';
if (file_exists($buttonComponentPath)) {
    $content = file_get_contents($buttonComponentPath);
    if (strpos($content, 'showAuditLog') !== false) {
        $success[] = "✅ Komponen audit-log-button tersedia";
    } else {
        $errors[] = "❌ Komponen audit-log-button tidak valid";
    }
} else {
    $errors[] = "❌ Komponen audit-log-button tidak ditemukan";
}

// 5. Cek komponen audit-log-modal
echo "5. 🔍 Checking Audit Log Modal Component...\n";
$modalComponentPath = __DIR__ . '/resources/views/components/audit-log-modal.blade.php';
if (file_exists($modalComponentPath)) {
    $content = file_get_contents($modalComponentPath);
    if (strpos($content, 'auditLogModal') !== false &&
        strpos($content, 'displayAuditLogs') !== false) {
        $success[] = "✅ Komponen audit-log-modal tersedia dengan JavaScript";
    } else {
        $errors[] = "❌ Komponen audit-log-modal tidak lengkap";
    }
} else {
    $errors[] = "❌ Komponen audit-log-modal tidak ditemukan";
}

// 6. Cek routes audit log
echo "6. 🔍 Checking Routes...\n";
$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $content = file_get_contents($routesPath);
    if (strpos($content, 'audit-logs') !== false &&
        strpos($content, 'AuditLogController') !== false) {
        $success[] = "✅ Routes audit log tersedia";

        if (strpos($content, 'audit-logs/model') !== false) {
            $success[] = "✅ Route AJAX audit-logs/model tersedia";
        } else {
            $warnings[] = "⚠️  Route AJAX audit-logs/model mungkin tidak tersedia";
        }
    } else {
        $errors[] = "❌ Routes audit log tidak ditemukan";
    }
} else {
    $errors[] = "❌ File routes/web.php tidak ditemukan";
}

// 7. Cek database migration
echo "7. 🔍 Checking Database Migration...\n";
$migrationDir = __DIR__ . '/database/migrations/';
$auditMigration = false;
if (is_dir($migrationDir)) {
    $files = scandir($migrationDir);
    foreach ($files as $file) {
        if (strpos($file, 'audit_logs') !== false || strpos($file, 'karyawan_audit') !== false) {
            $auditMigration = true;
            break;
        }
    }

    if ($auditMigration) {
        $success[] = "✅ Migration audit_logs ditemukan";
    } else {
        $errors[] = "❌ Migration audit_logs tidak ditemukan";
    }
} else {
    $errors[] = "❌ Directory migrations tidak ditemukan";
}

// 8. Cek menu audit log di sidebar
echo "8. 🔍 Checking Sidebar Menu...\n";
$layoutPath = __DIR__ . '/resources/views/layouts/app.blade.php';
if (file_exists($layoutPath)) {
    $content = file_get_contents($layoutPath);
    if (strpos($content, 'audit-logs.index') !== false) {
        $success[] = "✅ Menu Audit Log tersedia di sidebar";
    } else {
        $warnings[] = "⚠️  Menu Audit Log mungkin belum ditambahkan ke sidebar";
    }
} else {
    $warnings[] = "⚠️  Layout app.blade.php tidak ditemukan";
}

// 9. Cek halaman dashboard audit log
echo "9. 🔍 Checking Audit Log Dashboard...\n";
$dashboardPath = __DIR__ . '/resources/views/audit-logs/index.blade.php';
if (file_exists($dashboardPath)) {
    $content = file_get_contents($dashboardPath);
    if (strpos($content, 'auditLogs') !== false) {
        $success[] = "✅ Dashboard audit log tersedia";
    } else {
        $errors[] = "❌ Dashboard audit log tidak valid";
    }
} else {
    $errors[] = "❌ Dashboard audit log tidak ditemukan";
}

// 10. Cek model yang sudah menggunakan Auditable trait
echo "10. 🔍 Checking Models with Auditable Trait...\n";
$modelsWithAuditable = [];
$modelDir = __DIR__ . '/app/Models/';
if (is_dir($modelDir)) {
    $files = scandir($modelDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $filePath = $modelDir . $file;
            $content = file_get_contents($filePath);
            if (strpos($content, 'use Auditable') !== false ||
                strpos($content, 'App\\Traits\\Auditable') !== false) {
                $modelsWithAuditable[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
    }
}

if (count($modelsWithAuditable) > 0) {
    $success[] = "✅ " . count($modelsWithAuditable) . " model menggunakan Auditable trait: " .
                 implode(', ', array_slice($modelsWithAuditable, 0, 5)) .
                 (count($modelsWithAuditable) > 5 ? '...' : '');
} else {
    $warnings[] = "⚠️  Tidak ada model yang menggunakan Auditable trait";
}

// Tampilkan hasil
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 HASIL VERIFIKASI KOMPONEN AUDIT LOG\n";
echo str_repeat("=", 60) . "\n";

if (count($success) > 0) {
    echo "✅ KOMPONEN BERHASIL (" . count($success) . "):\n";
    foreach ($success as $item) {
        echo "   $item\n";
    }
}

if (count($warnings) > 0) {
    echo "\n⚠️  PERINGATAN (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "   $item\n";
    }
}

if (count($errors) > 0) {
    echo "\n❌ ERROR (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "   $item\n";
    }
}

// Status akhir
echo "\n📈 STATUS KELENGKAPAN:\n";
$totalChecks = 10;
$successChecks = count($success);
$completeness = round(($successChecks / $totalChecks) * 100, 1);

echo "- Komponen lengkap: $successChecks dari $totalChecks ($completeness%)\n";

if (count($errors) === 0) {
    echo "\n🎉 SEMUA KOMPONEN AUDIT LOG SUDAH SIAP!\n";
    echo "Anda dapat menjalankan script implementasi ke semua menu.\n";
} else {
    echo "\n⚠️  MASIH ADA KOMPONEN YANG KURANG!\n";
    echo "Perbaiki error di atas sebelum implementasi ke semua menu.\n";
}

echo "\n📋 LANGKAH SELANJUTNYA:\n";
if (count($errors) === 0) {
    echo "1. Jalankan backup: php backup_views_before_audit.php\n";
    echo "2. Jalankan implementasi: php implement_audit_log_all_menus.php\n";
    echo "3. Test audit log di beberapa halaman\n";
} else {
    echo "1. Perbaiki error yang ditemukan\n";
    echo "2. Jalankan verifikasi ini lagi\n";
    echo "3. Baru jalankan implementasi setelah semua OK\n";
}
