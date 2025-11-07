<?php

// Script untuk membuat permissions via artisan tinker
$permissions = [
    // Authentication & Core
    'login', 'logout', 'dashboard', 'dashboard-view', 'dashboard-admin', 'dashboard-operational',
    
    // Master Data Management
    'master',
    
    // User Management
    'master-user-view', 'master-user-create', 'master-user-update', 'master-user-delete',
    'master-user-bulk-manage', 'master-user-print', 'master-user-export', 'master-user-import',
    
    // Karyawan Management
    'master-karyawan-view', 'master-karyawan-create', 'master-karyawan-update', 'master-karyawan-delete',
    'master-karyawan-print', 'master-karyawan-export', 'master-karyawan-import', 'master-karyawan-crew-checklist',
    
    // Kontainer Management
    'master-kontainer-view', 'master-kontainer-create', 'master-kontainer-update', 'master-kontainer-delete',
    'master-kontainer-print', 'master-kontainer-export', 'master-kontainer-import',
    
    // Tujuan Management
    'master-tujuan-view', 'master-tujuan-create', 'master-tujuan-update', 'master-tujuan-delete',
    'master-tujuan-print', 'master-tujuan-export',
    
    // Kegiatan Management
    'master-kegiatan-view', 'master-kegiatan-create', 'master-kegiatan-update', 'master-kegiatan-delete',
    'master-kegiatan-print', 'master-kegiatan-export',
    
    // Permission Management
    'master-permission-view', 'master-permission-create', 'master-permission-update', 'master-permission-delete',
    'master-permission-print', 'master-permission-export',
    
    // Mobil Management
    'master-mobil-view', 'master-mobil-create', 'master-mobil-update', 'master-mobil-delete',
    'master-mobil-print', 'master-mobil-export',
    
    // Bank Management
    'master-bank-view', 'master-bank-create', 'master-bank-update', 'master-bank-delete',
    'master-bank-destroy', 'master-bank-print', 'master-bank-export',
    
    // Divisi Management
    'master-divisi-view', 'master-divisi-create', 'master-divisi-update', 'master-divisi-delete',
    'master-divisi-print', 'master-divisi-export',
    
    // Pajak Management
    'master-pajak-view', 'master-pajak-create', 'master-pajak-update', 'master-pajak-delete',
    'master-pajak-destroy', 'master-pajak-print', 'master-pajak-export',
    
    // Cabang Management
    'master-cabang-view', 'master-cabang-create', 'master-cabang-update', 'master-cabang-delete',
    'master-cabang-print', 'master-cabang-export',
    
    // COA Management
    'master-coa-view', 'master-coa-create', 'master-coa-update', 'master-coa-delete',
    'master-coa-print', 'master-coa-export',
    
    // Pekerjaan Management
    'master-pekerjaan-view', 'master-pekerjaan-create', 'master-pekerjaan-update', 'master-pekerjaan-delete',
    'master-pekerjaan-destroy', 'master-pekerjaan-print', 'master-pekerjaan-export',
    
    // Vendor Bengkel Management
    'master-vendor-bengkel-view', 'master-vendor-bengkel-create', 'master-vendor-bengkel-update', 'master-vendor-bengkel-delete',
    'master-vendor-bengkel-print', 'master-vendor-bengkel-export', 'master-vendor-bengkel',
    'master-vendor-bengkel.view', 'master-vendor-bengkel.create', 'master-vendor-bengkel.update', 'master-vendor-bengkel.delete',
    
    // Kode Nomor Management
    'master-kode-nomor-view', 'master-kode-nomor-create', 'master-kode-nomor-update', 'master-kode-nomor-delete',
    'master-kode-nomor-print', 'master-kode-nomor-export', 'master-kode-nomor',
    
    // Stock Kontainer Management
    'master-stock-kontainer-view', 'master-stock-kontainer-create', 'master-stock-kontainer-update', 'master-stock-kontainer-delete',
    'master-stock-kontainer-print', 'master-stock-kontainer-export', 'master-stock-kontainer',
    
    // Kapal Management
    'master-kapal-view', 'master-kapal-create', 'master-kapal-edit', 'master-kapal-delete',
    'master-kapal-print', 'master-kapal-export', 'master-kapal',
    'master-kapal.view', 'master-kapal.create', 'master-kapal.edit', 'master-kapal.delete',
    
    // Pelabuhan Management
    'master-pelabuhan-view', 'master-pelabuhan-create', 'master-pelabuhan-edit', 'master-pelabuhan-update', 'master-pelabuhan-delete',
    
    // Tipe Akun Management
    'master-tipe-akun-view', 'master-tipe-akun-create', 'master-tipe-akun-update', 'master-tipe-akun-delete',
    'master-tipe-akun-destroy', 'master-tipe-akun-print', 'master-tipe-akun-export', 'master-tipe-akun',
    
    // Nomor Terakhir Management
    'master-nomor-terakhir-view', 'master-nomor-terakhir-create', 'master-nomor-terakhir-update', 'master-nomor-terakhir-delete',
    'master-nomor-terakhir-print', 'master-nomor-terakhir-export', 'master-nomor-terakhir',
    
    // Pengirim Management
    'master-pengirim-view', 'master-pengirim-create', 'master-pengirim-update', 'master-pengirim-delete',
    
    // Jenis Barang Management
    'master-jenis-barang-view', 'master-jenis-barang-create', 'master-jenis-barang-update', 'master-jenis-barang-delete',
    
    // Term Management
    'master-term-view', 'master-term-create', 'master-term-update', 'master-term-delete',
    
    // Tujuan Kirim Management
    'master-tujuan-kirim-view', 'master-tujuan-kirim-create', 'master-tujuan-kirim-update', 'master-tujuan-kirim-delete',
    
    // Vendor Kontainer Sewa Management
    'vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-update',
    'vendor-kontainer-sewa-delete', 'vendor-kontainer-sewa-export', 'vendor-kontainer-sewa-print',
    
    // Pergerakan Kapal Management
    'pergerakan-kapal-view', 'pergerakan-kapal-create', 'pergerakan-kapal-update', 'pergerakan-kapal-delete',
    
    // Pricelist Management
    'master-pricelist-sewa-kontainer-view', 'master-pricelist-sewa-kontainer-create', 'master-pricelist-sewa-kontainer-update',
    'master-pricelist-sewa-kontainer-delete', 'master-pricelist-sewa-kontainer-print', 'master-pricelist-sewa-kontainer-export',
    'master-pricelist-cat-view', 'master-pricelist-cat-create', 'master-pricelist-cat-update', 'master-pricelist-cat-delete',
    'master-pricelist-cat-print', 'master-pricelist-cat-export', 'master-pricelist-cat',
    'master-pricelist-gate-in-view', 'master-pricelist-gate-in-create', 'master-pricelist-gate-in-update', 'master-pricelist-gate-in-delete',
    
    // Uang Jalan Management
    'uang-jalan-view', 'uang-jalan-create', 'uang-jalan-update', 'uang-jalan-delete',
    'uang-jalan-approve', 'uang-jalan-print', 'uang-jalan-export',
    'uang-jalan-batam.view', 'uang-jalan-batam.create', 'uang-jalan-batam.edit', 'uang-jalan-batam.delete',
    
    // Order Management
    'order-view', 'order-create', 'order-update', 'order-delete', 'order-print', 'order-export',
    
    // Surat Jalan Management
    'surat-jalan-view', 'surat-jalan-create', 'surat-jalan-update', 'surat-jalan-delete',
    'surat-jalan-print', 'surat-jalan-export', 'surat-jalan-bongkaran-view', 'surat-jalan-bongkaran-create',
    'surat-jalan-bongkaran-update', 'surat-jalan-bongkaran-delete',
    
    // Pranota Management
    'pranota-view', 'pranota-create', 'pranota-update', 'pranota-delete', 'pranota-print', 'pranota-export', 'pranota-approve', 'pranota',
    'pranota-supir-view', 'pranota-supir-create', 'pranota-supir-update', 'pranota-supir-delete', 'pranota-supir-print',
    'pranota-uang-jalan-view', 'pranota-uang-jalan-create', 'pranota-uang-jalan-update', 'pranota-uang-jalan-delete',
    'pranota-uang-jalan-approve', 'pranota-uang-jalan-print', 'pranota-uang-jalan-export',
    'pranota-uang-rit-view', 'pranota-uang-rit-create', 'pranota-uang-rit-update', 'pranota-uang-rit-delete',
    'pranota-uang-rit-approve', 'pranota-uang-rit-mark-paid',
    'pranota-uang-kenek-view', 'pranota-uang-kenek-create', 'pranota-uang-kenek-update', 'pranota-uang-kenek-delete',
    'pranota-uang-kenek-approve', 'pranota-uang-kenek-mark-paid',
    'pranota-cat-view', 'pranota-cat-create', 'pranota-cat-update', 'pranota-cat-delete', 'pranota-cat-print', 'pranota-cat-export', 'pranota-cat',
    'pranota-kontainer-sewa-view', 'pranota-kontainer-sewa-create', 'pranota-kontainer-sewa-edit', 'pranota-kontainer-sewa-update',
    'pranota-kontainer-sewa-delete', 'pranota-kontainer-sewa-print', 'pranota-kontainer-sewa-export', 'pranota-kontainer-sewa',
    'pranota-perbaikan-kontainer-view', 'pranota-perbaikan-kontainer-create', 'pranota-perbaikan-kontainer-update',
    'pranota-perbaikan-kontainer-delete', 'pranota-perbaikan-kontainer-print', 'pranota-perbaikan-kontainer-export', 'pranota-perbaikan-kontainer',
    
    // Pembayaran Management
    'pembayaran-pranota-supir-view', 'pembayaran-pranota-supir-create', 'pembayaran-pranota-supir-update',
    'pembayaran-pranota-supir-delete', 'pembayaran-pranota-supir-print',
    'pembayaran-pranota-kontainer-view', 'pembayaran-pranota-kontainer-create', 'pembayaran-pranota-kontainer-update',
    'pembayaran-pranota-kontainer-delete', 'pembayaran-pranota-kontainer-print', 'pembayaran-pranota-kontainer-export',
    'pembayaran-pranota-cat-view', 'pembayaran-pranota-cat-create', 'pembayaran-pranota-cat-update',
    'pembayaran-pranota-cat-delete', 'pembayaran-pranota-cat-print', 'pembayaran-pranota-cat-export',
    'pembayaran-pranota-perbaikan-kontainer-view', 'pembayaran-pranota-perbaikan-kontainer-create',
    'pembayaran-pranota-perbaikan-kontainer-update', 'pembayaran-pranota-perbaikan-kontainer-delete',
    'pembayaran-pranota-perbaikan-kontainer-print', 'pembayaran-pranota-perbaikan-kontainer-export',
    'pembayaran-pranota-surat-jalan-view', 'pembayaran-pranota-surat-jalan-create', 'pembayaran-pranota-surat-jalan-edit',
    'pembayaran-pranota-surat-jalan-delete', 'pembayaran-pranota-surat-jalan-approve', 'pembayaran-pranota-surat-jalan-print',
    'pembayaran-pranota-surat-jalan-export',
    'pembayaran-pranota-uang-jalan-view', 'pembayaran-pranota-uang-jalan-create', 'pembayaran-pranota-uang-jalan-edit',
    'pembayaran-pranota-uang-jalan-delete',
    'pembayaran-aktivitas-lainnya-view', 'pembayaran-aktivitas-lainnya-create', 'pembayaran-aktivitas-lainnya-update',
    'pembayaran-aktivitas-lainnya-delete', 'pembayaran-aktivitas-lainnya-export', 'pembayaran-aktivitas-lainnya-print',
    'pembayaran-aktivitas-lainnya-approve', 'pembayaran-aktivitas-lainnya-reject', 'pembayaran-aktivitas-lainnya-generate-nomor',
    'pembayaran-aktivitas-lainnya-payment-form',
    'pembayaran-uang-muka-view', 'pembayaran-uang-muka-create', 'pembayaran-uang-muka-edit', 'pembayaran-uang-muka-update',
    'pembayaran-uang-muka-delete', 'pembayaran-uang-muka-print',
    'pembayaran-ob-view', 'pembayaran-ob-create', 'pembayaran-ob-edit', 'pembayaran-ob-update',
    'pembayaran-ob-delete', 'pembayaran-ob-print',
    'realisasi-uang-muka-view', 'realisasi-uang-muka-create', 'realisasi-uang-muka-edit', 'realisasi-uang-muka-update',
    'realisasi-uang-muka-delete', 'realisasi-uang-muka-print',
    
    // Tanda Terima Management
    'tanda-terima-view', 'tanda-terima-create', 'tanda-terima-update', 'tanda-terima-edit', 'tanda-terima-delete',
    'tanda-terima-print', 'tanda-terima-export',
    'tanda-terima-tanpa-surat-jalan-view', 'tanda-terima-tanpa-surat-jalan-create', 'tanda-terima-tanpa-surat-jalan-update',
    'tanda-terima-tanpa-surat-jalan-delete',
    
    // Gate In Management
    'gate-in-view', 'gate-in-create', 'gate-in-update', 'gate-in-delete', 'gate-in-print', 'gate-in-export',
    
    // Tagihan Management
    'tagihan-cat-view', 'tagihan-cat-create', 'tagihan-cat-update', 'tagihan-cat-delete',
    'tagihan-cat-print', 'tagihan-cat-export', 'tagihan-cat-approve', 'tagihan-cat',
    'tagihan-kontainer-view', 'tagihan-kontainer-print', 'tagihan-kontainer-export', 'tagihan-kontainer-sewa',
    'tagihan-kontainer-sewa-view', 'tagihan-kontainer-sewa-create', 'tagihan-kontainer-sewa-update', 'tagihan-kontainer-sewa-delete',
    'tagihan-kontainer-sewa-print', 'tagihan-kontainer-sewa-index', 'tagihan-kontainer-sewa-destroy',
    'tagihan-perbaikan-kontainer-view', 'tagihan-perbaikan-kontainer-create', 'tagihan-perbaikan-kontainer-update',
    'tagihan-perbaikan-kontainer-delete', 'tagihan-perbaikan-kontainer-print', 'tagihan-perbaikan-kontainer-export',
    'tagihan-perbaikan-kontainer-approve',
    
    // Perbaikan Kontainer Management
    'perbaikan-kontainer-view', 'perbaikan-kontainer-create', 'perbaikan-kontainer-update', 'perbaikan-kontainer-delete',
    'perbaikan-kontainer-print', 'perbaikan-kontainer-export',
    'perbaikan-kontainer.view', 'perbaikan-kontainer.create', 'perbaikan-kontainer.update', 'perbaikan-kontainer.delete',
    
    // Aktivitas Lainnya Management
    'aktivitas-lainnya-view', 'aktivitas-lainnya-create', 'aktivitas-lainnya-update', 'aktivitas-lainnya-delete', 'aktivitas-lainnya-approve',
    
    // Supir & Driver Management
    'supir', 'supir-view', 'supir-create', 'supir-update', 'supir-delete', 'supir-checkpoint', 'supir-dashboard-view',
    'checkpoint-create', 'checkpoint-update',
    
    // Approval System
    'approval', 'approval-view', 'approval-update', 'approval-delete', 'approval-approve', 'approval-print', 'approval-export',
    'approval-dashboard', 'approval-surat-jalan-view', 'approval-surat-jalan-approve',
    'approval-tugas-1', 'approval-tugas-1.view', 'approval-tugas-1.approve',
    'approval-tugas-2', 'approval-tugas-2.view', 'approval-tugas-2.approve',
    'surat-jalan-approval-dashboard',
    
    // User Approval
    'user-approval-view', 'user-approval-create', 'user-approval-update', 'user-approval-delete',
    'user-approval-print', 'user-approval-export', 'user-approval-approve', 'user-approval-reject',
    
    // Profile Management
    'profile-view', 'profile-update', 'profile-delete',
    'profile.show', 'profile.edit', 'profile.update', 'profile.destroy',
    
    // Admin Features
    'admin-view', 'admin-create', 'admin-update', 'admin-delete', 'admin-debug',
    'admin.debug', 'admin.features', 'admin.user-approval', 'admin.user-approval.create',
    'admin.user-approval.update', 'admin.user-approval.delete',
    
    // Permohonan Management
    'permohonan', 'permohonan-memo-view', 'permohonan-memo-create', 'permohonan-memo-update',
    'permohonan-memo-delete', 'permohonan-memo-print', 'permohonan-memo-export', 'permohonan-memo',
    
    // BL Management
    'bl-view', 'bl-create', 'bl-edit', 'bl-update', 'bl-delete',
    
    // Prospek Management
    'prospek-view', 'prospek-edit', 'prospek-kapal-view', 'prospek-kapal-create', 'prospek-kapal-update', 'prospek-kapal-delete',
    
    // Audit & Reports
    'audit-logs-view', 'audit-logs-export', 'audit-log-view', 'audit-log-export',
    'report-tagihan-view', 'report-tagihan-export', 'report-pembayaran-view', 'report-pembayaran-export', 'report-pembayaran-print',
    
    // System Access
    'access-admin-panel', 'manage-system-settings', 'bulk-operations', 'import-export-data',
];

