#!/bin/bash

# 📋 SCRIPT UPLOAD DAN UPDATE VENDOR CSV KE SERVER
# Gunakan script ini untuk mengupload file CSV dan menjalankan update vendor

echo "📋 VENDOR CSV UPDATE HELPER"
echo "=========================="

# Configuration - sesuaikan dengan server Anda
SERVER_USER="root"
SERVER_HOST="your-server-ip"
SERVER_PATH="/var/www/aypsis"
LOCAL_CSV_PATH="C:/Users/amanda/Downloads/Zona.csv"

# Fungsi untuk upload file
upload_csv() {
    echo "📤 1. Uploading CSV file to server..."

    if [ ! -f "$LOCAL_CSV_PATH" ]; then
        echo "❌ File tidak ditemukan: $LOCAL_CSV_PATH"
        echo "💡 Ubah LOCAL_CSV_PATH di script ini sesuai lokasi file Anda"
        exit 1
    fi

    echo "Uploading $LOCAL_CSV_PATH to $SERVER_USER@$SERVER_HOST:$SERVER_PATH/"
    scp "$LOCAL_CSV_PATH" "$SERVER_USER@$SERVER_HOST:$SERVER_PATH/"

    if [ $? -eq 0 ]; then
        echo "✅ File berhasil diupload"
    else
        echo "❌ Gagal upload file"
        exit 1
    fi
}

# Fungsi untuk backup data di server
backup_data() {
    echo "💾 2. Creating backup on server..."
    ssh "$SERVER_USER@$SERVER_HOST" "cd $SERVER_PATH && php backup_vendor_data.php"
}

# Fungsi untuk update vendor di server
update_vendor() {
    echo "🔄 3. Updating vendor data on server..."
    ssh "$SERVER_USER@$SERVER_HOST" "cd $SERVER_PATH && php artisan vendor:update-from-csv $SERVER_PATH/Zona.csv"
}

# Fungsi untuk menampilkan status
show_status() {
    echo "📊 4. Showing update status..."
    ssh "$SERVER_USER@$SERVER_HOST" "cd $SERVER_PATH && tail -20 storage/logs/laravel.log | grep -i vendor"
}

# Main execution
case "${1:-all}" in
    "upload")
        upload_csv
        ;;
    "backup")
        backup_data
        ;;
    "update")
        update_vendor
        ;;
    "status")
        show_status
        ;;
    "all")
        echo "🚀 Running complete vendor update process..."
        upload_csv
        backup_data
        update_vendor
        show_status
        echo "✅ Vendor update process completed!"
        ;;
    *)
        echo "Usage: $0 [upload|backup|update|status|all]"
        echo ""
        echo "Commands:"
        echo "  upload  - Upload CSV file to server"
        echo "  backup  - Create backup on server"
        echo "  update  - Update vendor data from CSV"
        echo "  status  - Show update status/logs"
        echo "  all     - Run complete process (default)"
        echo ""
        echo "Configuration (edit this script):"
        echo "  SERVER_USER: $SERVER_USER"
        echo "  SERVER_HOST: $SERVER_HOST"
        echo "  SERVER_PATH: $SERVER_PATH"
        echo "  LOCAL_CSV_PATH: $LOCAL_CSV_PATH"
        ;;
esac
