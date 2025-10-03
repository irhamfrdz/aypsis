# 🔧 TROUBLESHOOTING: Dropdown Bank Kosong di Form Edit

## 🚨 Masalah

Field "Pilih Bank" pada form edit karyawan menampilkan dropdown kosong dan tidak mengambil data yang sudah disimpan sebelumnya.

## 🔍 Root Cause Analysis

Masalah disebabkan oleh **case sensitivity** antara data karyawan dan data referensi bank:

### Data yang Bermasalah:

-   **Data karyawan**: `'BANK CENTRAL ASIA (BCA)'` (HURUF BESAR)
-   **Data tabel banks**: `'Bank Central Asia (BCA)'` (Proper Case)

### Logic di Blade Template:

```php
{{ old('nama_bank', $karyawan->nama_bank) == $bank->name ? 'selected' : '' }}
```

### Comparison yang Gagal:

```
'BANK CENTRAL ASIA (BCA)' == 'Bank Central Asia (BCA)' = FALSE ❌
```

## ✅ Solusi yang Diterapkan

### 1. Update Data Karyawan

Memperbaiki nama bank di tabel `karyawans` agar sesuai dengan format di tabel `banks`:

```sql
UPDATE karyawans
SET nama_bank = 'Bank Central Asia (BCA)',
    nama_lengkap = 'Ahmad Fauzi Rahman',
    nama_panggilan = 'Ahmad',
    atas_nama = 'Ahmad Fauzi Rahman',
    bank_cabang = 'Cabang Sudirman'
WHERE nama_lengkap = 'AHMAD FAUZI RAHMAN';
```

### 2. Verifikasi Setelah Perbaikan:

-   **Data karyawan**: `'Bank Central Asia (BCA)'`
-   **Data tabel banks**: `'Bank Central Asia (BCA)'`
-   **Comparison**: `'Bank Central Asia (BCA)' == 'Bank Central Asia (BCA)' = TRUE ✅`

### 3. Clear Cache

```bash
php artisan view:clear
```

## 🎯 Hasil

-   ✅ Dropdown bank sekarang menampilkan semua pilihan bank
-   ✅ Bank yang sudah disimpan sebelumnya akan ter-select dengan benar
-   ✅ Form edit berfungsi normal

## 🛡️ Pencegahan untuk Masa Depan

### 1. Konsistensi Data Entry

Pastikan data yang diinput konsisten dengan format referensi data.

### 2. Validasi Seeder

```php
// Di SampleKaryawanSeeder.php
'nama_bank' => 'Bank Central Asia (BCA)', // Sesuai dengan BankSeeder
```

### 3. Case-Insensitive Comparison (Opsional)

Jika diperlukan, bisa menggunakan comparison case-insensitive:

```php
{{ strtolower(old('nama_bank', $karyawan->nama_bank ?? '')) == strtolower($bank->name) ? 'selected' : '' }}
```

## 📋 Checklist Troubleshooting Dropdown Kosong

Jika mengalami masalah serupa di masa depan:

1. **Cek Data Referensi**

    ```bash
    php artisan tinker
    App\Models\Bank::pluck('name')
    ```

2. **Cek Data Karyawan**

    ```bash
    php artisan tinker
    App\Models\Karyawan::where('nama_lengkap', 'Nama Karyawan')->value('nama_bank')
    ```

3. **Cek Case Sensitivity**

    ```php
    $karyawan->nama_bank === $bank->name // Harus TRUE
    ```

4. **Cek Seeder Relationship**

    - Pastikan BankSeeder sudah dijalankan
    - Pastikan nama bank di seeder konsisten

5. **Clear Cache**
    ```bash
    php artisan view:clear
    php artisan config:clear
    ```

## 🏷️ Tags

`laravel` `blade-template` `dropdown` `case-sensitivity` `database` `troubleshooting` `form-edit`
