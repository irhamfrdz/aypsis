# Inventaris Pricelist AYPSIS Lama untuk Rebuild AYPSIS-2

Tanggal inventaris: 27 November 2026. Pendamping `AYPSIS-2-PRICELIST-AI-BRIEF.md`.

## Batas bukti

Inventaris statis repository utama: model bernama Pricelist/Tarif, migrasi terkait, serta referensi tekstual pada kode aplikasi. Tidak membaca data production, tidak memastikan seluruh migrasi telah diterapkan, dan tidak menyimpulkan fitur aktif hanya dari keberadaan file. Field di bawah berasal dari fillable model, bukan hasil introspeksi schema. Referensi pemakai adalah kandidat penelusuran, termasuk import/CRUD; keberadaan referensi belum membuktikan jalur runtime aktif. Database dan rumus harus diverifikasi sebelum implementasi/migrasi.

## Keputusan arsitektur lintas domain yang perlu dibawa ke rebuild

| Kelompok | Cakupan | Hal yang tidak boleh hilang |
|---|---|---|
| Biaya kapal/pelabuhan | Air tawar, labuh tambat, dokumen, buruh, buruh bongkar, TKBM, OPP/OPT, THC, LOLO, Storage, Storage Pelindo, Freight, Perijinan, Pelindo | Satuan, vendor/lokasi, ukuran/status kontainer, multi kapal/voyage, kondisi bertingkat jika relevan |
| Pelayaran | Temas, Meratus, Tanto | Jenis komponen tarif; per kontainer versus per dokumen harus diverifikasi; workflow DP tetap terpisah dari master harga |
| Transportasi | Uang jalan, uang jalan Batam, rit, vendor supir, trucking, TarifBatam | Rute/cabang/ring, ukuran/full-empty, tarif dasar, periode BBM, komponen biaya dan tanggal berlaku |
| OB | OB dan OB antar gudang, LOLO Batam | Ukuran/status/service, gudang tujuan, konsumen tagihan/pranota dan aktivitas operasional |
| Sewa | Dua model pricelist sewa dan tabel `sk_tarif_sewas`/`sk_tarifs` dari migrasi | Bedakan tarif beli vendor dengan tarif jual customer, harian/bulanan, tipe/ukuran, periode dan prorata jika digunakan |
| Perawatan/tenaga kerja | Cat kontainer, kanisir ban, lembur | Jenis cat/ukuran, ukuran ban/material, nominal lembur dan satuannya; jangan menebak rumus dari nama field |
| Master tarif tambahan | Pelayanan pelabuhan, alat berat, asuransi, kelola BBM | Pisahkan data identitas aset/vendor dari tarif; persentase berbeda dari nominal uang; periode dan satuan harus jelas |

Ini pengelompokan rancangan, bukan keputusan bahwa semua modul menggunakan satu calculator. Untuk tarif berbentuk beberapa kolom seperti uang jalan atau kanisir, evaluasi normalisasi menjadi beberapa baris dimensi/komponen dan rekonsiliasi setiap kolom asal. Nilai nol, null, dan tidak berlaku harus dibedakan.

### Entri pendukung dan legacy

- `PricelistTarifHistory` adalah riwayat perubahan tarif, bukan jenis pricelist baru. Bawa provenance perubahan dan hubungan BBM ke rancangan versi/audit.
- `MasterPricelistBiayaStoragePelindoTarifMassa` adalah detail bertingkat, bukan pricelist mandiri. Hubungannya memakai `pricelist_id` walaupun tidak ada pada fillable.
- `MasterPricelistDemurrage` hanya berupa model kosong; migrasi create yang ditemukan hanya berisi ID dan timestamps. Tidak ada referensi model/tabel pada kode PHP lain di `app/` yang ditemukan. Jangan menyimpulkan modul demurrage tidak ada; telusuri sumber tarif aktualnya.
- `master_pricelist_tujuan_kontainer_sewas` muncul pada migrasi lama dan dihapus pada `up()` migrasi `2026_10_16_000007_remove_master_pricelist_tujuan_kontainer_sewa.php`. `down()` membuat kembali tabel ini. Jangan memperlakukannya sebagai master aktif tanpa audit database target.
- `sk_tarif_sewas` memakai vendor/tipe/ukuran dengan tarif harian dan bulanan; `sk_tarifs` memakai customer/tipe/ukuran dan ID string. Bukti ada pada dua migrasi `2026_06_29_*`. Model `SkTarifSewa` tidak ditemukan di `app/Models` utama saat audit, walaupun dirujuk `VendorKontainerSewa`. Status implementasinya harus diperiksa.
- Kedua model pricelist sewa yang ada tidak otomatis berarti keduanya masih aktif atau harus digabung datanya tanpa deduplikasi.

## 1. MasterPricelistAirTawar

- Model: `app/Models/MasterPricelistAirTawar.php`.
- Tabel menurut Eloquent: `master_pricelist_air_tawar`.
- Field fillable: `nama_agen`, `harga`, `keterangan`, `lokasi`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/MasterPricelistAirTawarController.php`, `app/Imports/MasterPricelistAirTawarImport.php`

## 2. MasterPricelistBiayaStorage

- Model: `app/Models/MasterPricelistBiayaStorage.php`.
- Tabel menurut Eloquent: `master_pricelist_biaya_storages`.
- Field fillable: `vendor`, `lokasi`, `size_kontainer`, `tarif_massa_1`, `tarif_massa_2`, `status`, `keterangan`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/MasterPricelistBiayaStorageController.php`

## 3. MasterPricelistBiayaStoragePelindo

