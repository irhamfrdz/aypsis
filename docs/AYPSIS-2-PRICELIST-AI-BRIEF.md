# Instruksi AI: Seluruh Pricelist AYPSIS Terpusat untuk Rebuild AYPSIS-2

## Cara menggunakan dokumen

Salin dokumen ini ke repository `aypsis-2`, lalu berikan instruksi berikut kepada AI yang mempunyai akses ke project tersebut:

> Baca AGENTS.md, AYPSIS-2-PRICELIST-AI-BRIEF.md, dan AYPSIS-2-PRICELIST-INVENTORY.md. Cakupan saya seluruh pricelist AYPSIS, bukan hanya biaya kapal. Kerjakan tahap audit dan blueprint terlebih dahulu, berdasarkan kode aktual project ini dan inventaris project lama. Jangan implementasi sebelum saya menyetujui blueprint. Jelaskan temuan, pilihan arsitektur, risiko, dan keputusan bisnis yang masih perlu jawaban. Setelah saya menyetujui implementasi, kerjakan bertahap sesuai ruang lingkup yang disetujui dan jalankan pengujian yang relevan.

Dokumen ini adalah brief untuk AI, bukan script shell atau migrasi database. Saat ini hanya repository AYPSIS lama yang sudah ditinjau; kondisi AYPSIS-2 belum diperiksa. Jangan menganggap nama tabel, stack, atau fitur lama sudah tersedia di rebuild.

## 1. Tujuan dan batas ruang lingkup

Saya sedang membangun ulang AYPSIS menjadi AYPSIS-2. Saya ingin mengurangi pembuatan tabel pricelist, field form, dan kode berulang untuk seluruh jenis tarif di project, termasuk biaya kapal, uang jalan, rit, OB, sewa kontainer, vendor supir, kanisir ban, cat, lembur, gate in, tarif Batam, dan kandidat tarif yang tersimpan di master lain.

Target arsitektur:

- Satu sistem pricelist terpusat untuk seluruh domain AYPSIS, dengan scope dan aturan per domain.
- Tabel master dan tabel detail tarif bersama, bukan tabel baru untuk setiap modul.
- Field tambahan dapat dikonfigurasi berdasarkan metadata.
- Pola perhitungan umum dapat dipakai ulang.
- Aturan bisnis kompleks tetap ditangani service yang eksplisit.
- Transaksi historis tetap dapat ditelusuri meskipun tarif atau konfigurasi berubah.

"Pricelist dijadikan satu" berarti satu model pengelolaan bersama, bukan memaksakan seluruh informasi ke tepat satu tabel fisik.

Semua pricelist masuk ruang lingkup audit dan blueprint. Implementasi tetap bertahap; jangan memaksakan seluruh domain memakai form transaksi, calculator, atau workflow pembayaran yang sama. Jangan menjalankan migrasi atau koreksi terhadap production selama tahap perancangan.

Gunakan lampiran `AYPSIS-2-PRICELIST-INVENTORY.md` sebagai daftar penelusuran. Setiap entri harus diberi keputusan: digabung ke rate bersama, menjadi detail/komponen/rule, dipertahankan khusus, atau legacy/tidak digunakan dengan bukti. Jangan menghilangkan entri hanya karena namanya tidak terkait biaya kapal.

## 2. Konteks AYPSIS lama

Repository lama menggunakan Laravel, Blade, dan database relasional. Form biaya kapal memiliki partial terpisah untuk THC, LOLO, Storage, Demurrage, Buruh, Temas, Meratus, Tanto, Pelindo, dan lainnya.

Referensi jika repository lama tersedia:

- `resources/views/biaya-kapal/create.blade.php`
- `resources/views/biaya-kapal/create/`
- `app/Http/Controllers/BiayaKapalController.php`
- `app/Models/BiayaKapal.php`
- `app/Services/TemasBillingService.php`
- `app/Services/MeratusBillingService.php`
- `app/Services/SharedShippingDpService.php`

Fitur yang perlu diperiksa kesetaraannya di rebuild:

- Multi kapal/voyage dan beberapa baris biaya dalam satu dokumen.
- Hubungan BL dan kontainer.
- Tarif per unit, per kontainer, per ton, per hari, atau nilai tetap.
- Estimasi biaya, tagihan aktual, pajak/potongan, dan penyesuaian.
- DP, saldo DP, pelunasan, serta penggunaan DP lintas Temas/Meratus jika masih menjadi kebutuhan.
- Pembayaran sebagian, cetak, export, laporan, permission, dan audit trail.

