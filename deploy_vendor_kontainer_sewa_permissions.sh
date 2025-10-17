#!/bin/bash

# Script untuk deploy permissions Vendor Kontainer Sewa ke server production
# Usage: ./deploy_vendor_kontainer_sewa_permissions.sh

echo "🚀 Deploy Permissions Vendor Kontainer Sewa ke Server Production"
echo "=================================================================="

# Check if we're in Laravel directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Script harus dijalankan dari root directory Laravel!"
    echo "   Pastikan file 'artisan' ada di direktori ini."
    exit 1
fi

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP tidak ditemukan di system!"
    echo "   Pastikan PHP sudah terinstall dan tersedia di PATH."
    exit 1
fi

# Check if permissions script exists
if [ ! -f "run_vendor_kontainer_sewa_permissions.php" ]; then
    echo "❌ Error: File 'run_vendor_kontainer_sewa_permissions.php' tidak ditemukan!"
    echo "   Pastikan file script sudah di-upload ke server."
    exit 1
fi

# Create backup of current permissions (optional)
echo "📦 Membuat backup permissions sebelum menjalankan script..."
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

\$timestamp = date('Y-m-d_H-i-s');
\$filename = \"permissions_backup_{\$timestamp}.json\";

\$permissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->join('users', 'user_permissions.user_id', '=', 'users.id')
    ->where('permissions.name', 'LIKE', 'vendor-kontainer-sewa-%')
    ->select('users.username', 'permissions.name', 'permissions.description')
    ->get();

file_put_contents(\$filename, json_encode(\$permissions, JSON_PRETTY_PRINT));
echo \"✅ Backup dibuat: {\$filename}\n\";
"

# Run the permissions script
echo ""
echo "🔧 Menjalankan script permissions..."
echo "-----------------------------------"

php run_vendor_kontainer_sewa_permissions.php

# Check exit code
if [ $? -eq 0 ]; then
    echo ""
    echo "🎉 Deploy permissions berhasil!"
    echo ""
    echo "✅ Langkah selanjutnya:"
    echo "   1. Login ke aplikasi sebagai admin"
    echo "   2. Cek menu 'Master Vendor Kontainer Sewa' di sidebar"
    echo "   3. Test CRUD operations"
    echo ""
    echo "🔗 URL: http://$(hostname -I | awk '{print $1}')/vendor-kontainer-sewa"
    echo ""
else
    echo ""
    echo "❌ Deploy permissions gagal!"
    echo "   Silakan cek log error di atas dan perbaiki masalahnya."
    echo ""
    exit 1
fi
