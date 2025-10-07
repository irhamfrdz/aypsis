#!/bin/bash

# Server Migration Fix Commands
echo "=== Server Migration Fix Commands ==="

echo "1. Rollback migration yang gagal:"
echo "php artisan migrate:rollback --step=1"

echo ""
echo "2. Jalankan ulang migration yang sudah diperbaiki:"
echo "php artisan migrate"

echo ""
echo "3. Jika masih ada masalah, cek struktur tabel:"
echo "php artisan tinker --execute=\"echo 'Columns in stock_kontainers: '; print_r(DB::select('DESCRIBE stock_kontainers'));\""

echo ""
echo "4. Alternatif - Reset migration untuk file ini saja:"
echo "php artisan migrate:reset --path=database/migrations/2025_10_07_154324_finalize_stock_kontainers_structure.php"

echo ""
echo "5. Lalu jalankan lagi:"
echo "php artisan migrate --path=database/migrations/2025_10_07_154324_finalize_stock_kontainers_structure.php"

echo ""
echo "=== Troubleshooting ==="
echo "Jika masih error, jalankan query manual:"
echo "ALTER TABLE stock_kontainers ADD COLUMN awalan_kontainer VARCHAR(10) NULL AFTER keterangan;"
echo "ALTER TABLE stock_kontainers ADD COLUMN nomor_seri_kontainer VARCHAR(20) NULL AFTER awalan_kontainer;"
echo "ALTER TABLE stock_kontainers ADD COLUMN akhiran_kontainer VARCHAR(5) NULL AFTER nomor_seri_kontainer;"
echo "ALTER TABLE stock_kontainers ADD COLUMN nomor_seri_gabungan VARCHAR(50) NULL AFTER akhiran_kontainer;"
echo "ALTER TABLE stock_kontainers ADD UNIQUE INDEX stock_kontainers_nomor_seri_gabungan_unique (nomor_seri_gabungan);"

echo ""
echo "=== Verification ==="
echo "Setelah selesai, verifikasi dengan:"
echo "php artisan tinker --execute=\"echo 'StockKontainer model test: '; echo App\\Models\\StockKontainer::count();\""