<?php

namespace Tests\Feature;

use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckpointTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function supir_can_submit_checkpoint_form()
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $this->withSession(['_token' => csrf_token()]);

        $karyawan = \App\Models\Karyawan::factory()->create([
            'divisi' => 'SUPIR',
        ]);
        $user = User::factory()->create([
            'karyawan_id' => $karyawan->id,
        ]);
        $permohonan = Permohonan::factory()->create([
            'jumlah_kontainer' => 1,
            'supir_id' => $karyawan->id,
        ]);

        $this->actingAs($user);

        $gudang = \App\Models\Gudang::create([
            'nama_gudang' => 'Gudang Tes',
            'lokasi' => 'Batam',
        ]);

        $kontainer = \App\Models\Kontainer::factory()->create();
        $response = $this->post(route('supir.checkpoint.store', $permohonan), [
            'tanggal_checkpoint' => now()->format('Y-m-d'),
            'nomor_kontainer' => [$kontainer->nomor_seri_gabungan],
            'surat_jalan_vendor' => 'SJ-001',
            'catatan' => 'Tes checkpoint',
            'gudang_tujuan_id' => $gudang->id,
        ], [
            'X-CSRF-TOKEN' => csrf_token(),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('checkpoints', [
            'catatan' => 'Tes checkpoint',
            'surat_jalan_vendor' => 'SJ-001',
        ]);
    }
}