- Model: `app/Models/MasterPricelistBiayaStoragePelindo.php`.
- Tabel menurut Eloquent: `master_pricelist_biaya_storage_pelindos`.
- Field fillable: `size_kontainer`, `status_kontainer`, `status`, `keterangan`.
- Referensi aplikasi: `app/Http/Controllers/JurnalOppOptController.php`, `app/Http/Controllers/MasterPricelistBiayaStoragePelindoController.php`, `app/Models/MasterPricelistBiayaStoragePelindoTarifMassa.php`

## 4. MasterPricelistBiayaStoragePelindoTarifMassa

- Model: `app/Models/MasterPricelistBiayaStoragePelindoTarifMassa.php`.
- Tabel menurut Eloquent: `master_pricelist_biaya_storage_pelindo_tarif_massas`.
- Field fillable: `massa`, `tarif`.
- Referensi aplikasi: `app/Models/MasterPricelistBiayaStoragePelindo.php`

## 5. MasterPricelistBiayaTrucking

- Model: `app/Models/MasterPricelistBiayaTrucking.php`.
- Tabel menurut Eloquent: `master_pricelist_biaya_trucking`.
- Field fillable: `nama_vendor`, `size`, `biaya`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/MasterPricelistBiayaTruckingController.php`

## 6. MasterPricelistDemurrage

- Model: `app/Models/MasterPricelistDemurrage.php`.
- Tabel menurut Eloquent: `master_pricelist_demurrages`.
- Field fillable: Tidak didefinisikan; wajib periksa migrasi dan penggunaan langsung..
- Referensi aplikasi: Tidak ditemukan di file PHP lain dalam `app/` melalui pencarian nama model/tabel. Perlu verifikasi status fitur.

## 7. MasterPricelistFreight

- Model: `app/Models/MasterPricelistFreight.php`.
- Tabel menurut Eloquent: `master_pricelist_freights`.
- Field fillable: `nama_barang`, `lokasi`, `vendor`, `tarif`, `status`, `keterangan`, `pelabuhan_asal_id`, `pelabuhan_tujuan_id`, `size_kontainer`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/MasterPricelistFreightController.php`, `app/Imports/MasterPricelistFreightImport.php`

## 8. MasterPricelistKanisirBan

- Model: `app/Models/MasterPricelistKanisirBan.php`.
- Tabel menurut Eloquent: `master_pricelist_kanisir_bans`.
- Field fillable: `vendor`, `harga_1000_kawat`, `harga_1000_benang`, `harga_900_kawat`, `harga_900_benang`, `status`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistKanisirBanController.php`, `app/Http/Controllers/StockBanController.php`

## 9. MasterPricelistLabuhTambat

- Model: `app/Models/MasterPricelistLabuhTambat.php`.
- Tabel menurut Eloquent: `master_pricelist_labuh_tambat`.
- Field fillable: `nama_agen`, `nama_kapal`, `lokasi`, `harga`, `keterangan`, `is_active`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/MasterPricelistLabuhTambatController.php`

## 10. MasterPricelistLembur

- Model: `app/Models/MasterPricelistLembur.php`.
- Tabel menurut Eloquent: `master_pricelist_lemburs`.
- Field fillable: `nama`, `nominal`, `keterangan`, `status`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistLemburController.php`, `app/Http/Controllers/PranotaLemburController.php`, `app/Http/Controllers/ReportLemburController.php`

## 11. MasterPricelistLolo

- Model: `app/Models/MasterPricelistLolo.php`.
- Tabel menurut Eloquent: `master_pricelist_lolos`.
- Field fillable: `vendor`, `nama_biaya`, `lokasi`, `size`, `tarif`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/MasterPricelistLoloController.php`

## 12. MasterPricelistLoloBatam

- Model: `app/Models/MasterPricelistLoloBatam.php`.
- Tabel menurut Eloquent: `master_pricelist_lolo_batams`.
- Field fillable: `size`, `tarif`, `status`, `keterangan`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Http/Controllers/LangsirBatamController.php`, `app/Http/Controllers/MasterPricelistLoloBatamController.php`, `app/Http/Controllers/PranotaLoloBatamController.php`, `app/Http/Controllers/SuratJalanBongkaranBatamController.php`, `app/Http/Controllers/SuratJalanTarikKosongBatamController.php`, `app/Http/Controllers/TagihanLoloBatamController.php`, `app/Models/TagihanLoloBatamItem.php`

## 13. MasterPricelistOb

- Model: `app/Models/MasterPricelistOb.php`.
- Tabel menurut Eloquent: `master_pricelist_ob`.
- Field fillable: `size_kontainer`, `status_kontainer`, `status_service`, `biaya`, `keterangan`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistObController.php`, `app/Http/Controllers/ObController.php`, `app/Http/Controllers/PranotaObController.php`, `app/Http/Controllers/TagihanObController.php`, `app/Imports/MasterPricelistObImport.php`, `app/Models/TagihanOb.php`

## 14. MasterPricelistObAntarGudang

- Model: `app/Models/MasterPricelistObAntarGudang.php`.
- Tabel menurut Eloquent: `master_pricelist_ob_antar_gudang`.
- Field fillable: `size_kontainer`, `status_kontainer`, `status_service`, `gudang_tujuan_id`, `biaya`, `keterangan`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistObAntarGudangController.php`, `app/Http/Controllers/ObAntarGudangController.php`, `app/Http/Controllers/ObController.php`

## 15. MasterPricelistSewaKontainer

- Model: `app/Models/MasterPricelistSewaKontainer.php`.
- Tabel menurut Eloquent: `master_pricelist_sewa_kontainers`.
- Field fillable: `vendor`, `tarif`, `ukuran_kontainer`, `harga`, `tanggal_harga_awal`, `tanggal_harga_akhir`, `keterangan`, `nomor_tagihan`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistSewaKontainerController.php`

## 16. MasterPricelistUangJalan

