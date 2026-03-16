# Dokumentasi Alur Kerja Surat Jalan (Menyeluruh)

Dokumen ini menjelaskan alur kerja modul Surat Jalan secara end-to-end, dengan fokus utama pada halaman daftar (`index`) dan keterkaitannya ke proses create, edit, print, status, export, serta integrasi ke modul lain.

## 1. Ruang Lingkup

Cakupan dokumen:
- Halaman daftar Surat Jalan (`surat-jalan.index`)
- Alur filter, pencarian, pagination, dan export
- Aksi per baris (detail, edit, print, cancel, delete, memo, preprinted, tagihan vendor)
- Permission/middleware per endpoint
- Logika status dan status pembayaran
- Interaksi frontend (dropdown, AJAX update status)
- Dampak bisnis ke data `orders` saat update/delete Surat Jalan
- Alur lanjutan sampai Uang Jalan -> Pranota Uang Jalan -> Pembayaran Pranota Uang Jalan

Referensi implementasi utama:
- `resources/views/surat-jalan/index.blade.php`
- `app/Http/Controllers/SuratJalanController.php`
- `app/Models/SuratJalan.php`
- `routes/web.php`

## 2. Aktor dan Hak Akses

Aktor utama:
- Staff operasional
- Admin
- User dengan role keuangan/vendor (terkait tagihan)

Permission kunci (berdasarkan middleware/Blade `@can`):
- `surat-jalan-view`: lihat daftar/detail/print
- `surat-jalan-create`: buat Surat Jalan (dengan order/tanpa order)
- `surat-jalan-update`: edit Surat Jalan, update status via AJAX
- `surat-jalan-delete`: hapus Surat Jalan
- `surat-jalan-export`: export Excel
- `tagihan-supir-vendor-create`: buat tagihan vendor dari Surat Jalan
- `audit-log-view`: lihat audit log pada tombol audit

## 3. Peta Endpoint (Route Map)

Route inti Surat Jalan:
- `GET /surat-jalan` -> `SuratJalanController@index` -> `surat-jalan.index`
- `GET /surat-jalan/select-order` -> `selectOrder`
- `GET /surat-jalan/create-without-order` -> `createWithoutOrder`
- `POST /surat-jalan/store-without-order` -> `storeWithoutOrder`
- `GET /surat-jalan/create` -> `create` (butuh `order_id`)
- `POST /surat-jalan` -> `store`
- `GET /surat-jalan/{suratJalan}` -> `show`
- `GET /surat-jalan/{suratJalan}/edit` -> `edit`
- `PUT/PATCH /surat-jalan/{suratJalan}` -> `update`
- `DELETE /surat-jalan/{suratJalan}` -> `destroy`
- `GET /surat-jalan/export` -> `exportExcel`
- `POST /surat-jalan/{suratJalan}/update-status` -> `updateStatus` (AJAX)
- `POST /surat-jalan/{suratJalan}/update-supir` -> `updateSupir` (endpoint ringan)
- `GET /surat-jalan/{suratJalan}/print` -> `print`
- `GET /surat-jalan/{suratJalan}/download` -> `downloadPdf`
- `GET /surat-jalan/{suratJalan}/print-memo` -> `printMemo`
- `GET /surat-jalan/{suratJalan}/print-preprinted` -> `printPreprinted`
- `GET /surat-jalan/generate-nomor` -> `generateNomorSuratJalan`

## 4. Alur Halaman Index (Daftar Surat Jalan)

### 4.1 Halaman Dibuka

1. User akses `surat-jalan.index`.
2. Controller `index()` membangun query `SuratJalan::query()`.
3. Controller menerapkan filter dari request.
4. Controller load relasi:
   - `order`
   - `tagihanSupirVendor.invoice`
   - `withCount('pranotaUangRit')`
5. Data diurutkan:
   - `created_at DESC`
   - `tanggal_surat_jalan DESC`
   - `id DESC`
6. Data dipaginasi `15` per halaman.
7. View `resources/views/surat-jalan/index.blade.php` dirender.

### 4.2 Filter dan Pencarian

Input filter di form GET:
- `search`
- `status`
- `status_pembayaran`
- `tipe_kontainer`
- `start_date`
- `end_date`

