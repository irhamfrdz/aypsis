<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Permohonan;

class ApprovalMassProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_mass_process_route_accepts_post_and_processes_items()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $permohonan = Permohonan::factory()->create(['status' => 'draft']);

        $response = $this->post(route('approval.mass_process'), [
            'permohonan_ids' => [$permohonan->id],
        ]);

        $response->assertRedirect(route('approval.dashboard'));
        $this->assertDatabaseHas('permohonans', ['id' => $permohonan->id, 'status' => 'Selesai']);
    }
}