- Model: `app/Models/MasterPricelistUangJalan.php`.
- Tabel menurut Eloquent: `master_pricelist_uang_jalan`.
- Field fillable: `kode`, `cabang`, `wilayah`, `dari`, `ke`, `uang_jalan_20ft`, `uang_jalan_40ft`, `keterangan`, `liter`, `jarak_km`, `mel_20ft`, `mel_40ft`, `ongkos_truk_20ft`, `antar_lokasi_20ft`, `antar_lokasi_40ft`, `status`, `berlaku_dari`, `berlaku_sampai`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistUangJalanController.php`, `app/Http/Controllers/UangJalanBongkaranController.php`

## 17. MasterPricelistVendorSupir

- Model: `app/Models/MasterPricelistVendorSupir.php`.
- Tabel menurut Eloquent: `master_pricelist_vendor_supirs`.
- Field fillable: `vendor_id`, `dari`, `ke`, `jenis_kontainer`, `nominal`, `status`, `keterangan`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistVendorSupirController.php`, `app/Http/Controllers/SuratJalanController.php`, `app/Http/Controllers/TagihanSupirVendorController.php`

## 18. PricelistBiayaDokumen

- Model: `app/Models/PricelistBiayaDokumen.php`.
- Tabel menurut Eloquent: `pricelist_biaya_dokumen`.
- Field fillable: `nama_vendor`, `biaya`, `keterangan`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/InvoiceAktivitasLainController.php`, `app/Http/Controllers/PricelistBiayaDokumenController.php`, `app/Models/BiayaKapal.php`

## 19. PricelistBuruh

- Model: `app/Models/PricelistBuruh.php`.
- Tabel menurut Eloquent: `pricelist_buruh`.
- Field fillable: `barang`, `size`, `tipe`, `tarif`, `is_active`, `keterangan`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Exports/PricelistBuruhExport.php`, `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/InvoiceAktivitasLainController.php`, `app/Http/Controllers/Master/PricelistBuruhController.php`, `app/Imports/PricelistBuruhImport.php`, `app/Models/BiayaKapalBarang.php`, `app/Models/InvoiceAktivitasLain.php`

## 20. PricelistBuruhBongkar

- Model: `app/Models/PricelistBuruhBongkar.php`.
- Tabel menurut Eloquent: `pricelist_buruh_bongkars`.
- Field fillable: `size`, `lokasi`, `nominal`, `status`, `keterangan`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/Master/PricelistBuruhBongkarController.php`

## 21. PricelistCat

- Model: `app/Models/PricelistCat.php`.
- Tabel menurut Eloquent: `pricelist_cat`.
- Field fillable: `vendor`, `jenis_cat`, `tarif`, `ukuran_kontainer`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistCatImportController.php`, `app/Http/Controllers/PerbaikanKontainerController.php`, `app/Http/Controllers/PricelistCatController.php`, `app/Http/Controllers/TagihanCatController.php`, `app/Models/AuditLog.php`

## 22. PricelistGateIn

- Model: `app/Models/PricelistGateIn.php`.
- Tabel menurut Eloquent: `pricelist_gate_ins`.
- Field fillable: `pelabuhan`, `kegiatan`, `biaya`, `gudang`, `kontainer`, `muatan`, `tarif`, `status`.
- Referensi aplikasi: `app/Http/Controllers/GateInController.php`, `app/Http/Controllers/PricelistGateInController.php`, `app/Models/AuditLog.php`

## 23. PricelistMeratus

- Model: `app/Models/PricelistMeratus.php`.
- Tabel menurut Eloquent: `pricelist_meratus`.
- Field fillable: `jenis_biaya`, `lokasi`, `size`, `harga`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/PricelistMeratusController.php`, `app/Services/MeratusBillingService.php`

## 24. PricelistOppOpt

- Model: `app/Models/PricelistOppOpt.php`.
- Tabel menurut Eloquent: `pricelist_opp_opts`.
- Field fillable: `nama_barang`, `lokasi`, `status_bongkar_muat`, `tarif`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/PricelistOppOptController.php`, `app/Models/BiayaKapalOppOpt.php`

## 25. PricelistPelindo

- Model: `app/Models/PricelistPelindo.php`.
- Tabel menurut Eloquent: `pricelist_pelindos`.
- Field fillable: `kegiatan`, `lokasi`, `ukuran`, `tarif`, `keterangan`, `status`, `status_kontainer`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/PricelistPelindoController.php`, `app/Http/Controllers/TagihanPelindoController.php`, `app/Models/TagihanPelindoItem.php`

## 26. PricelistPerijinan

- Model: `app/Models/PricelistPerijinan.php`.
- Tabel menurut Eloquent: `pricelist_perijinans`.
- Field fillable: `nama`, `tarif`, `lokasi`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/MasterPricelistPerijinanController.php`, `app/Models/BiayaKapalPerijinanDetail.php`

## 27. PricelistRit

- Model: `app/Models/PricelistRit.php`.
- Tabel menurut Eloquent: `pricelist_rit`.
- Field fillable: `tujuan`, `tarif`, `keterangan`, `status`, `created_by`, `updated_by`.
- Referensi aplikasi: `app/Http/Controllers/MasterPricelistRitImportController.php`, `app/Http/Controllers/PricelistRitController.php`

## 28. PricelistSewaKontainer

- Model: `app/Models/PricelistSewaKontainer.php`.
- Tabel menurut Eloquent: `pricelist_sewa_kontainers`.
- Field fillable: `vendor`, `tarif`, `ukuran_kontainer`, `tanggal_harga_awal`, `tanggal_harga_akhir`, `keterangan`, `harga`.
- Referensi aplikasi: `app/Http/Controllers/PricelistSewaKontainerController.php`, `app/Models/AuditLog.php`