Logika backend:
- `search`: cari ke beberapa kolom (`no_surat_jalan`, `pengirim`, `alamat`, `jenis_barang`, `tipe_kontainer`, `no_kontainer`, `no_plat`, `supir`)
- `status`: filter langsung jika bukan `all`
- `status_pembayaran`: logika gabungan `status_pembayaran` + `status_pembayaran_uang_jalan`
- `tipe_kontainer`: exact match
- `start_date`/`end_date`: `whereDate` rentang tanggal `tanggal_surat_jalan`

Perilaku UI:
- Nilai filter persisten lewat `request('...')`
- Tombol `Reset` muncul saat ada query parameter filter
- Tombol `Download Excel` membawa query filter aktif agar export konsisten dengan tampilan

### 4.3 Tabel dan Kolom Status

Tabel menampilkan kolom operasional utama, termasuk:
- No SJ, tanggal, pengirim, tujuan, barang, kontainer, supir
- Status Surat Jalan
- Status Pembayaran (overall)
- Status Pranota Vendor

Sumber logika status:
- `status_badge` -> accessor `getStatusBadgeAttribute()` di model
- `overall_status_pembayaran` -> accessor `getOverallStatusPembayaranAttribute()`
- `vendor_invoice_status` -> accessor `getVendorInvoiceStatusAttribute()`

Mapping ringkas:
- Status Surat Jalan:
  - `draft`, `active`, `completed`, `cancelled`, dan status checkpoint
- Overall pembayaran:
  - `sudah_dibayar`, `belum_dibayar`, `belum_masuk_pranota`
- Vendor invoice status:
  - `belum_tagihan`, `sudah_tagihan`, `sudah_invoice`, `sudah_pranota`

### 4.4 Aksi Per Baris

Ada dua area aksi:
- Dropdown `Actions` (ikon plus)
- Kolom `Aksi` (ikon cepat)

Aksi tersedia:
- Detail (`show`)
- Edit (`edit`)
- Print Surat Jalan (`print`)
- Print Memo (`printMemo`)
- Print Preprinted (`printPreprinted`)
- Cancel (ubah status ke `cancelled` via AJAX)
- Delete (`destroy`)
- Audit log (jika punya permission)
- Buat Tagihan Vendor (kondisional)

Kondisi khusus Tagihan Vendor:
- Tombol hanya tampil jika user punya `tagihan-supir-vendor-create`
- Hanya tampil jika data `TagihanSupirVendor` untuk `surat_jalan_id` tersebut belum ada

### 4.5 Empty State

Jika data kosong (`@forelse ... @empty`):
- Tampilkan pesan belum ada data
- Tampilkan CTA menuju `surat-jalan.select-order`

### 4.6 Pagination

Jika data lebih dari 1 halaman:
- Render `components.modern-pagination`
- Render `components.rows-per-page`

## 5. Interaksi Frontend (JavaScript) di Index

### 5.1 Dropdown Action (Anti Clipping)

Fungsi `toggleDropdown(event, dropdownId)`:
1. Menutup dropdown lain lebih dulu.
2. Memindahkan dropdown aktif ke `document.body` saat dibuka.
3. Alasan: mencegah dropdown terpotong parent dengan `overflow-x-auto`.
4. Posisi dihitung dari `button.getBoundingClientRect()`.
5. Posisi disesuaikan agar tidak keluar viewport (kanan/bawah).
6. Saat ditutup, dropdown dikembalikan ke parent asal (`__origParent`).

### 5.2 Update Status (AJAX)

Fungsi `updateStatus(suratJalanId, status)`:
1. Minta konfirmasi user.
2. Kirim `POST` ke `/surat-jalan/{id}/update-status`.
3. Sertakan CSRF token dari meta tag.
4. Jika `success`, lakukan `location.reload()`.
5. Jika gagal, tampilkan alert error.

Backend `updateStatus()`:
- Validasi status: `draft|active|completed|cancelled`
- Simpan status baru
- Return JSON `success: true/false`

### 5.3 Print Preprinted

Fungsi `printPreprinted(suratJalanId)`:
- Buka tab baru `/surat-jalan/{id}/print-preprinted`