Daftar ini adalah konteks untuk audit. Jangan menganggap semua fitur wajib dibuat ulang sekaligus.

## 3. Tahap pertama: audit project rebuild

Sebelum mendesain tabel atau menulis kode:

1. Baca instruksi repository dan periksa stack, pola arsitektur, skema database, serta pengujian yang tersedia.
2. Petakan seluruh modul pengguna pricelist: biaya kapal, transportasi/uang jalan/rit, OB, pergudangan, sewa, perawatan, invoice aktivitas lain, dan pembayaran yang sudah ada. Audit tarif di master yang tidak bernama pricelist juga.
3. Identifikasi struktur yang bisa dipakai ulang; jangan membuat master atau sistem pembayaran tandingan. Tentukan apakah tarif merupakan biaya beli, harga jual, kompensasi operasional, atau tarif internal. Jangan mencampurnya hanya karena parameter dan nominal serupa.
4. Pisahkan fakta dari kode, asumsi rancangan, dan keputusan bisnis yang belum disepakati.
5. Buat matriks per modul: parameter, sumber data, satuan, rumus, kondisi tarif, alur pembayaran, dan kebutuhan khusus. Cocokkan seluruh 35 model Pricelist/Tarif serta 33 entri menu Master Tarif dari inventaris lama; detail/riwayat bukan jenis tarif mandiri. Tambahkan temuan baru dari query langsung, API/JavaScript, atau master tanpa nama pricelist.
6. Jika kode lama tidak dapat diakses, sebutkan keterbatasannya. Jangan mengklaim telah mengauditnya.

Output tahap ini: blueprint dan daftar keputusan, bukan implementasi.

## 4. Pisahkan empat konsep

1. **Jenis tarif/layanan:** identitas bisnis seperti THC, buruh bongkar, uang jalan, atau sewa; hubungkan ke klasifikasi biaya jika relevan.
2. **Definisi input:** field apa yang diperlukan untuk menghitung biaya.
3. **Pricelist:** kumpulan tarif vendor/lokasi/periode tertentu.
4. **Calculator dan workflow:** cara menghitung biaya serta proses persetujuan/pembayarannya.

Contoh: perubahan tarif THC vendor A tidak perlu membuat definisi form THC baru. Perubahan nilai tarif juga tidak perlu membuat calculator baru.

Nama tampilan bukan identitas teknis. Gunakan key stabil dan relasi yang sesuai; hindari percabangan bisnis berdasarkan pencocokan teks nama biaya.

## 5. Rancangan data awal yang harus dievaluasi

Nama berikut bersifat konseptual. Sesuaikan dengan konvensi dan master AYPSIS-2 yang sudah ada.

| Entitas | Tanggung jawab |
|---|---|
| Jenis biaya / cost types | Identitas stabil, klasifikasi, pilihan calculator, referensi definisi input |
| Pricelists | Header daftar tarif, vendor, versi, periode berlaku, status draft/published/retired |
| Pricelist rates | Baris tarif, satuan, kondisi penerapan, parameter khusus, referensi pricelist |
| Rate components / tiers | Komponen uang jalan, tarif dasar/penyesuaian BBM, tingkatan massa/hari, atau harga harian/bulanan bila diperlukan |
| Field definitions | Definisi input bersama per jenis biaya dan versi schema |
| Cost documents dan items | Header transaksi dan baris biaya dengan nilai utama yang terstruktur |
| Estimate/actual allocations | Hubungan rincian estimasi dengan rincian aktual jika diperlukan |
| Payment/DP allocations | Hubungan pembayaran dan penggunaan DP; manfaatkan sistem yang sudah ada |

Definisi input tidak wajib melekat langsung pada setiap header pricelist. Evaluasi agar versi tarif tidak menggandakan konfigurasi form yang sama.

Bedakan masa berlaku dan scope header dengan kondisi per baris. Tentukan satu sumber kebenaran supaya vendor, lokasi, atau tanggal yang disimpan di dua tempat tidak saling bertentangan.

### Kolom relasional dan JSON

Simpan sebagai kolom/relasi biasa untuk data yang membutuhkan integritas, pencarian rutin, pengelompokan laporan, atau foreign key: identitas transaksi, jenis biaya, vendor, kapal/voyage, mata uang jika dibutuhkan, nilai total, status, pembayaran, dan referensi tarif.

Gunakan JSON untuk kondisi atau parameter tambahan yang bervariasi. Jangan menyimpan seluruh invoice, relasi kontainer, atau ledger pembayaran hanya sebagai JSON.