## 29. PricelistTanto

- Model: `app/Models/PricelistTanto.php`.
- Tabel menurut Eloquent: `pricelist_tanto`.
- Field fillable: `jenis_biaya`, `lokasi`, `size`, `harga`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/PricelistTantoController.php`

## 30. PricelistTarifHistory

- Model: `app/Models/PricelistTarifHistory.php`.
- Tabel menurut Eloquent: `pricelist_tarif_history`.
- Field fillable: `pricelist_uang_jalan_batam_id`, `kelola_bbm_id`, `tarif_lama`, `tarif_baru`, `persentase_perubahan`, `persentase_bbm`, `keterangan`.
- Referensi aplikasi: `app/Http/Controllers/KelolaBbmController.php`

## 31. PricelistTemas

- Model: `app/Models/PricelistTemas.php`.
- Tabel menurut Eloquent: `pricelist_temas`.
- Field fillable: `jenis_biaya`, `lokasi`, `size`, `harga`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/PricelistTemasController.php`, `app/Services/TemasBillingService.php`

## 32. PricelistThc

- Model: `app/Models/PricelistThc.php`.
- Tabel menurut Eloquent: `pricelist_thcs`.
- Field fillable: `nama_barang`, `lokasi`, `vendor`, `tarif`, `status`.
- Referensi aplikasi: `app/Exports/PricelistThcExport.php`, `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/PricelistThcController.php`

## 33. PricelistTkbm

- Model: `app/Models/PricelistTkbm.php`.
- Tabel menurut Eloquent: `pricelist_tkbms`.
- Field fillable: `nama_barang`, `tarif`, `status`.
- Referensi aplikasi: `app/Http/Controllers/BiayaKapalController.php`, `app/Http/Controllers/PricelistTkbmController.php`, `app/Models/BiayaKapalTkbm.php`

## 34. PricelistUangJalanBatam

- Model: `app/Models/PricelistUangJalanBatam.php`.
- Tabel menurut Eloquent: `pricelist_uang_jalan_batam`.
- Field fillable: `expedisi`, `ring`, `wilayah`, `tarif_20ft_full`, `tarif_20ft_full_base`, `tarif_20ft_empty`, `tarif_20ft_empty_base`, `tarif_40ft_full`, `tarif_40ft_full_base`, `tarif_40ft_empty`, `tarif_40ft_empty_base`, `tarif_antarlokasi_20ft`, `tarif_antarlokasi_20ft_base`, `tarif_antarlokasi_40ft`, `tarif_antarlokasi_40ft_base`, `status`, `kelola_bbm_id`.
- Referensi aplikasi: `app/Exports/PricelistUangJalanBatamExport.php`, `app/Http/Controllers/GajiSupirBatamController.php`, `app/Http/Controllers/KelolaBbmController.php`, `app/Http/Controllers/OrderBatamController.php`, `app/Http/Controllers/PricelistUangJalanBatamController.php`, `app/Http/Controllers/ReportKerjaSupirBatamController.php`, `app/Http/Controllers/SuratJalanBatamController.php`, `app/Http/Controllers/SuratJalanBongkaranBatamController.php`, `app/Http/Controllers/SuratJalanBongkaranController.php`, `app/Http/Controllers/SuratJalanTarikKosongBatamController.php`, `app/Imports/PricelistUangJalanBatamImport.php`, `app/Models/PricelistTarifHistory.php`

## 35. TarifBatam

- Model: `app/Models/TarifBatam.php`.
- Tabel menurut Eloquent: `tarif_batam`.
- Field fillable: `chasis_ayp`, `20ft_full`, `20ft_empty`, `antar_lokasi`, `40ft_full`, `40ft_empty`, `40ft_antar_lokasi`, `masa_berlaku`, `keterangan`, `status`.
- Referensi aplikasi: `app/Http/Controllers/TarifBatamController.php`

## Migrasi yang menyebut identifier pricelist/tarif

Daftar ini mencakup nama tabel, kolom, indeks, create/alter/drop/rollback dan riwayat lama; bukan daftar tabel yang pasti aktif. Telusuri urutan `up()` dan `down()` secara terpisah.