### 5.4 Close on Outside Click

Event listener dokumen:
- Klik di luar wrapper dropdown -> semua dropdown ditutup dan dikembalikan ke parent asal.

### 5.5 Resizable Table

Pada `document.ready`:
- Menjalankan `initResizableTable('suratJalanTable')`
- Mendukung resize kolom tabel secara manual.

## 6. Alur Proses Bisnis Terkait

### 6.1 Create Surat Jalan dari Order

1. User buka `selectOrder`.
2. Pilih order aktif/confirmed/processing.
3. Masuk ke form `create` dengan `order_id`.
4. `store` simpan data Surat Jalan.
5. Sistem redirect ke index dengan flash success.

Catatan data pendukung di form:
- Supir/kenek/krani dari `karyawans`
- Kegiatan dari `master_kegiatans`
- Kontainer digabung dari `stock_kontainers` + `kontainers`

### 6.2 Create Surat Jalan Tanpa Order

1. User buka `createWithoutOrder`.
2. Isi form manual (pengirim, barang, tujuan, supir, plat, dll).
3. `storeWithoutOrder` simpan `order_id = null`.
4. Default status awal:
   - `status = draft`
   - `status_pembayaran = belum_masuk_pranota`
5. Jika terdeteksi supir customer, sistem mencoba auto-create data `Prospek`.

### 6.3 Update Surat Jalan

1. User buka halaman edit.
2. User submit perubahan.
3. Controller `update` validasi dan transform data (misal array kontainer jadi CSV).
4. Jika ada perubahan `tujuan_pengiriman` dan surat jalan terkait order, order dapat ikut diperbarui.
5. Jika jumlah kontainer berubah:
   - Bila Surat Jalan sudah approved, order units disesuaikan.
   - Bila belum approved, hanya simpan perubahan tanpa proses unit order.

### 6.4 Delete Surat Jalan

1. User klik delete (dengan konfirmasi).
2. Controller `destroy` hapus data Surat Jalan (+ file gambar jika ada).
3. Jika Surat Jalan pernah approved, unit order dikembalikan (`sisa` ditambah) + history dipush.
4. Redirect ke index dengan flash message.

## 7. Integrasi Modul Lain

Integrasi yang tampak dari modul index:
- Tagihan Supir Vendor (`tagihan-supir-vendor.create`)
- Invoice Vendor (via relasi `tagihanSupirVendor.invoice`)
- Audit Log (via komponen `components.audit-log-modal`)
- Pranota Uang Rit (withCount)
- Uang Jalan (status gabungan memengaruhi badge pembayaran)

## 8. Skenario End-to-End (Operasional Harian)

1. Staff buka daftar Surat Jalan.
2. Staff cari data via filter (tanggal, status, pembayaran, kata kunci).
3. Staff review baris data + status badge.
4. Staff lakukan aksi:
   - print dokumen,
   - edit data,
   - cancel jika batal,
   - atau delete jika perlu.
5. Bila butuh billing vendor, buat tagihan vendor dari baris terkait.
6. Jika butuh pelaporan, export Excel sesuai filter aktif.
7. User verifikasi jejak perubahan dari audit log (jika diizinkan).

## 9. Catatan Teknis Penting

- Query `TagihanSupirVendor::where(...)->exists()` dipanggil di view per baris; pada data besar ini dapat menambah beban query.
- Dropdown action menggunakan teknik append ke `body`; ini efektif untuk menghindari clipping tabel horizontal.
- Update status menggunakan AJAX terpisah dari update form penuh, jadi lebih ringan untuk aksi cepat cancel/ubah status.
- Permission enforcement dilakukan di route middleware, dan beberapa method juga melakukan pengecekan eksplisit tambahan.

## 10. Alur Menyeluruh Sampai Pembayaran Pranota Uang Jalan

Bagian ini adalah alur bisnis lengkap dari data Surat Jalan sampai status pembayaran uang jalan menjadi lunas.

### 10.1 Tahap 1: Surat Jalan Tersedia

