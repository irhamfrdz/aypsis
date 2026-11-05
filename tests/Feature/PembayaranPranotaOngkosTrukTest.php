<?php

namespace Tests\Feature;

use App\Models\PranotaOngkosTruk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembayaranPranotaOngkosTrukTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembayaran_pranota_ongkos_truk_dapat_disimpan()
    {
        $user = User::factory()->create();
        // Setup permissions so user is authorized
        $permission = \App\Models\Permission::firstOrCreate(['name' => 'pembayaran-pranota-ongkos-truk-create']);
        $user->permissions()->attach($permission->id);

        $this->actingAs($user);

        // Create a test pranota
        $pranota = PranotaOngkosTruk::create([
            'no_pranota' => 'POT0626000001',
            'tanggal_pranota' => now()->toDateString(),
            'total_nominal' => 1500000,
            'status_pembayaran' => 'unpaid',
            'status' => 'submitted',
        ]);

        $data = [
            'pranota_ongkos_truk_ids' => [$pranota->id],
            'nomor_pembayaran' => 'SIS-06-26-000001',
            'nomor_accurate' => 'ACC-123',
            'tanggal_pembayaran' => now()->toDateString(),
            'total_pembayaran' => 1500000,
            'bank' => 'Bank BCA',
            'jenis_transaksi' => 'Kredit',
            'total_tagihan_penyesuaian' => 0,
            'total_tagihan_setelah_penyesuaian' => 1500000,
            'alasan_penyesuaian' => 'None',
            'keterangan' => 'Test pembayaran POT',
        ];

        // Create bank COA so validation or recordDoubleEntry works
        \App\Models\Coa::firstOrCreate([
            'nama_akun' => 'Bank BCA',
            'nomor_akun' => '1111',
            'tipe_akun' => 'bank',
        ]);
        $biayaTrucking = \App\Models\Coa::firstOrCreate([
            'nama_akun' => 'Biaya Trucking',
            'nomor_akun' => '5111',
            'tipe_akun' => 'biaya',
        ]);
        $biayaLain = \App\Models\Coa::firstOrCreate([
            'nama_akun' => 'Biaya Lain Truk',
            'nomor_akun' => '5112',
            'tipe_akun' => 'biaya',
        ]);
        $data['akun_items'] = [
            ['akun_coa_id' => $biayaTrucking->id, 'nominal' => 1000000, 'memo' => 'Ongkos utama'],
            ['akun_coa_id' => $biayaLain->id, 'nominal' => 500000, 'memo' => 'Biaya tambahan'],
        ];

        $response = $this->post(route('pembayaran-pranota-ongkos-truk.store'), $data);
        $response->assertRedirect(route('pembayaran-pranota-ongkos-truk.index'));

        $this->assertDatabaseHas('pembayaran_pranota_ongkos_truks', [
            'bank' => 'Bank BCA',
            'total_pembayaran' => 1500000,
        ]);

        $pembayaran = \App\Models\PembayaranPranotaOngkosTruk::firstOrFail();
        $this->assertCount(2, $pembayaran->akunItems);
        $this->assertDatabaseHas('pembayaran_pranota_ongkos_truk_akun_items', [
            'pembayaran_pranota_ongkos_truk_id' => $pembayaran->id,
            'akun_coa_id' => $biayaLain->id,
            'nominal' => 500000,
            'memo' => 'Biaya tambahan',
        ]);
        $jurnal = \App\Models\CoaTransaction::where('nomor_referensi', $pembayaran->nomor_pembayaran)->get();
        $this->assertCount(3, $jurnal);
        $this->assertEquals(1500000, $jurnal->sum('debit'));
        $this->assertEquals(1500000, $jurnal->sum('kredit'));
        $this->assertStringContainsString('Biaya tambahan', $jurnal->where('coa_id', $biayaLain->id)->first()->keterangan);

        $this->assertEquals('paid', $pranota->fresh()->status_pembayaran);
    }
}
