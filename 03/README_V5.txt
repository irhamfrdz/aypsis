CONTAINER BILLING CONTROL V5 — SIAP PAKAI

PERUBAHAN V5
1. MASTER MASSAL VIA TEXTAREA
   Format: KONTAINER|UKURAN|JENIS|VENDOR|CATATAN
   Update Master berlaku langsung pada seluruh tampilan, termasuk data awal.
   Data mentah/histori invoice lama tidak ditimpa. Jika Vendor histori kosong,
   tampilan memakai Vendor dari Master. Rental operasional yang sudah ada ikut disinkronkan.

2. SEMUA TEXTAREA MENGGUNAKAN SEPARATOR |
   Pengambilan:
   KONTAINER|TGL AMBIL|TARIF BULANAN|TARIF HARIAN

   Pengembalian:
   KONTAINER|TGL KEMBALI

   Tagihan:
   VENDOR|NO TAGIHAN|TGL TAGIHAN|TAGIHAN|KONTAINER|PERIODE

   Aplikasi tetap menerima TAB untuk kompatibilitas data lama, tetapi baris reject
   dikembalikan ke textarea dalam separator |.

3. SEMUA EXPORT CSV MENGGUNAKAN SEPARATOR |
   File tetap .csv, tetapi delimiter adalah karakter pagar |.

4. TAB RENTAL
   Menampilkan:
   - Rental OPERASIONAL hasil import pengambilan/pengembalian.
   - ESTIMASI DATA AWAL yang dibentuk dari Rental ID Estimasi pada Excel lama.
   Data estimasi diberi label agar tidak dianggap sebagai tanggal kembali aktual.

5. EXPECTED BILLING
   - Data awal tidak lagi kosong.
   - Dibentuk dari rentalIdEstimated / expectedId pada histori Excel.
   - Periode yang sudah ditagih tetapi belum dibayar: OUTSTANDING.
   - Periode 1..N yang hilang di antara histori: BELUM DITEMUKAN.
   - Nominal periode hilang tidak ditebak.
   - Rental operasional baru memakai anniversary + prorata.
   - Outstanding nominal adalah nominal klaim yang belum tercatat dibayar.

6. FORMAT TANGGAL
   Tampilan: DD MMM YY, contoh 09 Mei 23.
   Input menerima:
   - 09 Mei 23
   - 09 May 23
   - 09/05/2023
   - 2023-05-09

7. PEMBAYARAN
   Menampilkan jelas:
   - Tanggal Bayar
   - No. Bukti Bayar
   - No Invoice
   - Nominal
   - Sumber
   Data pembayaran awal dari Excel tetap tersedia.

8. LOG AUDIT
   Tidak kosong lagi.
   Menampilkan:
   - Jejak DATA AWAL per No. Tagihan
   - Import AMBIL
   - Import KEMBALI
   - Update MASTER
   - DITERIMA / REJECT dan alasannya

9. BACKUP / RESTORE
   - Nama file backup bisa diisi sendiri.
   - Tombol "Pilih Lokasi & Simpan Backup" membuka dialog browser untuk memilih
     folder dan nama file (Edge/Chrome di localhost).
   - Restore bisa memilih file melalui dialog.
   - backup_data_awal.json tetap tersedia sebagai baseline.
   - "Kembalikan Data Awal" tetap tersedia.

CARA MULAI
1. Extract ZIP ke folder tetap.
2. Double-click MULAI_APP.bat.
3. Gunakan Edge/Chrome.
4. Buka http://127.0.0.1:8765 jika browser tidak terbuka otomatis.
5. Setelah mulai bekerja, buat Backup JSON.

PENTING
Jangan menghapus Site Data / IndexedDB browser sebelum membuat backup JSON.
