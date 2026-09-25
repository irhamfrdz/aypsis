BACA README_V5.txt UNTUK VERSI TERBARU.

CONTAINER BILLING CONTROL - FINAL SIAP PAKAI

CARA MULAI
1. Extract ZIP ke folder tetap.
2. Double-click MULAI_APP.bat.
3. Browser membuka http://127.0.0.1:8765
4. Data awal otomatis dimuat bila database kosong.
5. Buka Backup / Restore lalu Download Backup JSON.

DATA AWAL
- 3,449 detail tagihan dari data Excel awal.
- Backup baseline: backup_data_awal.json.
- File sumber dan hasil analisa disertakan sebagai bukti audit.

PENGAMBILAN
KONTAINER [TAB] TGL AMBIL [TAB] TARIF BULANAN [TAB] TARIF HARIAN
Valid masuk dan hilang dari textarea. Reject tetap di textarea + alasan.

PENGEMBALIAN
KONTAINER [TAB] TGL KEMBALI
Reject tetap di textarea + alasan.

TAGIHAN
VENDOR [TAB] NO TAGIHAN [TAB] TGL TAGIHAN [TAB] TAGIHAN [TAB] KONTAINER [TAB] PERIODE
Semua baris diterima. Masalah diberi PENDING + alasan.

BACKUP / RESTORE
- Download backup JSON kapan saja dari menu aplikasi.
- Restore JSON mengganti database aktif setelah konfirmasi.
- Kembalikan Data Awal memakai baseline tertanam.
- backup_data_awal.json dapat dipakai restore manual.
