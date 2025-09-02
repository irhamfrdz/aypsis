<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class PranotaStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_pranota_marks_source_tagihan_as_sudah_masuk_pranota()
    {
    // avoid gate middleware in test environment
    $this->withoutMiddleware();
        // Create a sample tagihan and kontainer then link via pivot
        $kontainerId = DB::table('kontainers')->insertGetId([
            'awalan_kontainer' => 'K',
            'nomor_seri_kontainer' => 'TEST-1',
            'tipe_kontainer' => 'DRY',
            'akhiran_kontainer' => 'A',
            'nomor_seri_gabungan' => 'K-TEST-1-A',
            'ukuran' => '20',
            'pemilik_kontainer' => 'TESTVENDOR',
            'harga_satuan' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tagihanId = DB::table('tagihan_kontainer_sewa')->insertGetId([
            'vendor' => 'TESTVENDOR',
            'tarif' => 'Sewa',
            'ukuran_kontainer' => '20',
            'harga' => 1000,
            'tanggal_harga_awal' => now()->toDateString(),
            'keterangan' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tagihan_kontainer_sewa_kontainers')->insert([
            'tagihan_id' => $tagihanId,
            'kontainer_id' => $kontainerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // POST to the pranota creation route with kontainer_ids
        $response = $this->post(route('pranota-tagihan-kontainer.store'), [
            'kontainer_ids' => (string)$kontainerId,
            'vendor' => 'TESTVENDOR',
        ]);

        $response->assertRedirect();

        // Assert the original tagihan was updated
        $this->assertDatabaseHas('tagihan_kontainer_sewa', [
            'id' => $tagihanId,
            'status_pembayaran' => 'Sudah Masuk Pranota',
        ]);
    }
}
