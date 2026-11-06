<?php

use App\Services\LegacyFuelCardHistoryRepair;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hanya transaksi yang telah dicocokkan dengan audit dan riwayat kartu.
        $repairs = [
            [14, '20000063108', 2000000, 200000, 'amount', 'Pengisian bensin Kendaraan BP9250ZN'],
            [109, '20000063108', 2000000, null, 'removed', 'Pengisian bensin Kendaraan BP9359DU'],
            [110, '20000063108', 2000000, null, 'removed', 'Pengisian bensin Kendaraan BP9302DU'],
            [111, '20000063108', 3000000, null, 'removed', 'Pengisian bensin Kendaraan BP 8139 YU'],
            [112, '20000063108', 2500000, null, 'removed', 'Pengisian bensin Kendaraan BP9749YU'],
            [113, '20000063108', 2000000, null, 'removed', 'Pengisian bensin Kendaraan BP9746YU'],
            [114, '20000063108', 2000000, null, 'removed', 'Pengisian bensin Kendaraan BP8059FU'],
            [117, '20000063108', 555000, null, 'removed', 'Pengisian bensin Kendaraan BP8923ZH'],
            [150, '20000063108', 500000, null, 'deleted', 'Pengisian bensin Kendaraan BP8923ZH'],
            [156, '20000063108', 3000000, null, 'removed', 'Pengisian bensin Kendaraan BP9749YU'],
            [168, '20000063107', 3000000, null, 'removed', 'Pengisian bensin Kendaraan BP9749YU'],
            [219, '20000063107', 400000, 402500, 'amount', 'Pengisian bensin Kendaraan BP8246VE'],
            [256, '20000063107', 500000, 502500, 'amount', 'Pengisian bensin Kendaraan BP8246VE'],
            [270, '20000063107', 4750000, null, 'removed', 'Pengisian bensin Kendaraan -'],
        ];

        DB::transaction(function () use ($repairs): void {
            $repair = app(LegacyFuelCardHistoryRepair::class);
            foreach ($repairs as [$fuelId, $cardNumber, $chargedAmount, $correctAmount, $reason, $description]) {
                $repair->repair($fuelId, $cardNumber, $chargedAmount, $correctAmount, $reason, $description);
            }
        });
    }

    public function down(): void
    {
        // Riwayat dan saldo sebelum koreksi tidak dipulihkan otomatis.
    }
};
