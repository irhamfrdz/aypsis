# 🚢 Prospek Kapal - Container Loading Management System

## Overview

Prospek Kapal adalah fitur untuk mengelola proses loading kontainer ke kapal berdasarkan nomor voyage dari pergerakan kapal. Sistem ini memungkinkan pengguna untuk memasukkan kontainer yang sudah melalui proses tanda terima ke dalam kapal sesuai dengan jadwal voyage.

## Features

### 1. **Voyage-Based Planning**

-   Buat prospek kapal berdasarkan voyage yang tersedia di pergerakan kapal
-   Otomatis mengambil informasi kapal, kapten, dan rute pelayaran
-   Jadwalkan tanggal loading dan estimasi keberangkatan

### 2. **Container Management**

-   Tambahkan kontainer dari tanda terima yang sudah approved
-   Tambahkan kontainer dari tanda terima tanpa surat jalan
-   Otomatis parsing nomor kontainer dari field array/JSON
-   Sequence loading untuk mengatur urutan muatan

### 3. **Loading Progress Tracking**

-   Status tracking: pending → ready → loading → loaded → problem
-   Real-time progress percentage calculation
-   Bulk operations untuk update status kontainer
-   Timeline loading dengan tanggal dan keterangan

### 4. **Integration Points**

-   **Pergerakan Kapal**: Sumber voyage dan informasi kapal
-   **Tanda Terima**: Sumber kontainer yang sudah di-approve
-   **Tanda Terima Tanpa Surat Jalan**: Sumber kontainer alternatif
-   **Permission System**: Role-based access control

## Database Structure

### Tables Created

1. **`prospek_kapal`**

    - Informasi utama prospek kapal
    - Link ke pergerakan_kapal
    - Progress tracking
    - Status management

2. **`prospek_kapal_kontainers`**
    - Detail kontainer per prospek
    - Link ke tanda terima sources
    - Loading status dan sequence
    - Metadata per kontainer

### Permissions Added

-   `prospek-kapal-view`: Melihat daftar dan detail prospek
-   `prospek-kapal-create`: Membuat prospek baru
-   `prospek-kapal-update`: Edit dan update status
-   `prospek-kapal-delete`: Hapus prospek (hanya draft)

## User Workflow

### 1. Create Prospek Kapal

1. Pilih voyage dari pergerakan kapal yang tersedia
2. Set tanggal loading dan estimasi departure
3. Tambahkan keterangan jika diperlukan
4. Sistem otomatis create dengan status 'draft'

### 2. Add Containers

1. Pilih tanda terima yang sudah approved
2. Sistem otomatis parsing nomor kontainer
3. Generate loading sequence
4. Status kontainer dimulai dari 'pending'

### 3. Loading Process

1. Update status kontainer secara individual
2. Track progress real-time
3. Add keterangan per kontainer
4. Set tanggal loading actual

### 4. Complete Loading

-   Semua kontainer status 'loaded'
-   Prospek kapal status 'completed'
-   Progress 100%

## Technical Implementation

### Models

```php
ProspekKapal: Main entity with relationships
├── PergerakanKapal (belongsTo)
└── ProspekKapalKontainer (hasMany)
    ├── TandaTerima (belongsTo)
    └── TandaTerimaTanpaSuratJalan (belongsTo)
```

### Controllers

-   **ProspekKapalController**: Main CRUD operations
-   **Middleware**: Permission-based access control
-   **Validation**: Comprehensive form validation

### Views

-   **Index**: List all prospek with filters
-   **Create**: Form untuk buat prospek baru
-   **Show**: Detail view dengan container management
-   **Modals**: Add containers dan update status

### Routes

```php
Route::resource('prospek-kapal', ProspekKapalController::class)
Route::post('prospek-kapal/{prospekKapal}/add-kontainers')
Route::patch('prospek-kapal/kontainer/{kontainer}/update-status')
```

## Key Features Implemented

### ✅ Completed

1. **Database Schema**: Tables dan relationships
2. **Models**: Eloquent models dengan relationships
3. **Controllers**: Full CRUD operations
4. **Views**: Responsive UI dengan Tailwind CSS
5. **Routes**: RESTful routes dengan permissions
6. **Navigation**: Menu integration
7. **Permissions**: Role-based access control
8. **Container Parsing**: Automatic parsing dari tanda terima
9. **Progress Tracking**: Real-time loading progress
10. **Status Management**: Complete workflow states

### 🎯 Ready for Use

-   Menu sudah tersedia di sidebar navigation
-   Permissions sudah assign ke admin role
-   Database tables sudah ready
-   All CRUD operations functional
-   Integration dengan existing systems

## Next Steps for Enhancement

### 🚀 Future Improvements

1. **Bulk Operations**: Mass update container status
2. **Reporting**: Loading reports dan analytics
3. **Notifications**: Real-time updates
4. **Mobile View**: Responsive improvements
5. **Export**: PDF/Excel reports
6. **Integration**: API endpoints untuk external systems

## Usage Instructions

1. **Access**: Navigate to "Prospek Kapal" menu in sidebar
2. **Create**: Click "Buat Prospek Baru" and select voyage
3. **Add Containers**: Use "Tambah Kontainer" to select from tanda terima
4. **Track Progress**: Update container status as loading progresses
5. **Complete**: All containers loaded = prospek completed

---

**Status**: ✅ Ready for Production Use
**Last Updated**: October 22, 2025
**Dependencies**: Laravel 10+, MySQL 8+, Tailwind CSS
