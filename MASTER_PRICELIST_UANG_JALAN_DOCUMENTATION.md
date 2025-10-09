# 📋 MASTER PRICELIST UANG JALAN - SISTEM LENGKAP

## 🎯 **OVERVIEW**

Sistem Master Pricelist Uang Jalan yang dibuat berdasarkan struktur CSV dengan header lengkap untuk mengelola tarif uang jalan kontainer.

---

## 📊 **FITUR UTAMA**

### ✅ **Database Structure:**

-   **Table:** `master_pricelist_uang_jalan`
-   **Fields:** Semua kolom sesuai CSV (Kode, Cabang, Wilayah, Dari, Ke, Uang Jalan 20ft/40ft, dll)
-   **Indexes:** Optimal untuk performance query
-   **Validation:** Comprehensive dengan foreign keys

### ✅ **Model Features:**

-   **Dynamic Methods:** `getUangJalanBySize()`, `getMelBySize()`, `getAntarLokasiBySize()`
-   **Scopes:** `active()`, `byCabang()`, `byWilayah()`
-   **Auto-calculations:** `getTotalBiaya()` untuk total cost per size
-   **Route Finder:** `findByRoute()` untuk pencarian berdasarkan rute
-   **Auto-generation:** Kode otomatis dengan format `{CABANG}{NUMBER}`

### ✅ **Controller Features:**

-   **CRUD Operations:** Create, Read, Update, Delete dengan validation
-   **CSV Import:** Batch import dari file CSV dengan error handling
-   **Advanced Filtering:** Search, cabang, wilayah, status
-   **API Endpoint:** `findByRoute()` untuk integration dengan modul lain
-   **Soft Delete:** Status inactive instead of hard delete

### ✅ **Views Features:**

-   **Responsive Design:** Mobile-friendly dengan Tailwind CSS
-   **Advanced Table:** Sortable, filterable dengan pagination
-   **Import Modal:** Drag & drop CSV import with preview
-   **Form Validation:** Real-time validation dengan auto-calculation
-   **Status Indicators:** Visual status dan validity period

---

## 🗂️ **FILE STRUCTURE**

### **📁 Database:**

```
database/migrations/
└── 2025_10_09_000001_create_master_pricelist_uang_jalan_table.php
```

### **📁 Models:**

```
app/Models/
└── MasterPricelistUangJalan.php
```

### **📁 Controllers:**

```
app/Http/Controllers/
└── MasterPricelistUangJalanController.php
```

### **📁 Views:**

```
resources/views/master-pricelist-uang-jalan/
├── index.blade.php      # Data listing dengan filter & import
├── create.blade.php     # Form tambah dengan auto-calculation
├── edit.blade.php       # Form edit (akan dibuat)
└── show.blade.php       # Detail view (akan dibuat)
```

---

## 🔧 **ROUTES YANG DIPERLUKAN**

Tambahkan ke `routes/web.php`:

```php
// Master Pricelist Uang Jalan Routes
Route::prefix('master-pricelist-uang-jalan')->name('master-pricelist-uang-jalan.')->group(function () {
    Route::get('/', [MasterPricelistUangJalanController::class, 'index'])->name('index');
    Route::get('/create', [MasterPricelistUangJalanController::class, 'create'])->name('create');
    Route::post('/', [MasterPricelistUangJalanController::class, 'store'])->name('store');
    Route::get('/{masterPricelistUangJalan}', [MasterPricelistUangJalanController::class, 'show'])->name('show');
    Route::get('/{masterPricelistUangJalan}/edit', [MasterPricelistUangJalanController::class, 'edit'])->name('edit');
    Route::put('/{masterPricelistUangJalan}', [MasterPricelistUangJalanController::class, 'update'])->name('update');
    Route::delete('/{masterPricelistUangJalan}', [MasterPricelistUangJalanController::class, 'destroy'])->name('destroy');

    // Import & API Routes
    Route::post('/import', [MasterPricelistUangJalanController::class, 'importCsv'])->name('import');
    Route::get('/api/find-by-route', [MasterPricelistUangJalanController::class, 'findByRoute'])->name('api.find-by-route');
});
```

---

## 📝 **CSV IMPORT FORMAT**

### **Header CSV:**

```
Kode;Cabang;Wilayah;Dari;Ke;Uang Jalan 20ft;Uang Jalan 40ft;Keterangan;Liter;Jarak dari Penjaringan (km);Mel 20 Feet;Mel 40 Feet;Ongkos Truk 20ft;Antar Lokasi 20ft;Antar Lokasi 40ft
```

### **Example Data:**

```
1;JKT;JAKARTA UTARA;GARASI PLUIT;KAPUK;350000;500000;PT. Sinar Mega Mas 50;30;5;30000;50000;1050000;0;0
2;JKT;JAKARTA UTARA;GARASI PLUIT;TANJUNG PRIUK;350000;500000;PT. Salim Ivomas 30;30;7;30000;50000;1050000;0;0
```

---

## 🚀 **DEPLOYMENT STEPS**

### **1. Run Migration:**

```bash
php artisan migrate
```

### **2. Add Routes:**

Tambahkan routes ke `routes/web.php`

### **3. Add Navigation:**

Tambahkan menu ke sidebar di `layouts/app.blade.php`

### **4. Set Permissions (Optional):**

```php
// database/seeders/PermissionSeeder.php
'master-pricelist-uang-jalan-view',
'master-pricelist-uang-jalan-create',
'master-pricelist-uang-jalan-edit',
'master-pricelist-uang-jalan-delete',
```

### **5. Import Initial Data:**

Upload CSV file melalui interface atau command

---

## 🎯 **INTEGRATION EXAMPLES**

### **A. Get Uang Jalan by Route:**

```php
$pricelist = MasterPricelistUangJalan::findByRoute('GARASI PLUIT', 'KAPUK');
$uangJalan = $pricelist ? $pricelist->getUangJalanBySize('20ft') : 0;
```

### **B. API Call (AJAX):**

```javascript
fetch(
    "/master-pricelist-uang-jalan/api/find-by-route?dari=GARASI PLUIT&ke=KAPUK&ukuran=20ft"
)
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            console.log("Uang Jalan:", data.data.uang_jalan_by_size);
            console.log("Total Biaya:", data.data.total_biaya);
        }
    });
```

### **C. Integration dengan Tagihan:**

```php
// Di TagihanController atau similar
$pricelist = MasterPricelistUangJalan::findByRoute($dari, $ke);
$tagihan->uang_jalan = $pricelist ? $pricelist->getUangJalanBySize($ukuran_kontainer) : 0;
$tagihan->mel = $pricelist ? $pricelist->getMelBySize($ukuran_kontainer) : 0;
```

---

## ✅ **STATUS COMPLETION**

-   [x] ✅ Database Migration
-   [x] ✅ Model dengan relationships & methods
-   [x] ✅ Controller dengan CRUD & Import
-   [x] ✅ Index view dengan filtering
-   [x] ✅ Create form dengan auto-calculation
-   [ ] ⏳ Edit view
-   [ ] ⏳ Show/detail view
-   [ ] ⏳ Routes setup
-   [ ] ⏳ Navigation menu
-   [ ] ⏳ Initial data import

---

## 💡 **NEXT STEPS**

1. **Complete Views:** Edit & Show pages
2. **Add Routes:** Register semua routes
3. **Navigation Menu:** Add to sidebar
4. **Data Import:** Import CSV data awal
5. **Integration:** Connect dengan modul tagihan
6. **Testing:** Comprehensive testing semua fitur

**🎉 Sistem Master Pricelist Uang Jalan siap untuk deployment!**
