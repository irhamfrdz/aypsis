<?php

use App\Models\Bl;

echo "Mencari data BL dengan no_voyage AP03BJ26...\n";
$count = Bl::withTrashed()->where('no_voyage', 'AP03BJ26')->count();
echo "Ditemukan $count record.\n";

if ($count > 0) {
    $deleted = Bl::withTrashed()->where('no_voyage', 'AP03BJ26')->forceDelete();
    echo "$deleted record berhasil dihapus secara permanen dari table bls.\n";
} else {
    echo "Data tidak ditemukan.\n";
}

// Keluar dari tinker
exit;
