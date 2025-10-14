# Test Workflow Surat Jalan

## Daftar Modifikasi Yang Sudah Dilakukan

### 1. ✅ Input Telepon dan Alamat Dihapus
- Field `telepon` dan `alamat` sudah dihapus dari form create
- Database tetap memiliki kolom tersebut (tidak dihapus)
- Validasi controller sudah diupdate untuk tidak require field tersebut

### 2. ✅ Pengirim Menjadi Readonly
- Field pengirim sekarang readonly dengan background abu-abu
- Data pengirim diambil dari order yang dipilih
- User tidak bisa mengedit field pengirim secara manual

### 3. ✅ Kemasan Input Menjadi Dropdown
- Karton, Terpal, Plastik sekarang menggunakan dropdown "Pakai/Tidak Pakai"
- Database kolom kemasan sudah diubah dari integer ke string
- Validasi sudah disesuaikan untuk menerima string values

### 4. ✅ Workflow Order Selection
- User harus pilih order terlebih dahulu sebelum create surat jalan
- Jika akses langsung ke create tanpa pilih order, akan redirect ke select-order
- Alert message ditampilkan untuk memberi info ke user

## Cara Testing

### Test 1: Akses Direct ke Create (Expected: Redirect)
1. Buka: http://127.0.0.1:8000/surat-jalan/create
2. Expected: Redirect ke select-order dengan pesan info

### Test 2: Normal Workflow
1. Buka: http://127.0.0.1:8000/surat-jalan
2. Klik "Tambah Surat Jalan" 
3. Pilih salah satu order dari daftar
4. Verify: Form create terbuka dengan:
   - Pengirim readonly dan terisi dari order
   - Tidak ada field telepon dan alamat
   - Kemasan menggunakan dropdown "Pakai/Tidak Pakai"

### Test 3: Form Submission
1. Isi semua field yang required
2. Submit form
3. Expected: Data tersimpan dengan benar tanpa error validation

## Database Schema Status

### Tabel surat_jalans:
```sql
- id (primary key)
- order_id (foreign key) ✅
- pengirim (string) ✅ - readonly dari order
- telepon (string) ✅ - field dihapus dari form tapi kolom masih ada
- alamat (text) ✅ - field dihapus dari form tapi kolom masih ada
- karton (string) ✅ - dropdown "Pakai/Tidak Pakai"
- terpal (string) ✅ - dropdown "Pakai/Tidak Pakai" 
- plastik (string) ✅ - dropdown "Pakai/Tidak Pakai"
- ... (other columns)
```

## File Yang Telah Dimodifikasi

1. **SuratJalanController.php**
   - Method create: Added redirect logic jika tidak ada order
   - Method store/update: Removed telepon & alamat dari validation rules

2. **create.blade.php**
   - Removed telepon & alamat input fields
   - Made pengirim field readonly dengan styling
   - Changed kemasan inputs ke dropdown format

3. **select-order.blade.php**
   - Added alert messages untuk error & info

4. **Database Migrations**
   - Added order_id foreign key
   - Changed kemasan columns dari integer ke string

## Status: SELESAI ✅

Semua permintaan user telah diimplementasikan:
- ✅ Telepon & alamat dihapus dari form (database tetap ada)
- ✅ Pengirim readonly dari data order
- ✅ Kemasan pakai dropdown "Pakai/Tidak Pakai"
- ✅ Workflow order selection sudah proper
- ✅ Validasi controller sudah disesuaikan