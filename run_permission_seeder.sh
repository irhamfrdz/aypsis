#!/bin/bash
# 🔑 PANDUAN MENJALANKAN PERMISSION SEEDER DI SERVER
# =====================================================

echo "🚀 AYPSIS - PERMISSION SEEDER EXECUTION GUIDE"
echo "=============================================="

# ================================================================
# METODE 1: JALANKAN SEEDER LANGSUNG (RECOMMENDED)
# ================================================================
echo ""
echo "📋 METODE 1: Jalankan Seeder Langsung"
echo "====================================="
echo ""
echo "1️⃣ Masuk ke direktori aplikasi:"
echo "   cd /path/to/your/aypsis"
echo ""
echo "2️⃣ Pastikan aplikasi dalam maintenance mode:"
echo "   php artisan down --message='Installing Permissions' --retry=60"
echo ""
echo "3️⃣ Jalankan seeder permission:"
echo "   php artisan db:seed --class=ComprehensiveSystemPermissionSeeder"
echo ""
echo "4️⃣ Matikan maintenance mode:"
echo "   php artisan up"
echo ""

# ================================================================
# METODE 2: JALANKAN DENGAN PREVIEW DULU (SAFE MODE)
# ================================================================
echo "📋 METODE 2: Jalankan dengan Preview (Safe Mode)"
echo "==============================================="
echo ""
echo "1️⃣ Cek berapa permission yang akan dibuat:"
echo "   php artisan tinker"
echo "   >>> \$seeder = new \Database\Seeders\ComprehensiveSystemPermissionSeeder();"
echo "   >>> echo 'Total permissions: ' . \App\Models\Permission::count();"
echo "   >>> exit"
echo ""
echo "2️⃣ Jalankan seeder jika sudah yakin:"
echo "   php artisan db:seed --class=ComprehensiveSystemPermissionSeeder"
echo ""

# ================================================================
# METODE 3: BACKUP DULU SEBELUM JALANKAN (EXTRA SAFE)
# ================================================================
echo "📋 METODE 3: Backup Database Dulu (Extra Safe)"
echo "=============================================="
echo ""
echo "1️⃣ Backup database permissions:"
echo "   mysqldump -u [username] -p [database_name] permissions user_permissions > permissions_backup_\$(date +%Y%m%d_%H%M%S).sql"
echo ""
echo "2️⃣ Atau gunakan Laravel backup:"
echo "   php artisan backup:run --only-db"
echo ""
echo "3️⃣ Jalankan seeder:"
echo "   php artisan db:seed --class=ComprehensiveSystemPermissionSeeder"
echo ""

# ================================================================
# TROUBLESHOOTING
# ================================================================
echo "🔧 TROUBLESHOOTING"
echo "=================="
echo ""
echo "❌ Jika error 'Class not found':"
echo "   composer dump-autoload"
echo "   php artisan db:seed --class=ComprehensiveSystemPermissionSeeder"
echo ""
echo "❌ Jika error 'Permission already exists':"
echo "   Seeder sudah handle duplicate checking, tapi jika masih error:"
echo "   php artisan tinker"
echo "   >>> \App\Models\Permission::where('name', 'permission-name')->first()"
echo "   >>> exit"
echo ""
echo "❌ Jika ingin reset semua permissions (HATI-HATI!):"
echo "   php artisan tinker"
echo "   >>> \App\Models\Permission::truncate();"
echo "   >>> \DB::table('user_permissions')->truncate();"
echo "   >>> exit"
echo "   php artisan db:seed --class=ComprehensiveSystemPermissionSeeder"
echo ""

# ================================================================
# VERIFIKASI HASIL
# ================================================================
echo "✅ VERIFIKASI HASIL"
echo "=================="
echo ""
echo "1️⃣ Cek total permissions:"
echo "   php artisan tinker"
echo "   >>> echo 'Total permissions: ' . \App\Models\Permission::count();"
echo "   >>> exit"
echo ""
echo "2️⃣ Cek beberapa permission sample:"
echo "   php artisan tinker"
echo "   >>> \App\Models\Permission::whereIn('name', ['master-user-view', 'approval-dashboard', 'supir-dashboard'])->get(['name', 'description']);"
echo "   >>> exit"
echo ""
echo "3️⃣ Cek permission by category:"
echo "   php artisan tinker"
echo "   >>> \App\Models\Permission::where('name', 'like', 'master-%')->count();"
echo "   >>> \App\Models\Permission::where('name', 'like', 'approval-%')->count();"
echo "   >>> exit"
echo ""

# ================================================================
# COMMAND LENGKAP UNTUK COPY-PASTE
# ================================================================
echo "📋 COMMAND LENGKAP UNTUK COPY-PASTE"
echo "==================================="
echo ""
echo "# Masuk ke direktori aplikasi"
echo "cd /path/to/your/aypsis"
echo ""
echo "# Set maintenance mode"
echo "php artisan down --message='Installing Permissions' --retry=60"
echo ""
echo "# Pull latest changes"
echo "git pull origin main"
echo ""
echo "# Update dependencies"
echo "composer install --no-dev --optimize-autoloader"
echo ""
echo "# Run migrations if any"
echo "php artisan migrate --force"
echo ""
echo "# Clear caches"
echo "php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear"
echo ""
echo "# Run permission seeder"
echo "php artisan db:seed --class=ComprehensiveSystemPermissionSeeder"
echo ""
echo "# Disable maintenance mode"
echo "php artisan up"
echo ""
echo "# Verify results"
echo "php artisan tinker --execute=\"echo 'Total permissions: ' . \App\Models\Permission::count();\""
echo ""

echo "🎉 SEEDER EXECUTION GUIDE COMPLETED!"
echo "===================================="
echo "ℹ️  Seeder ini akan menambah 400+ permissions ke sistem Anda"
echo "ℹ️  Seeder otomatis cek duplicate dan hanya menambah yang baru"
echo "ℹ️  Jika ada pertanyaan, hubungi developer"
