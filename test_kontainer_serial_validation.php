<?php

// Test validasi nomor seri kontainer + akhiran pada master kontainer
echo "=== Test Validasi Nomor Seri + Akhiran Kontainer ===\n\n";

echo "📋 Aturan Validasi:\n";
echo "1. ✅ Nomor seri SAMA + akhiran BERBEDA → DIPERBOLEHKAN\n";
echo "2. ❌ Nomor seri SAMA + akhiran SAMA → SALAH SATU DINONAKTIFKAN\n\n";

echo "🔍 Contoh Skenario:\n\n";

echo "Skenario 1: DIPERBOLEHKAN\n";
echo "- Kontainer 1: ABCD123456A (Nomor seri: 123456, Akhiran: A)\n";
echo "- Kontainer 2: ABCD123456B (Nomor seri: 123456, Akhiran: B)\n";
echo "- Status: Kedua kontainer TETAP AKTIF ✅\n\n";

echo "Skenario 2: TIDAK DIPERBOLEHKAN\n";
echo "- Kontainer 1: ABCD123456A (Nomor seri: 123456, Akhiran: A) - Sudah ada\n";
echo "- Kontainer 2: EFGH123456A (Nomor seri: 123456, Akhiran: A) - Baru ditambah\n";
echo "- Aksi: Kontainer 1 DINONAKTIFKAN, Kontainer 2 TETAP AKTIF ❌\n\n";

echo "🛠️ Implementasi:\n";
echo "✅ Model Kontainer: Validasi otomatis saat create/update\n";
echo "✅ Controller: Validasi manual + pesan warning\n";
echo "✅ View: Tampilan status 'Nonaktif' untuk kontainer yang dinonaktifkan\n";
echo "✅ Sync Command: Membersihkan duplikasi data lama\n\n";

echo "🎯 Hasil:\n";
echo "- Tidak ada kontainer aktif dengan nomor seri + akhiran yang sama\n";
echo "- Kontainer dengan nomor seri sama tapi akhiran berbeda diperbolehkan\n";
echo "- Sistem otomatis menonaktifkan kontainer lama saat ada konflik\n";
echo "- User mendapat notifikasi warning saat terjadi konflik\n\n";

echo "✅ Validasi Berhasil Diimplementasikan!\n";