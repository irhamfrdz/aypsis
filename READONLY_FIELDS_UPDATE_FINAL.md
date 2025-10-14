# Update Surat Jalan Form - Readonly Fields

## Perubahan yang Telah Dilakukan

### ✅ Field yang Dibuat Readonly (Data dari Order):

1. **Pengirim** 
   - Field: `pengirim`
   - Sumber data: `$selectedOrder->pengirim->nama_pengirim`
   - Status: ✅ Readonly dengan background abu-abu

2. **Jenis Barang**
   - Field: `jenis_barang` 
   - Sumber data: `$selectedOrder->jenisBarang->nama_barang`
   - Status: ✅ Readonly dengan background abu-abu

3. **Tujuan Pengambilan**
   - Field: `tujuan_pengambilan`
   - Sumber data: `$selectedOrder->tujuan_ambil`
   - Status: ✅ Readonly dengan background abu-abu

4. **Tujuan Pengiriman**
   - Field: `tujuan_pengiriman`
   - Sumber data: `$selectedOrder->tujuan_kirim`
   - Status: ✅ Readonly dengan background abu-abu

### Styling untuk Readonly Fields:
```css
readonly
class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('field_name') border-red-500 @enderror"
```

### Informasi Tambahan:
- Setiap field readonly memiliki pesan informasi: "Data [field_name] diambil dari order yang dipilih"
- Background abu-abu (`bg-gray-50`) menunjukkan field tidak dapat diedit
- Data otomatis terisi dari order yang dipilih sebelumnya
- User tidak bisa mengubah data tersebut secara manual

### File yang Dimodifikasi:
- `resources/views/surat-jalan/create.blade.php`

## Status: SELESAI ✅

Semua field yang diminta telah berhasil diubah menjadi readonly:
- ✅ Pengirim
- ✅ Jenis Barang  
- ✅ Tujuan Pengambilan
- ✅ Tujuan Pengiriman

Form sekarang akan menampilkan data dari order yang dipilih dan mencegah user untuk mengedit field-field tersebut.