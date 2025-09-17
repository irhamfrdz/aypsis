# Master Divisi & Pekerjaan Permission Seeder

Seeder ini digunakan untuk mengisi permission dan data master untuk Divisi dan Pekerjaan dalam aplikasi Aypsis.

## File Seeder yang Dibuat

### 1. MasterDivisiPermissionSeeder.php

Seeder untuk permission master divisi dengan struktur:

-   `master-divisi` - Akses dasar manajemen divisi
-   `master-divisi.view` - Melihat data divisi
-   `master-divisi.create` - Membuat divisi baru
-   `master-divisi.update` - Mengupdate data divisi
-   `master-divisi.delete` - Menghapus divisi
-   `master-divisi.print` - Mencetak data divisi
-   `master-divisi.export` - Mengekspor data divisi

### 2. MasterPekerjaanPermissionSeeder.php

Seeder untuk permission master pekerjaan dengan struktur:

-   `master-pekerjaan` - Akses dasar manajemen pekerjaan
-   `master-pekerjaan.view` - Melihat data pekerjaan
-   `master-pekerjaan.create` - Membuat pekerjaan baru
-   `master-pekerjaan.update` - Mengupdate data pekerjaan
-   `master-pekerjaan.delete` - Menghapus pekerjaan
-   `master-pekerjaan.print` - Mencetak data pekerjaan
-   `master-pekerjaan.export` - Mengekspor data pekerjaan

### 3. PekerjaanSeeder.php

Seeder untuk data master pekerjaan dengan berbagai jabatan dari setiap divisi:

-   **IT Division**: IT Manager, Software Developer, System Administrator
-   **Finance Division**: Finance Manager, Accountant, Finance Staff
-   **Operations Division**: Operations Manager, Operations Supervisor, Operations Staff
-   **Human Resources Division**: HR Manager, HR Staff, Recruitment Officer
-   **ABK Division**: Nahkoda, Mualim I/II, Masinis I/II, Juru Mudi, Bosun
-   **Admin Division**: Admin Manager, Admin Staff, Document Officer

## Cara Menjalankan Seeder

### Menjalankan Semua Seeder

```bash
php artisan db:seed
```

### Menjalankan Seeder Tertentu

```bash
# Permission Divisi
php artisan db:seed --class=MasterDivisiPermissionSeeder

# Permission Pekerjaan
php artisan db:seed --class=MasterPekerjaanPermissionSeeder

# Data Pekerjaan
php artisan db:seed --class=PekerjaanSeeder

# Data Divisi
php artisan db:seed --class=DivisiSeeder
```

## Struktur Database

### Tabel divisis

```sql
CREATE TABLE divisis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_divisi VARCHAR(100) NOT NULL UNIQUE,
    kode_divisi VARCHAR(20) NOT NULL UNIQUE,
    deskripsi TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabel pekerjaans

```sql
CREATE TABLE pekerjaans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_pekerjaan VARCHAR(255) NOT NULL,
    kode_pekerjaan VARCHAR(255) NOT NULL UNIQUE,
    deskripsi TEXT NULL,
    divisi VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabel permissions

Permission akan ditambahkan ke tabel permissions yang sudah ada dengan format:

-   `master-divisi.*` untuk permission divisi
-   `master-pekerjaan.*` untuk permission pekerjaan

## Menu Akses

Setelah seeder dijalankan, user dengan permission yang sesuai dapat mengakses:

-   **Master → Divisi** - Mengelola data divisi
-   **Master → Pekerjaan** - Mengelola data pekerjaan

## Catatan

-   Seeder menggunakan `firstOrCreate()` untuk menghindari duplikasi data
-   Permission sudah terintegrasi dengan sistem permission matrix di UserController
-   Data pekerjaan sudah terhubung dengan divisi yang sesuai
