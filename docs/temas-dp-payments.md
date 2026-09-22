# Backend pembayaran DP TEMAS

DP berlaku per invoice biaya kapal TEMAS. Rincian biaya tetap per kontainer; dasar
pembayaran adalah jumlah `biaya_kapal_temas.grand_total`, termasuk pajak dan biaya
tambahan yang dicatat sekali per bagian kapal/voyage. Ini pembayaran terhadap
invoice yang nilainya sudah diketahui, bukan uang muka sebelum invoice dibuat.

## Database

Jalankan migrasi khusus ini pada setiap environment sebelum kode digunakan:

```sh
php artisan migrate --path=database/migrations/2026_09_22_120000_add_temas_payment_stages_to_pembayaran_biaya_kapal_items.php
```

Tabel `pembayaran_biaya_kapal_items` menyimpan `payment_mode` (`lunas`, `dp`,
`pelunasan_dp`), `dp_item_id` (foreign key ke baris DP), `nilai_tagihan`, dan
`sisa_setelah_bayar`. Kolom `nominal` menyimpan uang yang dibayar pada transaksi
tersebut. Pembayaran lama tetap bermode `lunas`; tidak ada backfill nominal.

Pada invoice, `dp` dan `sisa_pembayaran` disinkronkan setelah pembayaran atau
pembatalan. Status invoice tetap `pending` setelah DP dan menjadi `paid` setelah
lunas, agar cocok dengan enum dan daftar pembayaran yang sudah ada.

## Kontrak endpoint

Endpoint berada di middleware autentikasi dan permission pembayaran yang sudah ada.
`GET /pembayaran-biaya-kapal/temas/{biayaKapal}/saldo` memerlukan permission
`pembayaran-biaya-kapal-view`. Respons berisi `nilai_tagihan`, `total_dibayar`,
`sisa_pembayaran`, `status`, `dp_item_id`, dan `riwayat` pembayaran aktif. Nilai uang
dikembalikan sebagai string desimal tanpa pemisah ribuan. Gunakan ringkasan ini
untuk menampilkan saldo, bukan `total_biaya` header yang mungkin berisi nilai lama.

`POST /pembayaran-biaya-kapal` memerlukan permission
`pembayaran-biaya-kapal-create` dan CSRF. Contoh DP invoice Rp1.000.000:

```json
{
  "biaya_kapal_ids": [123],
  "tanggal_pembayaran": "2026-09-22",
  "bank": "Nama bank pembayaran",
  "jenis_transaksi": "kredit",
  "payment_mode": "dp",
  "nominal_dp": "300000.00",
  "total_pembayaran": "300000.00"
}
```

Untuk pelunasan, gunakan invoice yang sama, `payment_mode: "pelunasan_dp"`,
`dp_item_id` dari ringkasan saldo, serta `total_pembayaran: "700000.00"`.
Hapus `nominal_dp`. Nominal pelunasan selalu dihitung ulang server; nominal request
harus cocok dengan saldo terbaru. Untuk pembayaran penuh tanpa DP, gunakan
`payment_mode: "lunas"` (default) dan total seluruh tagihan.

DP dan pelunasan masing-masing hanya memilih satu invoice TEMAS. Satu invoice
memiliki satu DP aktif dan satu pelunasan aktif. DP harus positif dan lebih kecil
dari tagihan; cicilan tambahan tidak termasuk alur ini. Format uang request adalah
angka desimal tanpa `Rp`/pemisah ribuan. Transaksi TEMAS menggunakan `kredit`
(uang keluar); penyesuaian dicatat di invoice sebelum pembayaran. Parameter
`total_tagihan_penyesuaian` pembayaran TEMAS harus nol.

Endpoint simpan mengikuti perilaku resource lama (redirect sukses); validation
errors dikembalikan sebagai 422 jika request meminta JSON.

## Koreksi dan pembatalan

Invoice TEMAS dengan pembayaran aktif tidak dapat diedit/dihapus. Pembayaran
TEMAS dapat diedit untuk informasi referensi/catatan, tetapi perubahan tanggal,
bank, arah transaksi, atau nilai memerlukan pembatalan dan pembuatan ulang.
Gunakan endpoint DELETE pembayaran yang sudah ada, dengan permission delete.
Batalkan pelunasan sebelum DP. Saldo dihitung ulang setelah pembatalan. Item
TEMAS dipertahankan bersama header pembayaran yang di-soft-delete sehingga jejak
DP dan pelunasannya tidak hilang; pembayaran dibatalkan tidak dihitung ke saldo.

Penulisan mengunci invoice di dalam transaksi DB untuk mencegah dua pembayaran
memakai saldo yang sama. Pembayaran TEMAS tidak terhubung ke COA Transaction:
simpan, edit, dan pembatalan tidak memposting atau menghapus jurnal. Sinkronisasi
COA manual untuk TEMAS ditolak. Pembayaran TEMAS dan invoice jenis lainnya harus
dibuat terpisah. Kolom bank hanya menjadi informasi pembayaran, tanpa validasi
atau relasi ke akun COA.

## Batas cakupan dan verifikasi

Perubahan ini menyediakan backend/database. Form pembayaran belum memiliki
pilihan DP/pelunasan atau pengambilan saldo otomatis; perlu dihubungkan ke kontrak
di atas. Riwayat yang dibatalkan tersedia di database, belum di endpoint saldo.

```sh
php artisan test --filter=TemasPaymentTest
```

Test memakai SQLite in-memory yang terisolasi, meliputi saldo, asosiasi DP,
pelunasan ganda, pembatalan, edit invoice, nominal request, serta memastikan alur
TEMAS tidak memanggil COA Transaction. Penguncian MySQL menggunakan `lockForUpdate`; race antarproses belum diuji
oleh test SQLite tersebut.
