#!/bin/bash

echo "=== FIXING GIT PULL CONFLICT - Zona.csv already exists ==="

# Backup existing Zona.csv di server (jika ada)
if [ -f "Zona.csv" ]; then
    echo "📁 Backing up existing Zona.csv..."
    mv Zona.csv Zona_server_backup_$(date +%Y%m%d_%H%M%S).csv
    echo "✅ Existing Zona.csv backed up"
fi

# Pull latest changes
echo "📥 Pulling latest changes from git..."
git pull origin main

echo "✅ Git pull completed successfully!"

# Show files
echo "📋 Current files:"
ls -la | grep -E "(import_csv|preview_csv|demo_invoice|Zona)"

echo ""
echo "🔍 Next steps:"
echo "1. Upload your Zona.csv file to this directory"
echo "2. Run: php preview_csv_to_pranota.php"
echo "3. Run: php import_csv_to_pranota.php"
