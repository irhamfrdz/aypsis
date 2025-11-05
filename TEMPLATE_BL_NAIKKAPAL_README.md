# Template Import BL & Naik Kapal - Panduan Penggunaan

## Deskripsi

Template CSV ini digunakan untuk mengimpor data Bill of Lading (BL) dan Naik Kapal secara bulk ke dalam sistem.

## Format File

-   **Format**: CSV (Comma Separated Values)
-   **Encoding**: UTF-8
-   **Separator**: Koma (,)
-   **Delimiter**: Tanda kutip ganda (")

---

## TEMPLATE BILL OF LADING (BL)

### Kolom Template BL

#### 1. nomor_bl

-   **Deskripsi**: Nomor Bill of Lading unik
-   **Format**: BL-YYYYMMDD-XXX
-   **Contoh**: BL-20241104-001
-   **Wajib**: Ya

#### 2. nomor_kontainer

-   **Deskripsi**: Nomor kontainer
-   **Format**: CONTYYYYMMDDXXX
-   **Contoh**: CONT20241104001
-   **Wajib**: Ya

#### 3. no_seal

-   **Deskripsi**: Nomor seal kontainer
-   **Format**: Teks/Angka
-   **Contoh**: SEAL001
-   **Wajib**: Tidak

#### 4. nama_kapal

-   **Deskripsi**: Nama kapal
-   **Format**: Teks
-   **Contoh**: KM SINAR HARAPAN
-   **Wajib**: Ya

#### 5. no_voyage

-   **Deskripsi**: Nomor voyage kapal
-   **Format**: Teks/Angka
-   **Contoh**: SH001
-   **Wajib**: Ya

#### 6. pelabuhan_tujuan

-   **Deskripsi**: Pelabuhan tujuan
-   **Format**: Teks
-   **Contoh**: Batam, Jakarta, Surabaya
-   **Wajib**: Ya

#### 7. nama_barang

-   **Deskripsi**: Nama/jenis barang
-   **Format**: Teks
-   **Contoh**: Elektronik, Makanan & Minuman
-   **Wajib**: Ya

#### 8. tipe_kontainer

-   **Deskripsi**: Tipe/ukuran kontainer
-   **Format**: Teks
-   **Contoh**: 20 FT, 40 FT, 40 HC
-   **Wajib**: Ya

#### 9. ukuran_kontainer

-   **Deskripsi**: Detail ukuran kontainer
-   **Format**: Teks (PxLxT)
-   **Contoh**: 20x8x8.6, 40x8x8.6
-   **Wajib**: Tidak

#### 10. tonnage

-   **Deskripsi**: Berat tonnage (ton)
-   **Format**: Desimal
-   **Contoh**: 15.500, 25.000
-   **Wajib**: Ya

#### 11. volume

-   **Deskripsi**: Volume (m³)
-   **Format**: Desimal
-   **Contoh**: 25.750, 45.300
-   **Wajib**: Ya

#### 12. kuantitas

-   **Deskripsi**: Jumlah barang
-   **Format**: Angka
-   **Contoh**: 100, 200
-   **Wajib**: Tidak

#### 13. term

-   **Deskripsi**: Term pembayaran
-   **Format**: Teks
-   **Contoh**: COD, Credit 30, FOB
-   **Wajib**: Tidak

#### 14. tanggal_muat

-   **Deskripsi**: Tanggal muat barang
-   **Format**: YYYY-MM-DD
-   **Contoh**: 2024-11-04
-   **Wajib**: Tidak

#### 15. jam_muat

-   **Deskripsi**: Jam muat barang
-   **Format**: HH:MM
-   **Contoh**: 08:00, 14:30
-   **Wajib**: Tidak

#### 16. prospek_id

-   **Deskripsi**: ID prospek (dari master prospek)
-   **Format**: Angka
-   **Contoh**: 1, 2
-   **Wajib**: Tidak

#### 17. keterangan

-   **Deskripsi**: Keterangan tambahan
-   **Format**: Teks
-   **Contoh**: Contoh data BL untuk import
-   **Wajib**: Tidak

---

## TEMPLATE NAIK KAPAL

### Kolom Template Naik Kapal

#### 1. nomor_kontainer

-   **Deskripsi**: Nomor kontainer
-   **Format**: CONTYYYYMMDDXXX
-   **Contoh**: CONT20241104001
-   **Wajib**: Ya

#### 2. ukuran_kontainer

-   **Deskripsi**: Ukuran detail kontainer
-   **Format**: Teks (PxLxT)
-   **Contoh**: 20x8x8.6, 40x8x8.6
-   **Wajib**: Ya

#### 3. no_seal

-   **Deskripsi**: Nomor seal kontainer
-   **Format**: Teks/Angka
-   **Contoh**: SEAL001
-   **Wajib**: Tidak

#### 4. nama_kapal

-   **Deskripsi**: Nama kapal
-   **Format**: Teks
-   **Contoh**: KM SINAR HARAPAN
-   **Wajib**: Ya

#### 5. no_voyage

-   **Deskripsi**: Nomor voyage
-   **Format**: Teks/Angka
-   **Contoh**: SH001
-   **Wajib**: Ya

#### 6. pelabuhan_tujuan

-   **Deskripsi**: Pelabuhan tujuan
-   **Format**: Teks
-   **Contoh**: Batam, Jakarta
-   **Wajib**: Ya

#### 7. jenis_barang

-   **Deskripsi**: Jenis barang yang dimuat
-   **Format**: Teks
-   **Contoh**: Elektronik, Makanan & Minuman
-   **Wajib**: Ya

#### 8. tipe_kontainer

-   **Deskripsi**: Tipe kontainer
-   **Format**: Teks
-   **Contoh**: 20 FT, 40 FT
-   **Wajib**: Ya

#### 9. tipe_kontainer_detail

-   **Deskripsi**: Detail tipe kontainer
-   **Format**: Teks
-   **Contoh**: Dry Container, High Cube, Reefer
-   **Wajib**: Tidak

#### 10. volume

-   **Deskripsi**: Volume muatan (m³)
-   **Format**: Desimal
-   **Contoh**: 25.750, 45.300
-   **Wajib**: Ya

#### 11. tonase

-   **Deskripsi**: Berat tonase (ton)
-   **Format**: Desimal
-   **Contoh**: 15.500, 25.000
-   **Wajib**: Ya

#### 12. kuantitas

-   **Deskripsi**: Jumlah barang
-   **Format**: Angka
-   **Contoh**: 100, 200
-   **Wajib**: Tidak

#### 13. tanggal_muat

-   **Deskripsi**: Tanggal muat
-   **Format**: YYYY-MM-DD
-   **Contoh**: 2024-11-04
-   **Wajib**: Tidak

#### 14. jam_muat

-   **Deskripsi**: Jam muat
-   **Format**: HH:MM
-   **Contoh**: 08:00, 14:30
-   **Wajib**: Tidak

#### 15. prospek_id

-   **Deskripsi**: ID prospek/supir
-   **Format**: Angka
-   **Contoh**: 1, 2
-   **Wajib**: Tidak

#### 16. nama_supir

-   **Deskripsi**: Nama supir (untuk referensi)
-   **Format**: Teks
-   **Contoh**: SUPIR A, SUPIR B
-   **Wajib**: Tidak

#### 17. keterangan

-   **Deskripsi**: Keterangan tambahan
-   **Format**: Teks
-   **Contoh**: Contoh data naik kapal
-   **Wajib**: Tidak

---

## Tips Penggunaan

### Data Referensi

1. **prospek_id**: Pastikan ID sesuai dengan data di master prospek
2. **nama_kapal**: Harus sesuai dengan master kapal yang ada
3. **Format angka**: Gunakan titik (.) sebagai pemisah desimal

### Validasi Data

1. **Nomor kontainer unik**: Pastikan tidak ada duplikasi nomor kontainer
2. **Format tanggal**: Gunakan format YYYY-MM-DD yang konsisten
3. **Format jam**: Gunakan format 24 jam (HH:MM)

### Encoding File

1. Simpan file dengan encoding **UTF-8**
2. Gunakan pemisah koma (,) untuk kolom
3. Gunakan tanda kutip ganda (") untuk string yang mengandung koma

## Cara Import

1. Download template CSV dari sistem
2. Isi data sesuai format yang ditentukan
3. Simpan file dengan encoding UTF-8
4. Upload file melalui fitur import di sistem
5. Sistem akan memvalidasi dan memberikan laporan hasil import

## Contoh Data BL

```csv
nomor_bl,nomor_kontainer,no_seal,nama_kapal,no_voyage,pelabuhan_tujuan,nama_barang,tipe_kontainer,ukuran_kontainer,tonnage,volume,kuantitas,term,tanggal_muat,jam_muat,prospek_id,keterangan
BL-20241104-001,CONT20241104001,SEAL001,KM SINAR HARAPAN,SH001,Batam,Elektronik,20 FT,20x8x8.6,15.500,25.750,100,COD,2024-11-04,08:00,1,Contoh data BL untuk import
```

## Contoh Data Naik Kapal

```csv
nomor_kontainer,ukuran_kontainer,no_seal,nama_kapal,no_voyage,pelabuhan_tujuan,jenis_barang,tipe_kontainer,tipe_kontainer_detail,volume,tonase,kuantitas,tanggal_muat,jam_muat,prospek_id,nama_supir,keterangan
CONT20241104001,20x8x8.6,SEAL001,KM SINAR HARAPAN,SH001,Batam,Elektronik,20 FT,Dry Container,25.750,15.500,100,2024-11-04,08:00,1,SUPIR A,Contoh data naik kapal untuk import
```