- `database/migrations/2025_08_19_021600_create_pricelist_sewa_kontainers_table.php`: `pricelist_sewa_kontainers`
- `database/migrations/2025_08_20_000000_create_master_pricelist_sewa_kontainers_table.php`: `master_pricelist_sewa_kontainers`
- `database/migrations/2025_08_20_200000_remove_nomor_tagihan_from_master_pricelist_sewa_kontainers_table.php`: `master_pricelist_sewa_kontainers`
- `database/migrations/2025_08_28_200000_add_tarif_nominal_to_daftar_tagihan_kontainer_sewa.php`: `master_pricelist_sewa_kontainers`
- `database/migrations/2025_09_22_144346_create_pricelist_cat_table.php`: `pricelist_cat`
- `database/migrations/2025_09_22_145718_remove_unused_columns_from_pricelist_cat_table.php`: `pricelist_cat`, `pricelist_cat_tanggal_harga_awal_index`, `pricelist_cat_tanggal_harga_akhir_index`
- `database/migrations/2025_09_22_145922_rename_tarif_per_meter_to_harga_satuan_in_pricelist_cat_table.php`: `pricelist_cat`
- `database/migrations/2025_09_22_150300_rename_harga_satuan_to_tarif_in_pricelist_cat_table.php`: `pricelist_cat`
- `database/migrations/2025_10_09_000001_create_master_pricelist_uang_jalan_table.php`: `master_pricelist_uang_jalan`
- `database/migrations/2025_10_17_151146_create_pricelist_gate_ins_table.php`: `pricelist_gate_ins`, `nama_pricelist`
- `database/migrations/2025_10_17_152258_update_pricelist_gate_ins_table_structure.php`: `pricelist_gate_ins`
- `database/migrations/2025_10_17_220426_create_karyawan_audit_logs_table.php`: `pricelist`
- `database/migrations/2025_10_19_135048_update_pricelist_gate_ins_to_pelabuhan_sunda_kelapa_structure.php`: `pricelist_gate_ins`
- `database/migrations/2025_10_20_133000_cleanup_master_services_foreign_keys.php`: `pricelist_gate_ins`
- `database/migrations/2025_10_20_134713_drop_service_id_from_tables.php`: `pricelist_gate_ins`
- `database/migrations/2025_10_20_150000_fix_master_services_foreign_key_constraints.php`: `pricelist_gate_ins`
- `database/migrations/2025_11_04_130411_create_tarif_batam_table.php`: `tarif_batam`
- `database/migrations/2025_11_04_150401_drop_tarif_batam_table.php`: `tarif_batam`
- `database/migrations/2025_11_10_141442_create_master_pricelist_ob_table.php`: `master_pricelist_ob`
- `database/migrations/2025_11_12_111634_create_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2025_11_12_130414_add_tanggal_berlaku_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2025_11_12_131144_drop_tanggal_berlaku_from_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2025_11_12_154804_create_pricelist_tarif_history_table.php`: `pricelist_tarif_history`, `pricelist_uang_jalan_batam_id`, `pricelist_uang_jalan_batam`
- `database/migrations/2025_11_12_155530_add_tarif_base_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2025_12_22_092543_create_pricelist_rit_table.php`: `pricelist_rit`
- `database/migrations/2025_12_22_093833_add_status_to_pricelist_rit_table.php`: `pricelist_rit`
- `database/migrations/2026_01_02_152709_add_rute_column_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_01_03_120000_add_rute_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_01_05_095510_create_pricelist_buruh_table.php`: `pricelist_buruh`
- `database/migrations/2026_01_05_101418_add_full_empty_to_pricelist_buruh_table.php`: `pricelist_buruh`
- `database/migrations/2026_01_05_102849_drop_full_empty_from_pricelist_buruh_table.php`: `pricelist_buruh`
- `database/migrations/2026_01_05_103347_add_tipe_to_pricelist_buruh_table.php`: `pricelist_buruh`
- `database/migrations/2026_01_08_create_pricelist_biaya_dokumen_table.php`: `pricelist_biaya_dokumen`
- `database/migrations/2026_01_09_105118_create_biaya_kapal_barang_table.php`: `pricelist_buruh_id`, `pricelist_buruh`
- `database/migrations/2026_01_09_152456_add_kapal_voyage_to_biaya_kapal_barang_table.php`: `pricelist_buruh_id`
- `database/migrations/2026_01_13_005653_add_vendor_id_to_biaya_kapals_table.php`: `pricelist_biaya_dokumen`
- `database/migrations/2026_01_17_000001_create_master_pricelist_air_tawar_table.php`: `master_pricelist_air_tawar`
- `database/migrations/2026_01_19_000001_add_lokasi_to_master_pricelist_air_tawar_table.php`: `master_pricelist_air_tawar`
- `database/migrations/2026_01_21_093406_create_pricelist_tkbms_table.php`: `pricelist_tkbms`
- `database/migrations/2026_01_21_095709_modify_pricelist_tkbms_table.php`: `pricelist_tkbms`
- `database/migrations/2026_01_21_105002_create_biaya_kapal_tkbm_table.php`: `pricelist_tkbm_id`, `pricelist_tkbms`
- `database/migrations/2026_02_02_094203_create_master_pricelist_kanisir_bans_table.php`: `master_pricelist_kanisir_bans`
- `database/migrations/2026_02_02_141203_make_pricelist_buruh_id_nullable_in_biaya_kapal_barang_table.php`: `pricelist_buruh_id`, `pricelist_buruh`
- `database/migrations/2026_02_10_create_master_pricelist_biaya_trucking_table.php`: `master_pricelist_biaya_trucking`
- `database/migrations/2026_02_12_142807_create_master_pricelist_lemburs_table.php`: `master_pricelist_lemburs`
- `database/migrations/2026_02_12_144014_add_harga_900_benang_to_master_pricelist_kanisir_bans_table.php`: `master_pricelist_kanisir_bans`
- `database/migrations/2026_02_23_152228_create_master_pricelist_vendor_supirs_table.php`: `master_pricelist_vendor_supirs`
- `database/migrations/2026_02_23_153943_add_columns_to_master_pricelist_vendor_supirs_table.php`: `master_pricelist_vendor_supirs`
- `database/migrations/2026_02_24_091122_remove_tujuan_id_from_master_pricelist_vendor_supirs_table.php`: `master_pricelist_vendor_supirs`
- `database/migrations/2026_02_24_115906_add_vendor_id_to_master_pricelist_vendor_supirs.php`: `master_pricelist_vendor_supirs`
- `database/migrations/2026_02_25_085511_create_master_pricelist_labuh_tambat_table.php`: `master_pricelist_labuh_tambat`
- `database/migrations/2026_02_25_091004_rename_columns_in_master_pricelist_labuh_tambat_table.php`: `master_pricelist_labuh_tambat`
- `database/migrations/2026_02_25_092031_add_nama_kapal_to_master_pricelist_labuh_tambat_table.php`: `master_pricelist_labuh_tambat`
- `database/migrations/2026_02_27_151415_create_pricelist_opp_opts_table.php`: `pricelist_opp_opts`
- `database/migrations/2026_02_28_102504_create_master_pricelist_freights_table.php`: `master_pricelist_freights`
- `database/migrations/2026_03_02_091012_add_vendor_and_lokasi_to_pricelist_opp_opts_table.php`: `pricelist_opp_opts`
- `database/migrations/2026_03_02_092252_remove_vendor_from_pricelist_opp_opts_table.php`: `pricelist_opp_opts`
- `database/migrations/2026_03_02_100457_create_biaya_kapal_opp_opts_table.php`: `pricelist_opp_opt_id`, `pricelist_opp_opts`
- `database/migrations/2026_03_02_103103_create_pricelist_thcs_table.php`: `pricelist_thcs`
- `database/migrations/2026_03_02_105303_add_vendor_to_pricelist_thcs_table.php`: `pricelist_thcs`
- `database/migrations/2026_03_02_154737_create_master_pricelist_lolos_table.php`: `master_pricelist_lolos`
- `database/migrations/2026_03_03_093749_add_vendor_and_location_to_master_pricelist_lolos_table.php`: `master_pricelist_lolos`
- `database/migrations/2026_03_03_094903_remove_unused_columns_from_master_pricelist_lolos_table.php`: `master_pricelist_lolos`
- `database/migrations/2026_03_03_095016_remove_terminal_and_tipe_aktivitas_from_master_pricelist_lolos_table.php`: `master_pricelist_lolos`
- `database/migrations/2026_03_03_095706_remove_kategori_from_master_pricelist_lolos_table.php`: `master_pricelist_lolos`
- `database/migrations/2026_03_04_120000_create_master_pricelist_biaya_storages_table.php`: `master_pricelist_biaya_storages`
- `database/migrations/2026_03_04_120027_remove_free_time_from_master_pricelist_biaya_storages.php`: `master_pricelist_biaya_storages`
- `database/migrations/2026_03_12_103525_add_status_to_master_pricelist_freights_table.php`: `master_pricelist_freights`
- `database/migrations/2026_03_12_103528_modify_master_pricelist_freights_table.php`: `master_pricelist_freights`
- `database/migrations/2026_03_13_145621_create_pricelist_perijinans_table.php`: `pricelist_perijinans`
- `database/migrations/2026_03_14_000001_add_nama_biaya_to_master_pricelist_lolos_table.php`: `master_pricelist_lolos`
- `database/migrations/2026_03_14_083548_create_biaya_kapal_perijinan_details_table.php`: `pricelist_perijinan_id`, `pricelist_perijinans`
- `database/migrations/2026_04_07_153845_create_pricelist_meratus_table.php`: `pricelist_meratus`
- `database/migrations/2026_04_07_160413_add_lokasi_to_pricelist_meratus_table.php`: `pricelist_meratus`
- `database/migrations/2026_04_07_162341_create_biaya_kapal_meratus_table.php`: `pricelist_meratus_id`
- `database/migrations/2026_04_15_162502_create_master_pricelist_demurrages_table.php`: `master_pricelist_demurrages`
- `database/migrations/2026_04_23_164317_drop_rute_from_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_04_24_091252_add_tarif_antar_lokasi_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_04_24_092030_drop_f_e_from_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`, `pricelist_uang_jalan_batam_expedisi_ring_size_f_e_index`
- `database/migrations/2026_04_24_092620_drop_size_from_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_04_24_093331_change_tarif_structure_in_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_04_24_093738_add_base_tarif_columns_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_04_24_094306_drop_tarif_antar_lokasi_from_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_04_24_100832_add_antarlokasi_tarif_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_04_27_140829_create_pricelist_temas_table.php`: `pricelist_temas`
- `database/migrations/2026_04_27_142615_create_biaya_kapal_temas_table.php`: `pricelist_temas_id`
- `database/migrations/2026_05_11_161157_rename_biaya_per_hari_to_tarif_massa_1_in_master_pricelist_biaya_storages_table.php`: `master_pricelist_biaya_storages`
- `database/migrations/2026_05_15_141643_create_master_pricelist_tujuan_kontainer_sewas_table.php`: `master_pricelist_tujuan_kontainer_sewas`
- `database/migrations/2026_05_16_095327_add_base_antarlokasi_tarif_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_05_19_104553_create_pricelist_tanto_table.php`: `pricelist_tanto`
- `database/migrations/2026_05_19_110833_create_biaya_kapal_tanto_table.php`: `pricelist_tanto_id`
- `database/migrations/2026_05_22_142042_create_pricelist_pelindos_table.php`: `pricelist_pelindos`
- `database/migrations/2026_05_23_085200_add_status_kontainer_to_pricelist_pelindos_table.php`: `pricelist_pelindos`
- `database/migrations/2026_06_03_144219_create_tagihan_pelindos_table.php`: `pricelist_pelindo_id`, `pricelist_pelindos`
- `database/migrations/2026_06_25_093502_add_kelola_bbm_id_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_06_29_094000_create_sk_sewa_kontainer_tables.php`: `sk_tarif_sewas`
- `database/migrations/2026_06_29_111835_drop_sk_sewa_kontainer_tables.php`: `sk_tarif_sewas`
- `database/migrations/2026_06_29_112846_create_sk_react_tables.php`: `sk_tarifs`
- `database/migrations/2026_06_29_144801_add_wilayah_to_pricelist_uang_jalan_batam_table.php`: `pricelist_uang_jalan_batam`
- `database/migrations/2026_07_27_101049_add_nomor_bl_and_vendor_to_biaya_kapal_dokumens_table.php`: `pricelist_biaya_dokumen`
- `database/migrations/2026_08_10_143332_add_manifest_id_to_biaya_kapal_opp_opts_table.php`: `pricelist_opp_opt_id`
- `database/migrations/2026_08_20_102049_create_biaya_kapal_opp_opt_manifest_table.php`: `pricelist_opp_opt_id`
- `database/migrations/2026_08_27_104058_create_pricelist_buruh_bongkars_table.php`: `pricelist_buruh_bongkars`
- `database/migrations/2026_08_27_110928_add_lokasi_to_pricelist_buruh_bongkars_table.php`: `pricelist_buruh_bongkars`
- `database/migrations/2026_09_01_092919_add_status_bongkar_muat_to_pricelist_opp_opts_table.php`: `pricelist_opp_opts`
- `database/migrations/2026_09_12_100000_add_status_service_to_master_pricelist_ob_table.php`: `master_pricelist_ob`
- `database/migrations/2026_09_14_120000_create_master_pricelist_ob_antar_gudang_table.php`: `master_pricelist_ob_antar_gudang`, `ob_antar_gudang_pricelist_unique`
- `database/migrations/2026_09_14_120002_make_service_pricelist_container_status_optional.php`: `master_pricelist_ob_antar_gudang`
- `database/migrations/2026_09_15_120000_add_destination_to_ob_antar_gudang_pricelists.php`: `master_pricelist_ob_antar_gudang`, `ob_antar_gudang_pricelist_unique`, `ob_antar_gudang_pricelist_destination_unique`
- `database/migrations/2026_10_01_000004_create_tagihan_lolo_batams_table.php`: `master_pricelist_lolo_batam_id`
- `database/migrations/2026_10_01_110000_create_master_pricelist_lolo_batams_table.php`: `master_pricelist_lolo_batams`
- `database/migrations/2026_10_01_120000_drop_vendor_and_nama_biaya_from_master_pricelist_lolo_batams_table.php`: `master_pricelist_lolo_batams`
- `database/migrations/2026_10_13_010000_remove_penyewaan_kontainer_app.php`: `sk_tarif_sewas`, `sk_tarifs`
- `database/migrations/2026_10_16_000007_remove_master_pricelist_tujuan_kontainer_sewa.php`: `master_pricelist_tujuan_kontainer_sewas`
- `database/migrations/2026_10_19_000003_create_master_pricelist_biaya_storage_pelindos_table.php`: `master_pricelist_biaya_storage_pelindos`
- `database/migrations/2026_10_19_000004_make_pelindo_storage_mass_tariffs_flexible.php`: `master_pricelist_biaya_storage_pelindos`, `master_pricelist_biaya_storage_pelindo_tarif_massas`, `pricelist_id`, `pelindo_mass_pricelist_fk`, `pelindo_mass_pricelist_unique`
- `database/migrations/2026_10_19_000005_remove_vendor_and_location_from_pelindo_storage_pricelists.php`: `master_pricelist_biaya_storage_pelindos`
- `database/migrations/2026_11_07_120000_add_lokasi_to_pricelist_pelindos_table.php`: `pricelist_pelindos`
- `database/migrations/2026_11_07_130000_add_cargo_to_pricelist_pelindo_status_kontainer.php`: `pricelist_pelindos`
- `database/migrations/2026_11_07_140000_add_b3_lpg_besi_beton_to_pricelist_pelindo_status.php`: `pricelist_pelindos`
- `database/migrations/2026_11_07_150000_rename_b3_to_barang_berbahaya_in_pelindo_status.php`: `pricelist_pelindos`
- `database/migrations/2026_11_07_160000_add_mobil_alat_berat_to_pricelist_pelindo_status.php`: `pricelist_pelindos`
- `database/migrations/2026_11_10_160000_add_palka_to_pricelist_pelindo_status.php`: `pricelist_pelindos`
- `database/migrations/2026_11_16_120000_add_chasis_and_headtruck_to_pricelist_pelindo_status.php`: `pricelist_pelindos`
- `database/migrations/2026_11_25_130000_add_temas_meratus_ob_pricelists.php`: `master_pricelist_ob_antar_gudang`