Prasyarat data:
- Surat Jalan sudah dibuat dan valid untuk operasional.
- Untuk alur Uang Jalan reguler, Surat Jalan harus:
   - memiliki `order_id` (bukan surat jalan tanpa order tertentu yang tidak eligible),
   - bukan `supir customer`,
   - `status_pembayaran_uang_jalan = belum_ada`.

Endpoint relevan:
- `GET /surat-jalan`
- `GET /surat-jalan/{id}`
- `GET /surat-jalan/{id}/edit`

### 10.2 Tahap 2: Pembuatan Uang Jalan

Alur umum:
1. User ke modul Uang Jalan (`uang-jalan`).
2. User pilih Surat Jalan dari halaman select (`select-surat-jalan`).
3. User isi form Uang Jalan dan simpan.
4. Sistem membuat 1 record Uang Jalan untuk 1 Surat Jalan.

Perubahan status saat Uang Jalan dibuat:
- Pada record `uang_jalans`:
   - `status = belum_masuk_pranota`
- Pada record `surat_jalans` (reguler):
   - `status_pembayaran_uang_jalan = sudah_masuk_uang_jalan`

Endpoint relevan:
- `GET /uang-jalan`
- `GET /uang-jalan/create` (berbasis `surat_jalan_id`)
- `POST /uang-jalan`

Permission utama:
- `uang-jalan-view`
- `uang-jalan-create`

### 10.3 Tahap 3: Pembentukan Pranota Uang Jalan

Alur umum:
1. User buka `pranota-uang-jalan.create`.
2. Sistem menampilkan daftar Uang Jalan yang belum punya pranota:
   - belum memiliki relasi di `pranota_uang_jalan_items`,
   - status `belum_dibayar` atau `belum_masuk_pranota`.
3. User pilih satu atau lebih Uang Jalan.
4. Sistem generate nomor pranota format `PUJ-MMYY-XXXXXX`.
5. Sistem simpan pranota dengan status awal `unpaid`.
6. Sistem attach item ke pivot `pranota_uang_jalan_items`.
7. Sistem update status Uang Jalan terpilih menjadi `sudah_masuk_pranota`.

Field penting pranota:
- `nomor_pranota`
- `tanggal_pranota`
- `periode_tagihan`
- `total_amount`
- `penyesuaian`
- `status_pembayaran` (`unpaid`, `paid`, `cancelled`)

Endpoint relevan:
- `GET /pranota-uang-jalan`
- `GET /pranota-uang-jalan/create`
- `POST /pranota-uang-jalan`
- `POST /pranota-uang-jalan/{pranota}/update-total`
- `GET /pranota-uang-jalan/export`

Permission utama:
- `pranota-uang-jalan-view`
- `pranota-uang-jalan-create`
- `pranota-uang-jalan-update`
- `pranota-uang-jalan-delete`
- `pranota-uang-jalan-export`

### 10.4 Tahap 4: Pembayaran Pranota Uang Jalan

Alur umum:
1. User buka `pembayaran-pranota-uang-jalan.create`.
2. Sistem hanya menampilkan pranota yang:
   - `status_pembayaran = unpaid`,
   - belum punya relasi pembayaran.
3. User pilih satu atau lebih pranota untuk dibayar.
4. Sistem generate nomor pembayaran format `SIS-MM-YY-NNNNNN`.
5. Sistem simpan 1 header pembayaran, lalu attach banyak pranota ke pivot `pembayaran_pranota_uang_jalan_items`.
6. Sistem update setiap pranota terpilih menjadi `paid`.
7. Sistem update setiap Uang Jalan dalam pranota menjadi `lunas`.
8. Sistem update Surat Jalan terkait menjadi:
   - `status_pembayaran_uang_jalan = dibayar`
   - `status = belum masuk checkpoint`
9. Sistem catat jurnal double-entry COA (`Bank/Kas` vs `Biaya Uang Jalan Muat`) sesuai jenis transaksi.

Endpoint relevan:
- `GET /pembayaran-pranota-uang-jalan`
- `GET /pembayaran-pranota-uang-jalan/create`
- `GET /pembayaran-pranota-uang-jalan/generate-nomor`
- `POST /pembayaran-pranota-uang-jalan`
- `GET /pembayaran-pranota-uang-jalan/{id}`
- `GET /pembayaran-pranota-uang-jalan/{id}/edit`
- `PUT /pembayaran-pranota-uang-jalan/{id}`
- `DELETE /pembayaran-pranota-uang-jalan/{id}`

