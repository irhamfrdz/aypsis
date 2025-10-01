<?php

// Script demonstrasi detail permission untuk setiap aksi

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DETAIL PERMISSION REQUIREMENT UNTUK SETIAP MENU ===\n\n";

$menus = [
    'Master Cabang' => [
        'Melihat menu di sidebar' => 'master-cabang-view',
        'Mengakses halaman daftar cabang' => 'master-cabang-view',
        'Melihat detail cabang' => 'master-cabang-view',
        'Membuat cabang baru' => 'master-cabang-create',
        'Mengedit cabang' => 'master-cabang-update',
        'Menghapus cabang' => 'master-cabang-delete'
    ],
    'Master COA' => [
        'Melihat menu di sidebar' => 'master-coa-view',
        'Mengakses halaman daftar COA' => 'master-coa-view',
        'Melihat detail COA' => 'master-coa-view',
        'Download template import' => 'master-coa-view',
        'Membuat COA baru' => 'master-coa-create',
        'Import COA' => 'master-coa-create',
        'Mengedit COA' => 'master-coa-update',
        'Menghapus COA' => 'master-coa-delete'
    ],
    'Master Kode Nomor' => [
        'Melihat menu di sidebar' => 'master-kode-nomor-view',
        'Mengakses halaman daftar kode nomor' => 'master-kode-nomor-view',
        'Melihat detail kode nomor' => 'master-kode-nomor-view',
        'Membuat kode nomor baru' => 'master-kode-nomor-create',
        'Mengedit kode nomor' => 'master-kode-nomor-update',
        'Menghapus kode nomor' => 'master-kode-nomor-delete'
    ],
    'Master Nomor Terakhir' => [
        'Melihat menu di sidebar' => 'master-nomor-terakhir-view',
        'Mengakses halaman daftar nomor terakhir' => 'master-nomor-terakhir-view',
        'Melihat detail nomor terakhir' => 'master-nomor-terakhir-view',
        'Membuat nomor terakhir baru' => 'master-nomor-terakhir-create',
        'Mengedit nomor terakhir' => 'master-nomor-terakhir-update',
        'Menghapus nomor terakhir' => 'master-nomor-terakhir-delete'
    ],
    'Master Tipe Akun' => [
        'Melihat menu di sidebar' => 'master-tipe-akun-view',
        'Mengakses halaman daftar tipe akun' => 'master-tipe-akun-view',
        'Melihat detail tipe akun' => 'master-tipe-akun-view',
        'Membuat tipe akun baru' => 'master-tipe-akun-create',
        'Mengedit tipe akun' => 'master-tipe-akun-update',
        'Menghapus tipe akun' => 'master-tipe-akun-delete'
    ]
];

foreach ($menus as $menuName => $actions) {
    echo "📋 $menuName\n";
    echo str_repeat("─", strlen($menuName) + 3) . "\n";

    foreach ($actions as $actionName => $permission) {
        if (str_contains($permission, '-view')) {
            $icon = '👁️';
        } elseif (str_contains($permission, '-create')) {
            $icon = '➕';
        } elseif (str_contains($permission, '-update')) {
            $icon = '✏️';
        } elseif (str_contains($permission, '-delete')) {
            $icon = '🗑️';
        } else {
            $icon = '📄';
        }

        echo sprintf("  %s %-35s : %s\n", $icon, $actionName, $permission);
    }
    echo "\n";
}

echo "🎯 RINGKASAN JAWABAN PERTANYAAN:\n";
echo str_repeat("═", 50) . "\n";
echo "❓ Apakah untuk mengakses menu cabang, coa, kode nomor, nomor terakhir, tipe akun harus centang permission create terlebih dahulu?\n\n";
echo "✅ JAWABAN: TIDAK PERLU!\n\n";
echo "📝 PENJELASAN:\n";
echo "   • Untuk MELIHAT MENU di sidebar → Cukup permission VIEW\n";
echo "   • Untuk MENGAKSES halaman daftar → Cukup permission VIEW\n";
echo "   • Untuk MELIHAT detail data → Cukup permission VIEW\n";
echo "   • Untuk MEMBUAT data baru → Butuh permission CREATE\n";
echo "   • Untuk MENGEDIT data → Butuh permission UPDATE\n";
echo "   • Untuk MENGHAPUS data → Butuh permission DELETE\n\n";

echo "🏆 BEST PRACTICE:\n";
echo "   1. Berikan VIEW untuk user yang hanya perlu melihat data\n";
echo "   2. Tambahkan CREATE untuk user yang boleh menambah data\n";
echo "   3. Tambahkan UPDATE untuk user yang boleh mengedit data\n";
echo "   4. Tambahkan DELETE untuk user yang boleh menghapus data\n\n";

echo "💡 CATATAN PENTING:\n";
echo "   Permission CREATE tidak diperlukan untuk mengakses menu, \n";
echo "   tetapi diperlukan untuk menggunakan fitur tambah data di dalam menu tersebut.\n";