## Pemeriksaan silang seluruh menu Master Tarif

Sumber: `config/master_tarif.php` (33 entri). Ini bukti konfigurasi menu, bukan jaminan endpoint aktif. Cocokkan route, permission, controller, view, dan tabel saat audit rebuild.

| Menu | Route | Permission |
|---|---|---|
| Pricelist Kontainer Sewa | `master.master.pricelist-sewa-kontainer.index` | `master-pricelist-sewa-kontainer-view` |
| Pricelist CAT Kontainer | `master.pricelist-cat.index` | `master-pricelist-cat-view` |
| Pricelist Gate In | `master.pricelist-gate-in.index` | `master-pricelist-gate-in-view` |
| Pricelist OB | `master.pricelist-ob.index` | `master-pricelist-ob-view` |
| Pricelist OB Antar Gudang | `master.pricelist-ob-antar-gudang.index` | `master-pricelist-ob-antar-gudang-view` |
| Master Pelayanan Pelabuhan | `master.master-pelayanan-pelabuhan.index` | `master-pelayanan-pelabuhan-view` |
| Pricelist Uang Jalan | `master-pricelist-uang-jalan.index` | `master-pricelist-uang-jalan-view` |
| Pricelist Uang Jalan Batam | `pricelist-uang-jalan-batam.index` | `master-pricelist-uang-jalan-batam-view` |
| Pricelist Rit | `master.pricelist-rit.index` | `master-pricelist-rit-view` |
| Pricelist Air Tawar | `master.pricelist-air-tawar.index` | `master-pricelist-air-tawar-view` |
| Pricelist Buruh | `master.pricelist-buruh.index` | `master-pricelist-buruh-view` |
| Pricelist Buruh Bongkar | `master.pricelist-buruh-bongkar.index` | `master-pricelist-buruh-bongkar-view` |
| Pricelist TKBM | `master.pricelist-tkbm.index` | Tidak ditentukan di konfigurasi |
| Pricelist OPP/OPT | `master.pricelist-opp-opt.index` | `master-pricelist-opp-opt-view` |
| Pricelist Biaya THC | `master.pricelist-thc.index` | `master-pricelist-thc-view` |
| Pricelist Meratus | `master.pricelist-meratus.index` | `master-pricelist-meratus-view` |
| Pricelist Temas | `master.pricelist-temas.index` | `master-pricelist-temas-view` |
| Pricelist Tanto | `master.pricelist-tanto.index` | `master-pricelist-tanto-view` |
| Pricelist Biaya LOLO | `master.pricelist-lolo.index` | `master-pricelist-lolo-view` |
| Pricelist LOLO Batam | `master.pricelist-lolo-batam.index` | `master-pricelist-lolo-batam-view` |
| Pricelist Biaya Storage | `master-pricelist-biaya-storage.index` | `master-pricelist-biaya-storage-view` |
| Pricelist Biaya Storage Pelindo | `master-pricelist-biaya-storage-pelindo.index` | `master-pricelist-biaya-storage-pelindo-view` |
| Pricelist Kanisir Ban | `master.pricelist-kanisir-ban.index` | `master-pricelist-kanisir-ban-view` |
| Pricelist Biaya Dokumen | `master.pricelist-biaya-dokumen.index` | `master-pricelist-biaya-dokumen-view` |
| Pricelist Biaya Trucking | `master.pricelist-biaya-trucking.index` | `master-pricelist-biaya-trucking-view` |
| Master Pricelist Lembur | `master.pricelist-lembur.index` | `master-pricelist-lembur-view` |
| Pricelist Vendor Supir | `master.pricelist-vendor-supir.index` | `master-pricelist-vendor-supir-view` |
| Pricelist Labuh Tambat | `master.master-pricelist-labuh-tambat.index` | `master-pricelist-labuh-tambat-view` |
| Pricelist Freight (Fright) | `master-pricelist-freight.index` | `master-pricelist-freight-view` |
| Kelola BBM | `kelola-bbm.index` | `master-kelola-bbm-view` |
| Ongkos Truck | `master.ongkos-truck.index` | `ongkos-truck-view` |
| Pricelist Perijinan | `master.pricelist-perijinan.index` | `master-pricelist-perijinan-view` |
| Pricelist Pelindo | `master.pricelist-pelindo.index` | `master-pricelist-pelindo-view` |

