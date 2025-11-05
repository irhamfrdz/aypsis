# Template Import Orders - Panduan Penggunaan

## Deskripsi

Template CSV ini digunakan untuk mengimpor data orders secara bulk ke dalam sistem.

## Format File

-   **Format**: CSV (Comma Separated Values)
-   **Encoding**: UTF-8
-   **Separator**: Koma (,)
-   **Delimiter**: Tanda kutip ganda (")

## Kolom Template

### 1. nomor_order

-   **Deskripsi**: Nomor order unik
-   **Format**: ORD-YYYYMMDD-XXX
-   **Contoh**: ORD-20241104-001
-   **Wajib**: Ya

### 2. tanggal_order

-   **Deskripsi**: Tanggal order dibuat
-   **Format**: YYYY-MM-DD
-   **Contoh**: 2024-11-04
-   **Wajib**: Ya

### 3. pengirim_id

-   **Deskripsi**: ID pengirim (dari master pengirim)
-   **Format**: Angka
-   **Contoh**: 1
-   **Wajib**: Ya

### 4. nama_pengirim

-   **Deskripsi**: Nama pengirim (untuk referensi)
-   **Format**: Teks
-   **Contoh**: PT CONTOH PENGIRIM
-   **Wajib**: Tidak (akan diambil dari pengirim_id)

### 5. term_id

-   **Deskripsi**: ID term pembayaran
-   **Format**: Angka
-   **Contoh**: 1
-   **Wajib**: Ya

### 6. nama_term

-   **Deskripsi**: Nama term pembayaran (untuk referensi)
-   **Format**: Teks
-   **Contoh**: COD, Credit 30
-   **Wajib**: Tidak

### 7. jenis_barang_id

-   **Deskripsi**: ID jenis barang
-   **Format**: Angka
-   **Contoh**: 1
-   **Wajib**: Ya

### 8. nama_jenis_barang

-   **Deskripsi**: Nama jenis barang (untuk referensi)
-   **Format**: Teks
-   **Contoh**: Elektronik, Makanan
-   **Wajib**: Tidak

### 9. tujuan_ambil

-   **Deskripsi**: Alamat/lokasi pengambilan barang
-   **Format**: Teks
-   **Contoh**: Jakarta Utara
-   **Wajib**: Ya

### 10. tujuan_kirim

-   **Deskripsi**: Alamat/lokasi tujuan pengiriman
-   **Format**: Teks
-   **Contoh**: Surabaya
-   **Wajib**: Ya

### 11. tipe_kontainer

-   **Deskripsi**: Jenis kontainer atau cargo
-   **Format**: kontainer | cargo
-   **Contoh**: kontainer, cargo
-   **Wajib**: Ya

### 12. size_kontainer

-   **Deskripsi**: Ukuran kontainer (jika tipe_kontainer = kontainer)
-   **Format**: Angka
-   **Contoh**: 20, 40
-   **Wajib**: Jika tipe_kontainer = kontainer

### 13. unit_kontainer

-   **Deskripsi**: Satuan ukuran kontainer
-   **Format**: ft | HC
-   **Contoh**: ft, HC
-   **Wajib**: Jika tipe_kontainer = kontainer

### 14. no_tiket_do

-   **Deskripsi**: Nomor tiket DO
-   **Format**: Teks
-   **Contoh**: TKT001
-   **Wajib**: Tidak

### 15. jumlah_barang

-   **Deskripsi**: Jumlah barang
-   **Format**: Angka
-   **Contoh**: 100
-   **Wajib**: Tidak

### 16. berat_barang

-   **Deskripsi**: Berat barang (kg)
-   **Format**: Angka
-   **Contoh**: 1000
-   **Wajib**: Tidak

### 17. keterangan

-   **Deskripsi**: Keterangan tambahan
-   **Format**: Teks
-   **Contoh**: Barang elektronik untuk toko
-   **Wajib**: Tidak

### 18. status

-   **Deskripsi**: Status order
-   **Format**: draft | confirmed | processing | completed | cancelled
-   **Contoh**: draft
-   **Default**: draft
-   **Wajib**: Tidak

## Status Order yang Valid

-   **draft**: Order baru dibuat
-   **confirmed**: Order sudah dikonfirmasi
-   **processing**: Order sedang diproses
-   **completed**: Order selesai
-   **cancelled**: Order dibatalkan

## Tips Penggunaan

1. **Pastikan ID referensi valid**: pengirim_id, term_id, dan jenis_barang_id harus sesuai dengan data di master
2. **Format tanggal konsisten**: Gunakan format YYYY-MM-DD
3. **Nomor order unik**: Pastikan nomor_order tidak duplikat
4. **Tipe kontainer**: Jika pilih "cargo", kosongkan size_kontainer dan unit_kontainer
5. **Encoding UTF-8**: Simpan file dengan encoding UTF-8 untuk karakter Indonesia

## Cara Import

1. Download template CSV dari sistem
2. Isi data sesuai format di atas
3. Simpan file dengan encoding UTF-8
4. Upload file melalui fitur import di sistem
5. Sistem akan validasi dan memberikan laporan hasil import

## Contoh Data

```csv
nomor_order,tanggal_order,pengirim_id,nama_pengirim,term_id,nama_term,jenis_barang_id,nama_jenis_barang,tujuan_ambil,tujuan_kirim,tipe_kontainer,size_kontainer,unit_kontainer,no_tiket_do,jumlah_barang,berat_barang,keterangan,status
ORD-20241104-001,2024-11-04,1,PT CONTOH PENGIRIM,1,COD,1,Elektronik,Jakarta Utara,Surabaya,kontainer,20,ft,TKT001,100,1000,Barang elektronik untuk toko,draft
ORD-20241104-002,2024-11-04,2,PT CONTOH LAINNYA,2,Credit 30,2,Makanan,Bandung,Medan,cargo,,,TKT002,50,500,Produk makanan ringan,confirmed
```
