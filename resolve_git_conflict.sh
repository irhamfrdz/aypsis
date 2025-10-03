#!/bin/bash
# Script untuk mengatasi konflik git saat pull di server
# Jalankan script ini di server untuk resolve conflict

echo "=== RESOLVING GIT CONFLICT ON SERVER ==="
echo "Date: $(date)"
echo ""

# Option 1: Stash local changes dan pull (RECOMMENDED)
echo "📋 OPTION 1: Stash and Pull (RECOMMENDED)"
echo "Ini akan menyimpan perubahan lokal Anda sementara, pull update, lalu apply kembali"
echo ""
echo "Commands:"
echo "git stash save 'Local server changes before pull'"
echo "git pull origin main"
echo "git stash pop"
echo ""

# Option 2: Backup file dan overwrite dengan versi baru
echo "📋 OPTION 2: Backup and Force Pull"
echo "Ini akan backup file lokal dan overwrite dengan versi dari GitHub"
echo ""
echo "Commands:"
echo "cp server_pull_commands.sh server_pull_commands.sh.backup"
echo "git checkout -- server_pull_commands.sh"
echo "git pull origin main"
echo "# Jika perlu, merge manual dari backup"
echo ""

# Option 3: Commit local changes dulu
echo "📋 OPTION 3: Commit Local Changes First"
echo "Ini akan commit perubahan lokal Anda dulu sebelum pull"
echo ""
echo "Commands:"
echo "git add server_pull_commands.sh"
echo "git commit -m 'Local server modifications'"
echo "git pull origin main --rebase"
echo ""

# Option 4: Reset hard (DANGEROUS - akan hilangkan perubahan lokal)
echo "📋 OPTION 4: Hard Reset (⚠️ DANGEROUS - loses local changes)"
echo "Ini akan MENGHAPUS semua perubahan lokal dan gunakan versi dari GitHub"
echo ""
echo "Commands:"
echo "git reset --hard origin/main"
echo "git pull origin main"
echo ""

echo "=== RECOMMENDED SOLUTION ==="
echo ""
echo "Jalankan perintah berikut di server:"
echo ""
echo "# 1. Lihat perubahan lokal yang ada"
echo "git diff server_pull_commands.sh"
echo ""
echo "# 2. Backup perubahan lokal"
echo "cp server_pull_commands.sh server_pull_commands.sh.local"
echo ""
echo "# 3. Stash perubahan lokal"
echo "git stash"
echo ""
echo "# 4. Pull update terbaru"
echo "git pull origin main"
echo ""
echo "# 5. Lihat perbedaan antara backup dan versi baru"
echo "diff -u server_pull_commands.sh.local server_pull_commands.sh"
echo ""
echo "# 6. Jika perlu, apply kembali perubahan penting dari backup"
echo "# (edit manual jika diperlukan)"
echo ""
echo "# 7. Hapus backup jika sudah tidak diperlukan"
echo "rm server_pull_commands.sh.local"
echo ""

# Auto-resolve script (interactive)
echo "=== AUTO-RESOLVE SCRIPT ==="
echo ""
echo "Atau jalankan auto-resolve berikut (akan prompt untuk konfirmasi):"
echo ""

cat << 'RESOLVE_SCRIPT'
#!/bin/bash
# Auto-resolve dengan backup

echo "Checking git status..."
git status

echo ""
read -p "Backup file lokal dan pull update? (y/n): " confirm

if [ "$confirm" = "y" ]; then
    echo "Creating backup..."
    cp server_pull_commands.sh server_pull_commands.sh.backup.$(date +%Y%m%d_%H%M%S)

    echo "Stashing local changes..."
    git stash save "Auto-backup before pull $(date +%Y%m%d_%H%M%S)"

    echo "Pulling updates..."
    git pull origin main

    echo ""
    echo "✅ Pull completed successfully!"
    echo ""
    echo "Backup file tersimpan di: server_pull_commands.sh.backup.*"
    echo "Perubahan lokal di-stash. Untuk restore: git stash pop"
    echo ""
    echo "List stash:"
    git stash list
else
    echo "Operation cancelled."
fi
RESOLVE_SCRIPT

echo ""
echo "Save script di atas sebagai resolve.sh dan jalankan dengan:"
echo "chmod +x resolve.sh && ./resolve.sh"
echo ""
echo "=== END OF CONFLICT RESOLUTION GUIDE ==="