AI harus menjelaskan:

- Field mana yang menjadi kolom dan mana yang JSON, beserta alasannya.
- Indeks yang mengikuti query aktual.
- Cara membatasi key dan memvalidasi tipe, satuan, rentang, serta referensi dalam JSON.
- Cara menyimpan tarif bertingkat; jangan dipaksakan ke satu angka tarif.
- Unique constraint dan pemeriksaan overlap yang sesuai model bisnis.

## 6. Pencarian tarif harus deterministik

Rancang service pemilihan tarif berdasarkan parameter yang relevan, seperti jenis biaya, vendor, lokasi/rute, tanggal acuan, ukuran, tipe kontainer, atau kategori barang.

Tentukan bersama pengguna:

- Tanggal acuan tarif: tanggal layanan, tanggal keberangkatan, atau tanggal invoice.
- Aturan batas awal/akhir masa berlaku.
- Arti kondisi kosong: berlaku umum atau data belum lengkap.
- Prioritas tarif khusus dibanding tarif umum.
- Penanganan tarif tidak ditemukan dan tarif ambigu.

Jangan otomatis memilih baris pertama, ID terbesar, atau tarif termurah ketika beberapa tarif cocok. Tolak konfigurasi ambigu atau gunakan urutan prioritas yang sudah disepakati dan dapat diaudit.

Validasi publikasi pricelist harus mengurangi potensi konflik sebelum digunakan transaksi.

## 7. Form dinamis dan calculator

Komponen input awal: number, money, select, date, text, dan textarea. Evaluasi tambahan untuk lookup master, dropdown bergantung, boolean, serta kelompok/baris berulang.

Lookup kapal, voyage, BL, kontainer, dan vendor harus memakai sumber data serta permission yang benar. Jangan membuatnya sekadar pilihan teks statis.

Pola calculator yang dapat dipakai ulang:

- Nilai tetap.
- Kuantitas × tarif.
- Tonase × tarif.
- Kuantitas × hari × tarif.
- Tarif bertingkat / minimum charge jika ada kebutuhan terverifikasi.
- Tarif harian/bulanan dan prorata sewa jika dibutuhkan; sepakati kalender, minimum periode, serta tanggal mulai/akhir.
- Tarif dengan beberapa komponen uang jalan dan penyesuaian periode BBM; jangan menjumlahkan semua kolom lama tanpa memeriksa aturan penggunaannya.
- Calculator khusus untuk aturan yang tidak sesuai pola umum.

Gunakan registry key calculator yang diizinkan aplikasi. Jangan mengeksekusi nama class bebas, SQL, JavaScript, atau PHP dari konfigurasi admin. Jangan menggunakan `eval` untuk rumus.

Hasil calculator harus memuat rincian dasar perhitungan, tarif, kuantitas/satuan, subtotal, penyesuaian, dan total yang relevan. Total di server menjadi sumber kebenaran; preview browser harus konsisten dengannya.

Definisikan presisi uang, presisi kuantitas/tarif, dan urutan pembulatan secara eksplisit. Hindari ketergantungan pada floating point biner untuk nilai finansial. Jangan sekadar menamai tipe `FinancialDecimal` tanpa implementasi konkret.

## 8. Historis, versi, dan pengendalian perubahan

Saat transaksi dikonfirmasi, simpan snapshot parameter, tarif, kondisi yang terpilih, versi definisi input, versi calculator, serta rincian hasil hitung.

- Perubahan tarif tidak menghitung ulang invoice lama secara diam-diam.
- Definisi field baru tidak membuat data lama gagal ditampilkan atau dicetak.
- Perubahan calculator tidak mengubah arti hasil historis.
- Tarif terpakai tidak dihapus permanen sembarangan.
- Override tarif memerlukan permission, alasan, dan audit sesuai kebijakan yang disepakati.
- Tentukan status transaksi yang masih boleh dihitung ulang atau diedit.

Gunakan status konfigurasi draft/published dan riwayat perubahan. Tentukan apakah publikasi harus melalui persetujuan berdasarkan kebutuhan organisasi.

## 9. Estimasi, aktual, dan pembayaran

Estimasi berbeda dengan DP. Estimasi adalah perkiraan biaya; DP adalah uang muka dengan saldo dan alokasi penggunaan.

Jangan menyamakan jumlah invoice, nominal yang dibayar, penggunaan DP, dan sisa tagihan.