echo "=== MEMBUAT PERMISSIONS DAN ASSIGN KE ADMIN ===\n\n";

// Generate script untuk tinker
echo "\n// Copy-paste script berikut ke dalam artisan tinker:\n\n";
echo "\$permissions = [\n";
foreach (array_chunk($permissions, 10) as $chunk) {
    echo "    '" . implode("', '", $chunk) . "',\n";
}
echo "];\n\n";

echo "\$createdCount = 0;\n";
echo "\$existingCount = 0;\n\n";

echo "foreach (\$permissions as \$permission) {\n";
echo "    \$existing = \\Spatie\\Permission\\Models\\Permission::where('name', \$permission)->first();\n";
echo "    if (!\$existing) {\n";
echo "        \\Spatie\\Permission\\Models\\Permission::create(['name' => \$permission, 'guard_name' => 'web']);\n";
echo "        \$createdCount++;\n";
echo "        echo \"Created: {\$permission}\\n\";\n";
echo "    } else {\n";
echo "        \$existingCount++;\n";
echo "        echo \"Exists: {\$permission}\\n\";\n";
echo "    }\n";
echo "}\n\n";

echo "echo \"\\nPermissions created: {\$createdCount}\";\n";
echo "echo \"\\nPermissions existed: {\$existingCount}\";\n\n";

