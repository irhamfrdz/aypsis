# 🚀 PANDUAN DEPLOYMENT CSV TO PRANOTA IMPORT KE SERVER

## 📋 OVERVIEW

Untuk menjalankan import di server, Anda perlu copy file CSV dan menjalankan script di server production.

## 📁 FILES YANG PERLU DI-COPY KE SERVER

### ✅ File Wajib:

```bash
📄 Zona.csv                    # ← FILE INI WAJIB DI-COPY KE SERVER
🚀 import_csv_to_pranota.php   # ← Sudah ter-push via git
👁️ preview_csv_to_pranota.php  # ← Sudah ter-push via git
🎯 demo_invoice_grouping.php   # ← Sudah ter-push via git
```

### ❌ File yang TIDAK perlu copy:

-   Script PHP sudah ter-push via git commit
-   Hanya file `Zona.csv` yang perlu copy manual

---

## 🔧 STEP-BY-STEP DEPLOYMENT

### 1. **📤 Upload Zona.csv ke Server**

#### Option A: Via SCP/SFTP

```bash
# Upload via SCP (dari local ke server)
scp Zona.csv username@server-ip:/path/to/aypsis/

# Atau via SFTP
sftp username@server-ip
put Zona.csv /path/to/aypsis/
```

#### Option B: Via FTP Client (FileZilla, WinSCP)

```
1. Buka FileZilla/WinSCP
2. Connect ke server
3. Navigate ke folder aypsis di server
4. Upload file Zona.csv
```

#### Option C: Via cPanel File Manager

```
1. Login ke cPanel
2. Buka File Manager
3. Navigate ke folder aypsis
4. Upload Zona.csv
```

### 2. **📥 Pull Latest Code di Server**

```bash
# SSH ke server
ssh username@server-ip

# Navigate ke folder project
cd /path/to/aypsis

# Pull latest changes
git pull origin main

# Verify files ada
ls -la | grep -E "(import_csv|preview_csv|demo_invoice|Zona.csv)"
```

### 3. **🔍 Verify Setup di Server**

```bash
# Test database connection
php artisan tinker --execute="
try {
    \DB::connection()->getPdo();
    echo 'Database connected successfully!\n';
} catch(Exception \$e) {
    echo 'Database error: ' . \$e->getMessage() . \"\n\";
}
"

# Test models accessible
php artisan tinker --execute="
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\DaftarTagihanKontainerSewa;
echo 'Models loaded successfully!\n';
"

# Verify Zona.csv exists
ls -la Zona.csv
```

---

## 🚀 MENJALANKAN DI SERVER

### **🔍 Step 1: Preview di Server (RECOMMENDED)**

```bash
# SSH ke server
ssh username@server-ip
cd /path/to/aypsis

# Jalankan preview mode dulu
php preview_csv_to_pranota.php
```

### **🎯 Step 2: Demo Grouping di Server**

```bash
# Lihat bagaimana grouping akan bekerja
php demo_invoice_grouping.php
```

### **🚀 Step 3: Import Production di Server**

```bash
# Backup database dulu (RECOMMENDED)
mysqldump -u username -p database_name > backup_before_import_$(date +%Y%m%d_%H%M%S).sql

# Jalankan import sesungguhnya
php import_csv_to_pranota.php
# Script akan meminta konfirmasi: ketik "YES CREATE PRANOTA"
```

---

## 📊 VERIFICATION DI SERVER

### **✅ Cek Hasil Import:**

```bash
# Cek jumlah pranota ZONA yang dibuat
php artisan tinker --execute="
use App\Models\PranotaTagihanKontainerSewa;
\$count = PranotaTagihanKontainerSewa::where('no_pranota', 'LIKE', 'PRN-ZONA-%')->count();
echo 'Total Pranota ZONA: ' . \$count . \"\n\";

\$latest = PranotaTagihanKontainerSewa::where('no_pranota', 'LIKE', 'PRN-ZONA-%')->latest()->take(3)->get();
foreach(\$latest as \$p) {
    echo \$p->no_pranota . ' - Items: ' . count(\$p->tagihan_kontainer_sewa_ids) . ' - Total: Rp ' . number_format(\$p->grand_total) . \"\n\";
}
"

# Cek tagihan yang ter-link
php artisan tinker --execute="
use App\Models\DaftarTagihanKontainerSewa;
\$linked = DaftarTagihanKontainerSewa::whereNotNull('pranota_tagihan_kontainer_sewa_id')->count();
echo 'Tagihan linked to Pranota: ' . \$linked . \"\n\";
"
```

---

## 🔒 SECURITY & BACKUP

### **📁 Backup Sebelum Import:**

```bash
# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup project files (optional)
tar -czf aypsis_backup_$(date +%Y%m%d_%H%M%S).tar.gz /path/to/aypsis
```

### **🗑️ Cleanup Setelah Import (Optional):**

```bash
# Hapus file CSV setelah import berhasil (untuk security)
rm Zona.csv

# Atau pindah ke folder backup
mkdir -p backups
mv Zona.csv backups/Zona_imported_$(date +%Y%m%d_%H%M%S).csv
```

---

## 🚨 TROUBLESHOOTING DI SERVER

### **❌ Error: File not found**

```bash
# Cek apakah Zona.csv ada
ls -la | grep Zona
# Jika tidak ada, upload ulang file CSV
```

### **❌ Error: Permission denied**

```bash
# Set permission file CSV
chmod 644 Zona.csv

# Set permission script PHP
chmod 755 import_csv_to_pranota.php preview_csv_to_pranota.php demo_invoice_grouping.php
```

### **❌ Error: Memory limit**

```bash
# Jalankan dengan memory limit lebih besar
php -d memory_limit=512M import_csv_to_pranota.php
```

### **❌ Error: Database connection**

```bash
# Cek .env file di server
cat .env | grep -E "(DB_|APP_ENV)"

# Test connection
php artisan config:cache
php artisan config:clear
```

---

## 📞 QUICK DEPLOYMENT CHECKLIST

```
□ Upload Zona.csv ke server
□ SSH ke server
□ cd ke folder aypsis
□ git pull origin main
□ Verify file Zona.csv ada
□ Test: php preview_csv_to_pranota.php
□ Backup database
□ Run: php import_csv_to_pranota.php
□ Verify: Cek jumlah pranota dibuat
□ Cleanup: Remove/backup CSV file
```

---

## 💡 TIPS DEPLOYMENT

1. **🔍 Always Preview First:** Jalankan preview mode dulu di server
2. **💾 Backup Database:** Backup sebelum import production
3. **🔐 Secure Files:** Hapus CSV setelah import berhasil
4. **📊 Monitor Results:** Verify count dan financial data
5. **📝 Log Results:** Simpan log output untuk reference

**Expected Results di Server:**

-   **121 Pranota** dari **209 Tagihan**
-   **503 Rows** diskip (no bank info)
-   Format: `PRN-ZONA-20251008-0001` s/d `PRN-ZONA-20251008-0121`

---

**Created:** October 8, 2025  
**For Server:** Production deployment of CSV to Pranota import  
**Status:** ✅ Ready for deployment