### Menu yang tidak setara dengan master pricelist

`Ongkos Truck` ada di menu Master Tarif, tetapi `app/Http/Controllers/OngkosTruckController.php` menampilkan data surat jalan berdasarkan mobil dan rentang tanggal. Metode create/store yang ditinjau kosong. Jangan menciptakan tabel pricelist Ongkos Truck hanya karena nama menu; telusuri sumber nilai dari surat jalan dan rute yang dipakai.

## Tarif tambahan di luar model bernama Pricelist/Tarif

### MasterPelayananPelabuhan

- Sumber: `app/Models/MasterPelayananPelabuhan.php`.
- Field fillable: `nama_pelayanan`, `deskripsi`, `biaya`, `satuan`, `is_active`.
- Catatan: Layanan dengan biaya dan satuan; terdaftar juga di menu Master Tarif.

### KelolaBbm

- Sumber: `app/Models/KelolaBbm.php`.
- Field fillable: `bulan`, `tahun`, `bbm_per_liter`, `persentase`, `keterangan`.
- Catatan: Periode bulan/tahun, harga per liter, persentase; digunakan sebagai konteks penyesuaian uang jalan Batam, bukan hanya rate sederhana.

### AlatBerat

- Sumber: `app/Models/AlatBerat.php`.
- Field fillable: `kode_alat`, `nama`, `nickname`, `warna`, `jenis`, `merk`, `tipe`, `kapasitas`, `nomor_seri`, `nomor_sertifikat_sia`, `tanggal_terbit_sertifikat_sia`, `tanggal_kadaluarsa_sertifikat_sia`, `biaya_sertifikat_sia`, `tahun_pembuatan`, `lokasi`, `tarif_harian`, `tarif_bulanan`, `keterangan`, `status`.
- Catatan: Tarif harian/bulanan menempel di master aset; jangan pindahkan identitas aset ke master pricelist.