Permission utama:
- `pembayaran-pranota-uang-jalan-view`
- `pembayaran-pranota-uang-jalan-create`
- `pembayaran-pranota-uang-jalan-edit`
- `pembayaran-pranota-uang-jalan-delete`

### 10.5 Dampak ke Halaman Surat Jalan Index

Setelah pembayaran pranota uang jalan sukses:
- Badge `overall_status_pembayaran` di index akan mengarah ke `sudah_dibayar` (karena `status_pembayaran_uang_jalan = dibayar`).
- Kolom pembayaran di tabel Surat Jalan berubah ke tampilan lunas/dibayar.
- Data tidak lagi termasuk kelompok yang "belum masuk pranota" atau "belum dibayar" untuk komponen uang jalan.

### 10.6 Diagram Transisi Status

Transisi inti yang terjadi antar modul:

1. `surat_jalans.status_pembayaran_uang_jalan`:
    - `belum_ada` -> `sudah_masuk_uang_jalan` -> `dibayar`

2. `uang_jalans.status`:
    - `belum_masuk_pranota` -> `sudah_masuk_pranota` -> `lunas`

3. `pranota_uang_jalans.status_pembayaran`:
    - `unpaid` -> `paid`

## 11. Checklist Uji Fungsional (Disarankan)

Checklist minimal untuk memastikan alur berjalan:
- User tanpa `surat-jalan-view` tidak bisa akses index.
- Filter kombinasi bekerja dan reset menghapus filter.
- Export menghasilkan data sesuai filter.
- Dropdown action tidak terpotong saat tabel discroll horizontal.
- Update status via AJAX berhasil dan menampilkan hasil reload.
- Delete hanya bisa oleh role dengan `surat-jalan-delete`.
- Tombol tagihan vendor muncul/hilang sesuai kondisi data.
- Badge pembayaran dan vendor status sesuai data relasi.
- Pagination dan rows-per-page berfungsi.

Checklist tambahan alur sampai pembayaran pranota uang jalan:
- Uang Jalan hanya bisa dibuat untuk Surat Jalan yang eligible dan belum punya Uang Jalan.
- Setelah Uang Jalan dibuat, `status_pembayaran_uang_jalan` Surat Jalan menjadi `sudah_masuk_uang_jalan`.
- Pranota hanya menarik Uang Jalan yang belum dipranota.
- Setelah Pranota dibuat, status Uang Jalan pindah ke `sudah_masuk_pranota`.
- Pembayaran hanya bisa memilih pranota `unpaid`.
- Setelah pembayaran sukses:
   - pranota jadi `paid`,
   - uang jalan jadi `lunas`,
   - surat jalan jadi `status_pembayaran_uang_jalan = dibayar`.
- Nomor pembayaran ter-generate format `SIS-MM-YY-NNNNNN`.
- Jurnal double-entry COA tercatat untuk transaksi pembayaran.

## 12. Referensi File Detail

- Halaman daftar: `resources/views/surat-jalan/index.blade.php`
- Controller utama: `app/Http/Controllers/SuratJalanController.php`
- Controller Uang Jalan: `app/Http/Controllers/UangJalanController.php`
- Controller Pranota Uang Jalan: `app/Http/Controllers/PranotaSuratJalanController.php`
- Controller Pembayaran Pranota Uang Jalan: `app/Http/Controllers/PembayaranPranotaUangJalanController.php`
- Model status/relasi: `app/Models/SuratJalan.php`
- Model Pranota: `app/Models/PranotaUangJalan.php`
- Model Pembayaran Pranota: `app/Models/PembayaranPranotaUangJalan.php`
- Definisi route: `routes/web.php`
- Modal audit log: `resources/views/components/audit-log-modal.blade.php`

---

Dokumen ini dibuat berdasarkan implementasi aktual pada kode saat ini. Jika ada perubahan route, permission, atau relasi model, dokumen perlu diperbarui agar tetap sinkron.
