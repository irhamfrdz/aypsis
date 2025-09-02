<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PranotaSupir;
use App\Models\PembayaranPranotaSupir;
use App\Models\User;

class PembayaranPranotaSupirTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembayaran_pranota_supir_dapat_disimpan()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pranota = PranotaSupir::factory()->create();

        $data = [
            'pranota_ids' => [$pranota->id],
            'nomor_pembayaran' => 'BMS-1-25-08-000001',
            'nomor_cetakan' => 1,
            'tanggal_pembayaran' => now()->toDateString(),
            'tanggal_kas' => now()->toDateString(),
            'total_pembayaran' => 100000,
            'bank' => 'BCA',
            'jenis_transaksi' => 'Debit',
            'total_tagihan_penyesuaian' => 0,
            'total_tagihan_setelah_penyesuaian' => 100000,
            'alasan_penyesuaian' => 'Test',
            'keterangan' => 'Test pembayaran',
        ];

        $response = $this->post(route('pembayaran-pranota-supir.store'), $data);
        $response->assertRedirect(route('pembayaran-pranota-supir.index'));
        $this->assertDatabaseHas('pembayaran_pranota_supir', [
            'nomor_pembayaran' => 'BMS-1-25-08-000001',
            'bank' => 'BCA',
            'jenis_transaksi' => 'Debit',
            'total_pembayaran' => 100000,
        ]);
    }
}
