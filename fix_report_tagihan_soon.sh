#!/bin/bash
# 🚀 QUICK FIX: Report Tagihan "soon" Issue
# Jalankan command ini jika Report Tagihan masih menampilkan label "(soon)"

echo "🔧 QUICK FIX: Report Tagihan Menu Issue"
echo "========================================"
echo ""

# Diagnosis
echo "📋 Checking current status..."
echo ""

# 1. Check git status
echo "1️⃣  Git Status:"
cd /path/to/your/aypsis
git log --oneline -5

echo ""
echo "2️⃣  Latest commit should be: 'Remove soon label from Report Tagihan'"
echo ""

# 2. Pull latest changes
echo "3️⃣  Pulling latest changes..."
git pull origin main

echo ""

# 3. Clear Route Cache (MOST IMPORTANT!)
echo "4️⃣  Clearing route cache (CRITICAL STEP!)..."
php artisan route:clear

echo ""

# 4. Clear All Caches
echo "5️⃣  Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""

# 5. Verify routes exist
echo "6️⃣  Verifying Report Tagihan routes..."
php artisan route:list | grep "report.tagihan"

echo ""

# 6. Re-cache for performance (optional)
echo "7️⃣  Re-caching routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ QUICK FIX COMPLETED!"
echo "======================"
echo ""
echo "📝 What was fixed:"
echo "   ✓ Pulled latest code (removed 'soon' label)"
echo "   ✓ Cleared route cache (so Route::has() works correctly)"
echo "   ✓ Cleared view cache (so blade file updates)"
echo "   ✓ Verified routes are registered"
echo ""
echo "🌐 Please refresh your browser and check:"
echo "   → Report menu should show 'Report Tagihan' without '(soon)'"
echo "   → Clicking should go to /report/tagihan"
echo ""
echo "❌ If still showing 'soon', run these manual checks:"
echo "   1. Check Route::has('report.tagihan.index') returns true:"
echo "      php artisan tinker"
echo "      >>> Route::has('report.tagihan.index')"
echo ""
echo "   2. Check route is registered:"
echo "      php artisan route:list | grep tagihan"
echo ""
echo "   3. Hard refresh browser: Ctrl + Shift + R (Windows/Linux) or Cmd + Shift + R (Mac)"
echo ""
