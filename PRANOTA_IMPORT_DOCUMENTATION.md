# PRANOTA IMPORT DOCUMENTATION

## 📋 Overview
Script untuk import data CSV ZONA ke sistem pranota berdasarkan kriteria:

### ✅ Kriteria Import:
1. **Grouping**: Data dikelompokkan berdasarkan `No.InvoiceVendor` yang sama
2. **Filter Bank**: Hanya data yang memiliki `No.Bank` yang akan diproses  
3. **Tanggal Pranota**: Menggunakan `Tgl.Bank` sebagai tanggal pranota
4. **Auto Assignment**: Tagihan otomatis di-assign ke pranota yang sesuai

### 📊 Preview Results:
- **Total CSV Rows**: 711 rows
- **Valid Data**: 209 rows (dengan invoice & bank lengkap)
- **Skipped**: 502 rows (391 tanpa bank info, 111 tanpa invoice)
- **Pranota to Create**: 121 pranota groups
- **Total Value**: Rp 236,702,670

### 📁 File Structure:
```
Zona.csv (Input File)
├── Group;Kontainer;Awal;Akhir;Ukuran;Harga;Periode;Status;...
├── No.InvoiceVendor (Column 18) - Grouping Key
├── No.Bank (Column 20) - Required Filter  
└── Tgl.Bank (Column 21) - Pranota Date
```

### 🏗️ Database Structure:
**pranota_tagihan_kontainer_sewa**:
- `no_invoice`: Generated PRN-ZONA-YYYYMMDD-XXXX
- `tanggal_pranota`: From Tgl.Bank
- `no_invoice_vendor`: From CSV Column 18  
- `tgl_invoice_vendor`: From CSV Column 19
- `total_amount`: Sum of grand_total per group
- `tagihan_kontainer_sewa_ids`: Array of related tagihan IDs
- `status`: 'draft'

**daftar_tagihan_kontainer_sewa**:
- All tagihan fields with adjustment values preserved
- `status_pranota`: 'included'
- `pranota_id`: Reference to created pranota

### 🎯 Sample Grouping:
**Invoice ZONA24.03.22929** → **1 Pranota** with:
- 4 tagihan items (Z16 containers)
- Bank: EBK240500209 (20 May 24)
- Total DPP: Rp 5,045,044
- Total Adjustment: Rp -1,513,512
- Net Amount: Rp 3,531,532

### ▶️ Execution Commands:
```bash
# 1. Preview first (safe)
php preview_csv_to_pranota.php

# 2. Run actual import 
php import_csv_to_pranota.php

# 3. Verify results
php -r "
use App\Models\PranotaTagihanKontainerSewa;
echo 'Created Pranota: ' . PranotaTagihanKontainerSewa::count() . '\n';
"
```

### 🔒 Safety Features:
- Transaction rollback on errors
- Confirmation prompt before import
- Detailed logging and error reporting  
- Backup creation with timestamp
- Validation of required fields

### 📈 Expected Outcomes:
- ✅ 121 new pranota records
- ✅ 209 tagihan records with pranota assignment
- ✅ Proper financial calculations with adjustments
- ✅ Bank information properly mapped
- ✅ Date parsing for various formats

---
*Generated: 2025-10-08*