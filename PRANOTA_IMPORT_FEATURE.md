# Fitur Import Pranota Kontainer Sewa

## Deskripsi

Fitur ini memungkinkan pengguna untuk mengimport pranota kontainer sewa secara bulk menggunakan file CSV. Ketika nomor kontainer yang ada di file CSV cocok dengan data di tabel `daftar_tagihan_kontainer_sewa`, sistem akan:

1. Membuat pranota baru untuk setiap kontainer
2. Mengubah status tagihan menjadi "masuk pranota" (status_pranota = 'included')
3. Generate nomor pranota otomatis dengan format PMS

## File yang Ditambahkan/Dimodifikasi

### 1. Controller

**File:** `app/Http/Controllers/PranotaTagihanKontainerSewaController.php`

**Method Baru:**

-   `importPage()` - Menampilkan halaman import
-   `downloadTemplateCsv()` - Download template CSV untuk import
-   `importCsv()` - Proses import data dari CSV

### 2. Routes

**File:** `routes/web.php`

**Routes Baru:**

```php
Route::get('pranota-kontainer-sewa/import', [PranotaTagihanKontainerSewaController::class, 'importPage'])
     ->name('pranota-kontainer-sewa.import')
     ->middleware('can:pranota-kontainer-sewa-create');

Route::post('pranota-kontainer-sewa/import', [PranotaTagihanKontainerSewaController::class, 'importCsv'])
     ->name('pranota-kontainer-sewa.import.process')
     ->middleware('can:pranota-kontainer-sewa-create');

Route::get('pranota-kontainer-sewa/template/csv', [PranotaTagihanKontainerSewaController::class, 'downloadTemplateCsv'])
     ->name('pranota-kontainer-sewa.template.csv')
     ->middleware('can:pranota-kontainer-sewa-create');
```

### 3. Views

**File Baru:** `resources/views/pranota/import.blade.php`

-   Halaman import dengan instruksi lengkap
-   Form upload CSV
-   Download template CSV
-   Display hasil import dengan detail

**File Dimodifikasi:** `resources/views/pranota/index.blade.php`

-   Menambahkan tombol "Import Pranota" di header

## Format CSV

### Konsep Import:

**1 Pranota = Beberapa kontainer dengan Group dan Periode yang sama**

Sistem akan mengelompokkan kontainer berdasarkan kombinasi `group` dan `periode`, kemudian membuat 1 pranota untuk setiap grup tersebut.

### Kolom Required:

-   `group` atau `Group` - Nomor group kontainer
-   `periode` atau `Periode` - Nomor periode kontainer
-   `nomor_kontainer` atau `Nomor Kontainer` - Nomor kontainer yang ada di tagihan kontainer sewa (auto-detected dari export file)

### Kolom Optional:

-   `keterangan` - Keterangan pranota untuk group ini (default: "Pranota Group {group} Periode {periode} - {jumlah} kontainer (Import)")
-   `due_date` - Tanggal jatuh tempo format YYYY-MM-DD (default: 30 hari dari sekarang)

### Format 1: Template Simple (comma-separated)

```csv
group,periode,keterangan,due_date
1,1,Pranota Group 1 Periode 1,2025-11-01
2,1,Pranota Group 2 Periode 1,2025-11-15
1,2,Pranota Group 1 Periode 2,2025-12-01
```

### Format 2: Export File (semicolon-separated) ✅ Recommended

Anda dapat langsung menggunakan file hasil export dari "Daftar Tagihan Kontainer Sewa":

```csv
Group;Vendor;Nomor Kontainer;Size;Tanggal Awal;Tanggal Akhir;Periode;...
1;DPE;CBHU3952697;20;24-01-2025;23-02-2025;1;...
1;DPE;CBHU4077764;20;21-01-2025;20-02-2025;1;...
1;DPE;CBHU5876322;20;22-01-2025;21-02-2025;1;...
2;DPE;CBHU5911444;20;24-02-2025;23-03-2025;1;...
```

**Result:**

-   Group 1 Periode 1 → 1 Pranota dengan 3 kontainer (CBHU3952697, CBHU4077764, CBHU5876322)
-   Group 2 Periode 1 → 1 Pranota dengan 1 kontainer (CBHU5911444)

## Alur Proses Import

1. **User mengakses halaman import:**

    - URL: `/pranota-kontainer-sewa/import`
    - Download template CSV atau export data dari "Daftar Tagihan Kontainer Sewa"

2. **User mengisi/menyiapkan file CSV:**

    - **Opsi 1:** Download template dan isi kolom `group`, `periode`
    - **Opsi 2:** Export file dari menu tagihan (sudah include semua kolom yang diperlukan)
    - Save dengan encoding UTF-8