### VendorAsuransi

- Sumber: `app/Models/VendorAsuransi.php`.
- Field fillable: `kode`, `nama_asuransi`, `alamat`, `telepon`, `email`, `keterangan`, `catatan`, `tarif`, `created_by`, `updated_by`.
- Catatan: Tarif menempel pada master vendor asuransi; verifikasi apakah persentase atau nominal melalui controller sebelum migrasi.

## Batas cakupan seluruh pricelist

Seluruh entri model Pricelist/Tarif yang ditemukan di `app/Models` utama dan seluruh menu `config/master_tarif.php` telah dimasukkan sebagai daftar audit. Tarif tambahan dan jejak migrasi juga dicatat. Ini bukan klaim semua rumus atau pemakaian production sudah diverifikasi.

AI di AYPSIS-2 harus melanjutkan pencarian query langsung, lookup tarif di JavaScript/API, nilai hardcode, harga di master lain, import/export, laporan, job, dan command yang memengaruhi tarif. Harga transaksi atau harga beli stok tidak otomatis merupakan pricelist. Subproject terpisah seperti `Penyewaan-Kontainer-main` dan `stowage-app` belum diaudit; masukkan bila menjadi sumber rebuild dan baca instruksi repository masing-masing.

Hasil akhir audit harus memiliki checklist setiap entri di dokumen ini, status pemakaian, mapping target, bukti file, dan alasan jika dipertahankan khusus atau ditandai legacy. Tidak boleh mengklaim selesai hanya karena tabel master/detail bersama berhasil dibuat.
