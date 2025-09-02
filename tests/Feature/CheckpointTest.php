<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Permohonan;
use App\Models\Checkpoint;

class CheckpointTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function supir_can_submit_checkpoint_form()
    {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    $this->withSession(['_token' => csrf_token()]);
        $user = User::factory()->create();
        $permohonan = Permohonan::factory()->create([
            'jumlah_kontainer' => 1,
        ]);

        $this->actingAs($user);

        $kontainer = \App\Models\Kontainer::factory()->create();
        $response = $this->post(route('supir.checkpoint.store', $permohonan), [
            'tanggal_checkpoint' => now()->format('Y-m-d'),
            'nomor_kontainer' => [$kontainer->id],
            'surat_jalan_vendor' => 'SJ-001',
            'catatan' => 'Tes checkpoint',
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