3. **User upload file CSV:**

    - Pilih file CSV
    - Klik tombol "Import Pranota"

4. **Sistem memproses import:**

    - Validasi format CSV (support comma dan semicolon delimiter)
    - Parse data dan kelompokkan berdasarkan `group` dan `periode`
    - Untuk setiap kombinasi group-periode:
        - Cari semua tagihan di `daftar_tagihan_kontainer_sewa` yang match dengan:
            - `nomor_kontainer` sesuai CSV
            - `group` sesuai CSV
            - `periode` sesuai CSV
            - `status_pranota` IS NULL (belum masuk pranota)
        - Kumpulkan semua tagihan dalam 1 group-periode
        - Generate nomor pranota baru (format: PMS{cetakan}{bulan}{tahun}{nomor})
        - Buat 1 pranota baru untuk beberapa kontainer tersebut
        - Update status semua tagihan dalam grup:
            - `status_pranota` = 'included'
            - `pranota_id` = ID pranota yang baru dibuat

5. **Sistem menampilkan hasil:**
    - Jumlah pranota berhasil dibuat
    - Total kontainer yang masuk pranota
    - Detail setiap pranota (nomor invoice, group, periode, jumlah kontainer, total amount)
    - Daftar kontainer yang tidak ditemukan
    - Daftar error jika ada

## Validasi dan Error Handling

### Validasi:

-   File harus format CSV
-   Ukuran maksimal 10MB
-   Kolom `nomor_kontainer` wajib ada
-   Nomor kontainer tidak boleh kosong

### Kondisi yang Diabaikan:

-   Baris kosong di CSV
-   Kontainer yang sudah masuk pranota (status_pranota != NULL)
-   Kontainer yang tidak ditemukan di database

### Error yang Ditampilkan:

-   Format file tidak valid
-   Kolom required tidak ada
-   Error database
-   Error parsing tanggal

## Database Changes

### Tabel: `pranota_tagihan_kontainer_sewa`

**Record Baru per Import (1 pranota untuk beberapa kontainer):**

```sql
INSERT INTO pranota_tagihan_kontainer_sewa (
    no_invoice,
    total_amount,
    keterangan,
    status,
    tagihan_kontainer_sewa_ids,
    jumlah_tagihan,
    tanggal_pranota,
    due_date,
    created_at,
    updated_at
) VALUES (
    'PMS110250000123',  -- Auto generated
    4275000.00,         -- Sum dari grand_total semua tagihan dalam group-periode
    'Pranota Group 1 Periode 1 - 5 kontainer (Import)',
    'unpaid',
    '[1234, 1235, 1236, 1237, 1238]',  -- Array ID semua tagihan dalam group-periode
    5,                  -- Jumlah kontainer dalam group-periode
    '2025-10-02',       -- Tanggal import
    '2025-11-01',       -- Due date dari CSV atau default
    NOW(),
    NOW()
);
```

### Tabel: `daftar_tagihan_kontainer_sewa`

**Update Status (Bulk update untuk semua kontainer dalam 1 group-periode):**

```sql
UPDATE daftar_tagihan_kontainer_sewa
SET status_pranota = 'included',
    pranota_id = 123,
    updated_at = NOW()
WHERE id IN (1234, 1235, 1236, 1237, 1238)  -- Semua tagihan dalam group-periode
AND status_pranota IS NULL;
```

**Atau dengan kondisi:**

```sql
UPDATE daftar_tagihan_kontainer_sewa
SET status_pranota = 'included',
    pranota_id = 123,
    updated_at = NOW()
WHERE `group` = 1
AND periode = 1
AND status_pranota IS NULL;
```

### Tabel: `nomor_terakhir`

**Update Nomor Terakhir:**

```sql
UPDATE nomor_terakhir
SET nomor_terakhir = nomor_terakhir + 1
WHERE modul = 'PMS';
```

## Permission Required

-   `pranota-kontainer-sewa-create` - Diperlukan untuk mengakses halaman import dan proses import

## Testing Checklist

### Basic Import:

-   [ ] Download template CSV berhasil
-   [ ] Upload CSV dengan 1 kontainer berhasil
-   [ ] Upload CSV dengan multiple kontainer berhasil
-   [ ] Pranota tergenerate dengan nomor yang benar
-   [ ] Status tagihan berubah menjadi "included"

### Validation:

-   [ ] Upload file non-CSV ditolak
-   [ ] Upload file > 10MB ditolak
-   [ ] CSV tanpa kolom `nomor_kontainer` ditolak
-   [ ] Baris dengan nomor kontainer kosong di-skip

### Error Handling:

-   [ ] Kontainer tidak ditemukan di-skip dan dilaporkan
-   [ ] Kontainer sudah masuk pranota di-skip dan dilaporkan
-   [ ] Error parsing tanggal menggunakan default (30 hari)
-   [ ] Database error di-rollback dan dilaporkan

### Display:

-   [ ] Hasil import ditampilkan dengan benar
-   [ ] Detail error dapat di-expand
-   [ ] List kontainer tidak ditemukan ditampilkan
-   [ ] Redirect ke index setelah sukses

## Contoh Penggunaan

### Scenario 1: Import dari Template Simple

```csv
group,periode,keterangan,due_date
1,1,Pranota Oktober Group 1,2025-10-31
```

**Jika di database ada:**

-   CBHU3952697 (Group: 1, Periode: 1)
-   CBHU4077764 (Group: 1, Periode: 1)
-   CBHU5876322 (Group: 1, Periode: 1)

**Result:**

-   **1 pranota dibuat** dengan 3 kontainer
-   Status 3 tagihan berubah menjadi "masuk pranota"

---

### Scenario 2: Import Multiple Groups

```csv
group,periode,keterangan,due_date
1,1,Pranota Group 1 Periode 1,2025-10-31
2,1,Pranota Group 2 Periode 1,2025-10-31
1,2,Pranota Group 1 Periode 2,2025-11-30
```

**Jika di database ada:**

-   Group 1 Periode 1: 5 kontainer
-   Group 2 Periode 1: 3 kontainer
-   Group 1 Periode 2: 4 kontainer

**Result:**

-   **3 pranota dibuat:**
    -   Pranota #1: Group 1 Periode 1 → 5 kontainer
    -   Pranota #2: Group 2 Periode 1 → 3 kontainer
    -   Pranota #3: Group 1 Periode 2 → 4 kontainer
-   Total: 12 kontainer masuk pranota

---

### Scenario 3: Import dari Export File (Recommended)

Langsung export dari "Daftar Tagihan Kontainer Sewa":

```csv
Group;Vendor;Nomor Kontainer;Size;Periode;...
1;DPE;CBHU3952697;20;1;...
1;DPE;CBHU4077764;20;1;...
1;DPE;CBHU5876322;20;1;...
2;DPE;CBHU5911444;20;1;...
2;DPE;CBHU5914130;20;1;...
```

**Result:**

-   **2 pranota dibuat:**
    -   Pranota #1: Group 1 Periode 1 → 3 kontainer (CBHU3952697, CBHU4077764, CBHU5876322)
    -   Pranota #2: Group 2 Periode 1 → 2 kontainer (CBHU5911444, CBHU5914130)

---

### Scenario 4: Import with Some Errors

```csv
group,periode,keterangan,due_date
1,1,Pranota valid,2025-10-31
99,99,Group tidak ada,2025-10-31
1,2,Sudah masuk pranota,2025-10-31
```

**Result:**

-   1 pranota dibuat (Group 1 Periode 1)
-   Group 99 Periode 99: Tidak ditemukan di database
-   Group 1 Periode 2: Sudah masuk pranota (di-skip)

## Maintenance Notes

### Jika Format Nomor Pranota Berubah:

Edit method `importCsv()` di controller, bagian generate nomor invoice:

```php
$nomorCetakan = 1;
$tahun = Carbon::now()->format('y');
$bulan = Carbon::now()->format('m');
$noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
```

### Jika Kolom CSV Berubah:

1. Update template di method `downloadTemplateCsv()`
2. Update validasi di method `importCsv()`
3. Update dokumentasi di view `import.blade.php`

### Jika Business Logic Berubah:

-   Update method `importCsv()` sesuai requirement baru
-   Update dokumentasi di file ini
-   Update instruksi di view import

## Troubleshooting

### Problem: "Modul PMS tidak ditemukan"

**Solution:** Pastikan ada record di tabel `nomor_terakhir` dengan `modul = 'PMS'`

```sql
INSERT INTO nomor_terakhir (modul, nomor_terakhir) VALUES ('PMS', 0);
```

### Problem: "Kontainer tidak ditemukan"

**Cause:**

-   Nomor kontainer tidak ada di tabel `daftar_tagihan_kontainer_sewa`
-   Nomor kontainer sudah masuk pranota

**Solution:**

-   Cek data di database
-   Pastikan nomor kontainer sesuai
-   Pastikan `status_pranota` IS NULL

### Problem: "Import berhasil tapi status tidak berubah"

**Cause:** Database transaction rollback karena error

**Solution:**

-   Cek log Laravel: `storage/logs/laravel.log`
-   Cek database connection
-   Pastikan tidak ada foreign key constraint yang bermasalah

## Support & Contact

Jika ada pertanyaan atau issue terkait fitur ini, hubungi tim development.
