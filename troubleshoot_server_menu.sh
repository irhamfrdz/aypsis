#!/bin/bash
# 🚀 SERVER TROUBLESHOOTING SCRIPT
# Jalankan script ini di server untuk mendiagnosis masalah menu master data

echo "🔍 SERVER TROUBLESHOOTING: User Admin Menu Issue"
echo "================================================"

# 1. Check current directory
echo "📁 Current directory: $(pwd)"

# 2. Check if user_admin exists and has proper role
echo ""
echo "👤 1. Checking user_admin status..."
php check_server_user_admin.php

# 3. If role assignment is missing, fix it
echo ""
echo "🔧 2. Fixing role assignment if needed..."
php fix_user_admin_role.php

# 4. Clear all caches
echo ""
echo "🧹 3. Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Re-check user_admin status after fix
echo ""
echo "✅ 4. Re-checking user_admin status..."
php check_server_user_admin.php

# 6. Check if Role model and migrations are properly set up
echo ""
echo "📊 5. Checking Role model and migrations..."
php artisan migrate:status | grep -E "(role|permission)"

echo ""
echo "🎯 TROUBLESHOOTING COMPLETED!"
echo "============================="
echo "✅ If hasRole('admin') is now True, the issue should be fixed"
echo "✅ Try logging out and logging back in as user_admin"
echo "✅ Menu Master Data should now appear in sidebar"
echo ""
echo "If issue persists, check:"
echo "- Laravel logs: storage/logs/laravel.log"
echo "- Web server error logs"
echo "- Database connection and Role table existence"