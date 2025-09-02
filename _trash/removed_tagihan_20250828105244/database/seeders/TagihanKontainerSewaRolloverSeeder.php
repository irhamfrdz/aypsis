<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kontainer;
use App\Models\TagihanKontainerSewa;

class TagihanKontainerSewaRolloverSeeder extends Seeder
{
    public function run()
    {
        // Create or find two kontainers (idempotent)
        $k1 = Kontainer::updateOrCreate([
            'nomor_seri_gabungan' => 'CCLU123456'
        ], [
            'awalan_kontainer' => 'CCLU',
            'nomor_seri_kontainer' => '123456',
            'akhiran_kontainer' => '',
            'ukuran' => '20 ft',
            'tipe_kontainer' => 'Dry',
            'status' => 'available'
        ]);

        $k2 = Kontainer::updateOrCreate([
            'nomor_seri_gabungan' => 'CBHU654321'
        ], [
            'awalan_kontainer' => 'CBHU',
            'nomor_seri_kontainer' => '654321',
            'akhiran_kontainer' => '',
            'ukuran' => '20 ft',
            'tipe_kontainer' => 'Dry',
            'status' => 'available'
        ]);

        // Create a tagihan with periode expressed as integer months (migrations convert periode -> integer)
        $tagihan = TagihanKontainerSewa::create([
            'vendor' => 'PT Contoh Vendor',
            'tarif' => 'Bulanan',
            'ukuran_kontainer' => '20 ft',
            'harga' => 775.00,
            'tanggal_harga_awal' => '2025-07-01',
            'tanggal_harga_akhir' => null,
            'keterangan' => 'Tagihan percobaan periode Juli',
            // one-month period
            'periode' => 1
        ]);

        // Attach pivots (avoid duplicates)
        DB::table('tagihan_kontainer_sewa_kontainers')->updateOrInsert([
            'tagihan_id' => $tagihan->id, 'kontainer_id' => $k1->id
        ], ['created_at' => now(), 'updated_at' => now()]);
        DB::table('tagihan_kontainer_sewa_kontainers')->updateOrInsert([
            'tagihan_id' => $tagihan->id, 'kontainer_id' => $k2->id
        ], ['created_at' => now(), 'updated_at' => now()]);

        $this->command->info('Seeded tagihan id=' . $tagihan->id . ' with 2 kontainers. Periode=' . $tagihan->periode);

    // Run rollover logic by manually invoking controller clone logic here for convenience
    $orig = $tagihan;
    // next periode remains an integer duration (1 month)
    $nextPeriode = 1;
    $clone = $orig->replicate()->toArray();
    unset($clone['id']);
    $clone['periode'] = $nextPeriode;
        $clone['created_at'] = now();
        $clone['updated_at'] = now();
        $new = TagihanKontainerSewa::create($clone);

        // copy pivots (avoid duplicates)
        DB::table('tagihan_kontainer_sewa_kontainers')->updateOrInsert([
            'tagihan_id' => $new->id, 'kontainer_id' => $k1->id
        ], ['created_at' => now(), 'updated_at' => now()]);
        DB::table('tagihan_kontainer_sewa_kontainers')->updateOrInsert([
            'tagihan_id' => $new->id, 'kontainer_id' => $k2->id
        ], ['created_at' => now(), 'updated_at' => now()]);

        $this->command->info('Created rollover tagihan id=' . $new->id . ' periode=' . $new->periode);
    }
}
