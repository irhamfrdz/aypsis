<?php

namespace Tests\Feature;

use App\Models\MasterChasisBatam;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class MasterChasisBatamTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user and permission with karyawan
        $karyawan = \App\Models\Karyawan::factory()->create();
        $this->user = User::factory()->create([
            'karyawan_id' => $karyawan->id,
            'status' => 'approved',
        ]);

        // Register permissions in DB
        Permission::firstOrCreate(['name' => 'master-chasis-batam-view'], ['description' => 'View']);
        Permission::firstOrCreate(['name' => 'master-chasis-batam-create'], ['description' => 'Create']);
        Permission::firstOrCreate(['name' => 'master-chasis-batam-update'], ['description' => 'Update']);
        Permission::firstOrCreate(['name' => 'master-chasis-batam-delete'], ['description' => 'Delete']);

        // Mock Gates for test execution
        Gate::define('master-chasis-batam-view', function ($user) {
            return $user->permissions()->where('name', 'master-chasis-batam-view')->exists();
        });
        Gate::define('master-chasis-batam-create', function ($user) {
            return $user->permissions()->where('name', 'master-chasis-batam-create')->exists();
        });
        Gate::define('master-chasis-batam-update', function ($user) {
            return $user->permissions()->where('name', 'master-chasis-batam-update')->exists();
        });
        Gate::define('master-chasis-batam-delete', function ($user) {
            return $user->permissions()->where('name', 'master-chasis-batam-delete')->exists();
        });
    }

    public function test_user_without_permission_cannot_view_index()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('master.chasis-batam.index'));
        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_view_index()
    {
        $this->user->permissions()->attach(Permission::where('name', 'master-chasis-batam-view')->first()->id);
        $this->actingAs($this->user);

        // Seed some data
        MasterChasisBatam::create([
            'kode' => 'CH-0001',
            'tipe' => '20ft',
            'kondisi' => 'baik',
            'lokasi' => 'sm',
            'tanggal_terakhir_pakai' => '2026-07-02',
        ]);

        $response = $this->get(route('master.chasis-batam.index'));
        $response->assertStatus(200);
        $response->assertSee('CH-0001');
        $response->assertSee('SM');
    }

    public function test_user_with_permission_can_store_chasis()
    {
        $this->user->permissions()->attach(Permission::where('name', 'master-chasis-batam-create')->first()->id);
        $this->actingAs($this->user);

        $data = [
            'kode' => 'CH-9999',
            'tipe' => '40ft',
            'kondisi' => 'rusak',
            'lokasi' => 'relasi',
            'tanggal_terakhir_pakai' => '2026-07-01',
            'catatan' => 'Testing store',
        ];

        $response = $this->post(route('master.chasis-batam.store'), $data);

        $response->assertRedirect(route('master.chasis-batam.index'));
        $this->assertDatabaseHas('master_chasis_batams', [
            'kode' => 'CH-9999',
            'kondisi' => 'rusak',
            'lokasi' => 'relasi',
        ]);
    }

    public function test_user_with_permission_can_update_chasis()
    {
        $this->user->permissions()->attach(Permission::where('name', 'master-chasis-batam-update')->first()->id);
        $this->actingAs($this->user);

        $chasis = MasterChasisBatam::create([
            'kode' => 'CH-0001',
            'tipe' => '20ft',
            'kondisi' => 'baik',
            'lokasi' => 'sm',
            'tanggal_terakhir_pakai' => '2026-07-02',
        ]);

        $data = [
            'kode' => 'CH-0001-REV',
            'tipe' => '40ft',
            'kondisi' => 'rusak',
            'lokasi' => 'relasi',
            'tanggal_terakhir_pakai' => '2026-07-03',
            'catatan' => 'Updated notes',
        ];

        $response = $this->put(route('master.chasis-batam.update', $chasis->id), $data);

        $response->assertRedirect(route('master.chasis-batam.index'));
        $this->assertDatabaseHas('master_chasis_batams', [
            'id' => $chasis->id,
            'kode' => 'CH-0001-REV',
            'kondisi' => 'rusak',
            'lokasi' => 'relasi',
        ]);
    }

    public function test_user_with_permission_can_delete_chasis()
    {
        $this->user->permissions()->attach(Permission::where('name', 'master-chasis-batam-delete')->first()->id);
        $this->actingAs($this->user);

        $chasis = MasterChasisBatam::create([
            'kode' => 'CH-0001',
            'tipe' => '20ft',
            'kondisi' => 'baik',
            'lokasi' => 'sm',
            'tanggal_terakhir_pakai' => '2026-07-02',
        ]);

        $response = $this->delete(route('master.chasis-batam.destroy', $chasis->id));

        $response->assertRedirect(route('master.chasis-batam.index'));
        $this->assertSoftDeleted('master_chasis_batams', [
            'id' => $chasis->id,
        ]);
    }
}