echo "// Assign semua permissions ke admin\n";
echo "\$adminUsers = \\App\\Models\\User::where('email', 'LIKE', '%admin%')->orWhere('name', 'LIKE', '%admin%')->orWhere('id', 1)->get();\n";
echo "foreach (\$adminUsers as \$admin) {\n";
echo "    \$allPermissions = \\Spatie\\Permission\\Models\\Permission::all();\n";
echo "    \$admin->syncPermissions(\$allPermissions);\n";
echo "    echo \"\\nAssigned {\$allPermissions->count()} permissions to {\$admin->name}\";\n";
echo "}\n\n";

echo "echo \"\\n\\n=== SELESAI ===\";\n";
echo "echo \"\\nTotal permissions: \" . \\Spatie\\Permission\\Models\\Permission::count();\n";
echo "echo \"\\nAdmin users dengan full permissions:\";\n";
echo "foreach (\$adminUsers as \$admin) {\n";
echo "    echo \"\\n- {\$admin->name} ({\$admin->email}): \" . \$admin->permissions->count() . \" permissions\";\n";
echo "}\n";

echo "\n\n=== INSTRUKSI ===\n";
echo "1. Jalankan command: php artisan tinker\n";
echo "2. Copy-paste script di atas\n";
echo "3. Tekan Enter untuk menjalankan\n";
echo "4. Admin akan mendapatkan semua permissions\n\n";