Relasi estimasi dan aktual harus mendukung kebutuhan nyata: satu estimasi dapat ditagih melalui beberapa invoice, atau satu invoice mencakup beberapa estimasi. Gunakan alokasi item jika diperlukan dan cegah penghitungan ganda pada laporan.

Putuskan cara menangani:

- Invoice tanpa estimasi.
- Estimasi baru terealisasi sebagian.
- Pembatalan, revisi, retur, atau penyesuaian.
- DP yang digunakan sebagian atau pada beberapa invoice.
- Transaksi bersamaan yang mencoba menggunakan saldo DP yang sama.

Mulai laporan dengan estimasi, aktual yang dialokasikan, selisih total, dan perubahan parameter. Jika diperlukan atribusi selisih kuantitas versus tarif, sepakati metode terlebih dahulu agar hasil tidak menyesatkan.

## 10. Strategi implementasi setelah blueprint disetujui

1. Bangun fondasi pricelist bersama, pencarian tarif, versi, serta calculator umum.
2. Pilih satu modul sederhana berdasarkan audit sebagai pilot; jangan memilih otomatis hanya dari nama modul.
3. Implementasikan pengelolaan pricelist dan satu alur transaksi lengkap: create, edit, detail, cetak/export yang diperlukan, serta laporan.
4. Integrasikan dengan permission dan pembayaran yang sudah ada.
5. Tambahkan modul berikutnya berdasarkan kesamaan pola, bukan menyalin ulang implementasi.
6. Tangani Temas/Meratus atau workflow kompleks setelah fondasi dan model pembayaran terbukti sesuai.

Jika ada migrasi data lama, sertakan mapping jenis biaya dan `(tabel asal, ID asal)` ke ID baru, mode dry-run, backup, rekonsiliasi jumlah/nilai, idempotensi, dan rencana rollback. Jangan menganggap ID lama unik di seluruh tabel.

Jangan melakukan rewrite massal, menghapus tabel lama, push, deploy, atau menulis production kecuali sudah termasuk instruksi pengguna untuk tahap tersebut.

## 11. Pengujian dan kriteria penerimaan

- Dua jenis biaya dengan pola sama dapat memakai form dan calculator bersama.
- Tambah tarif dalam pola tersedia tidak membutuhkan tabel atau field HTML khusus baru.
- Perhitungan backend tidak mempercayai total yang dikirim browser.
- Tarif spesifik/umum, tanggal batas, tarif hilang, dan konflik tarif diuji.
- Snapshot historis tetap benar setelah tarif dan definisi berubah.
- Pembulatan diuji dengan angka pecahan dan batas yang relevan.
- Data JSON yang tidak dikenal atau tidak valid ditolak.
- Estimasi yang memiliki beberapa invoice tidak terhitung ganda.
- Alokasi pembayaran/DP tidak dapat melebihi saldo; transaksi bersamaan ditangani jika fitur ini masuk scope.
- Permission dan audit perubahan konfigurasi diuji.
- Pembacaan, edit yang diizinkan, cetak, dan laporan data lama tetap benar jika migrasi diperlukan.

Gunakan database uji terisolasi; jangan memakai production sebagai tempat pengujian. Cocokkan pengujian integrasi dengan perilaku database target ketika fitur JSON, constraint, atau locking berbeda dari database pengujian ringan.

## 12. Bentuk jawaban yang saya harapkan dari AI

Untuk tahap blueprint, berikan:

1. Ringkasan kondisi AYPSIS-2 dengan referensi file aktual.
2. Matriks kesamaan dan perbedaan modul yang sudah ditemukan.
3. Rancangan relasi tabel, kolom utama, indeks, dan pembagian JSON.
4. Alur pemilihan tarif, render form, validasi, kalkulasi, penyimpanan snapshot, serta integrasi pembayaran.
5. Perbandingan pilihan tepat satu tabel tarif dengan master + detail bersama.
6. Risiko, batas fleksibilitas, dan aturan bisnis yang masih perlu saya putuskan.
7. Rekomendasi pilot, tahapan implementasi, dan kriteria penerimaan.
8. Checklist seluruh entri inventaris lintas domain: sumber lama, pemakai, status aktif/legacy/belum terverifikasi, struktur target, calculator, migrasi, dan pengujian. Setiap pengecualian harus dijelaskan.

Ajukan hanya pertanyaan yang tidak bisa dijawab dari repository. Jangan menjanjikan semua modul baru bisa dibuat tanpa coding. Tujuan saya adalah mengurangi kode berulang sambil mempertahankan ketepatan perhitungan, integritas transaksi, dan kemudahan audit.
