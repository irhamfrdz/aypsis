# Pembayaran TEMAS mengikuti alur Storage

Mode pembayaran dipilih pada **Biaya Kapal → TEMAS**, per bagian kapal/voyage,
dengan rincian biaya akhir tetap per kontainer. Form tambah dan edit memakai
implementasi yang sama.

| Mode | Input | Nominal transaksi |
| --- | --- | --- |
| Bayar langsung / lunas | Rincian biaya kontainer dan pajak/biaya tambahan | Seluruh tagihan |
| DP / uang muka | Kapal, voyage, nominal DP positif | Nominal DP saja; tagihan akhir belum diketahui |
| Pelunasan DP | Referensi DP dan rincian biaya akhir kontainer | Total biaya akhir dikurangi DP **satu kali** |

Seperti mode pelunasan Storage, pajak, materai, admin, dan penyesuaian terpisah
tidak diterapkan pada pelunasan: isikan biaya akhir pada rincian kontainer.
Contoh: DP Rp300.000, tagihan akhir Rp1.000.000 → nominal pelunasan Rp700.000.
Tidak ada pemotongan DP kedua kali atau posting ke COA Transaction.

## Penyimpanan

- Tabel `biaya_kapal_temas_stages` menyimpan satu mode per bagian kapal/voyage,
  referensi `dp_stage_id`, `nilai_tagihan`, `dp_diperhitungkan`, dan `nominal_dibayar`.
- Saat DP dibuat, `nilai_tagihan = 0` berarti belum diketahui; bukan tagihan lunas nol.
  DP tidak memerlukan rincian kontainer. Satu rincian berlabel DP dicatat untuk rekap.
- Baris biaya kontainer di `biaya_kapal_temas` terhubung melalui `temas_stage_id`.
  Subtotal menyimpan biaya sebelum DP; `grand_total` menyimpan bagian nominal
  transaksi setelah DP, dialokasikan proporsional dengan pembulatan sen.
- Nominal header dan total biaya mengikuti jumlah nominal transaksi, sehingga
  rekap DP + pelunasan tidak menggandakan biaya. Cetak menampilkan potongan DP.
- Data TEMAS lama tetap memiliki `temas_stage_id = null`. Tidak ada konversi historis otomatis.

## Endpoint dan request

`GET /biaya-kapal/temas-dp-candidates` mengembalikan DP yang belum dilunasi dari
invoice aktif. Memerlukan autentikasi dan permission `biaya-kapal-create`.
Referensi DP menjadi sumber identitas kapal/voyage; server mengabaikan identitas
kapal/voyage lain yang dikirim bersama pelunasan.

POST/PUT Biaya Kapal memakai struktur `temas[index]` yang sudah ada, ditambah:

- `payment_mode`: `lunas` (default), `dp`, atau `pelunasan_dp`.
- `nominal_dibayar`: angka desimal tanpa pemisah ribuan, wajib positif untuk DP.
- `dp_stage_id`: wajib pada pelunasan, merujuk DP di tabel stages.
- Pada bayar langsung/pelunasan, array `types`, `custom_prices`, `quantities`,
  `nomor_kontainers`, dan `size_items` tetap sejajar seperti sebelumnya.
  Server menghitung total dari rincian, bukan mempercayai nominal dari browser.

DP yang sudah dipakai tidak dapat diubah/dihapus, termasuk jika pelunasannya
kemudian dihapus, agar referensi audit tetap utuh. Pelunasan yang dihapus secara
soft delete membuka kembali DP untuk pelunasan baru. Edit pelunasan menghitung
ulang selisih terhadap nominal DP asli. Tagihan akhir di bawah DP, referensi
invoice sendiri, dan penggunaan DP aktif dua kali ditolak. Nilai akhir sama dengan
DP diperbolehkan dengan nominal pelunasan nol. Row lock dan transaksi menjaga
konsistensi saat penyimpanan bersamaan.

## Modul pembayaran invoice sebelumnya

Dukungan pembayaran bertahap pada `Pembayaran Biaya Kapal` dari implementasi
sebelumnya tetap kompatibel untuk invoice lama. Untuk invoice dengan stages baru,
DP/pelunasan ditentukan di form Biaya Kapal; modul pembayaran hanya menerima
bayar penuh sebesar **nominal transaksi** tersebut. Invoice yang sudah memiliki
pembayaran aktif tetap tidak dapat diubah/dihapus sampai pembayaran dibatalkan.
Pembayaran TEMAS tidak memanggil COA saat simpan, edit, pembatalan, atau sinkronisasi.

## Migrasi dan pengujian

Jalankan migrasi tambahan ini sebelum memakai kode baru (migrasi pembayaran item
sebelumnya tetap diperlukan dan tidak dihapus):

```sh
php artisan migrate --path=database/migrations/2026_09_22_130000_create_biaya_kapal_temas_stages.php
php artisan test --filter=TemasPaymentTest
node tests/temas-container-calculation.cjs
node tests/Browser/temas-storage-payment-smoke.mjs
```

Test backend menggunakan SQLite in-memory. Test browser memakai Chrome headless
dan data manifest/DP simulasi; tidak membuat transaksi pada database aplikasi.
Penguncian MySQL antarproses belum diuji oleh test SQLite.
