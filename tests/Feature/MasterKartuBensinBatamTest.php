<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\MasterKartuBensinBatam;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterKartuBensinBatamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Register bypass gate definitions in tests if needed
        \Illuminate\Support\Facades\Gate::define('master-kartu-bensin-batam-view', function () {
            return true;
        });
        \Illuminate\Support\Facades\Gate::define('master-kartu-bensin-batam-create', function () {
            return true;
        });
        \Illuminate\Support\Facades\Gate::define('master-kartu-bensin-batam-edit', function () {
            return true;
        });
        \Illuminate\Support\Facades\Gate::define('master-kartu-bensin-batam-delete', function () {
            return true;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_index_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('master-kartu-bensin-batam.index'));

        $response->assertStatus(200);
        $response->assertViewIs('master-kartu-bensin-batam.index');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_create_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('master-kartu-bensin-batam.create'));

        $response->assertStatus(200);
        $response->assertViewIs('master-kartu-bensin-batam.create');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_store_kartu_bensin()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $mobil = Mobil::factory()->create();
        $karyawan = Karyawan::factory()->create();

        $data = [
            'nomor_kartu' => '1234567890',
            'nama_kartu' => 'Kartu Test Batam',
            'provider' => 'Pertamina Brizzi',
            'mobil_id' => $mobil->id,
            'karyawan_id' => $karyawan->id,
            'status' => 'aktif',
            'saldo' => 150000,
            'keterangan' => 'Kartu bbm uji coba',
        ];

        $response = $this->post(route('master-kartu-bensin-batam.store'), $data);

        $response->assertRedirect(route('master-kartu-bensin-batam.index'));
        $this->assertDatabaseHas('master_kartu_bensin_batams', [
            'nomor_kartu' => '1234567890',
            'nama_kartu' => 'Kartu Test Batam',
            'provider' => 'Pertamina Brizzi',
            'status' => 'aktif',
            'saldo' => 150000,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_edit_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $card = MasterKartuBensinBatam::create([
            'nomor_kartu' => '987654321',
            'nama_kartu' => 'Kartu Test Edit',
            'provider' => 'Shell',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('master-kartu-bensin-batam.edit', $card->id));

        $response->assertStatus(200);
        $response->assertViewIs('master-kartu-bensin-batam.edit');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_kartu_bensin()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $card = MasterKartuBensinBatam::create([
            'nomor_kartu' => '5555555',
            'nama_kartu' => 'Kartu Awal',
            'provider' => 'Pertamina',
            'status' => 'aktif',
        ]);

        $data = [
            'nomor_kartu' => '5555555',
            'nama_kartu' => 'Kartu Diupdate',
            'provider' => 'Pertamina BNI',
            'status' => 'tidak_aktif',
            'saldo' => 250000,
            'keterangan' => 'Keterangan diupdate',
        ];

        $response = $this->put(route('master-kartu-bensin-batam.update', $card->id), $data);

        $response->assertRedirect(route('master-kartu-bensin-batam.index'));
        $this->assertDatabaseHas('master_kartu_bensin_batams', [
            'id' => $card->id,
            'nama_kartu' => 'Kartu Diupdate',
            'status' => 'tidak_aktif',
            'saldo' => 250000,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_delete_kartu_bensin()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $card = MasterKartuBensinBatam::create([
            'nomor_kartu' => '999999999',
            'nama_kartu' => 'Kartu Dihapus',
            'provider' => 'Pertamina',
            'status' => 'aktif',
        ]);

        $response = $this->delete(route('master-kartu-bensin-batam.destroy', $card->id));

        $response->assertRedirect(route('master-kartu-bensin-batam.index'));
        $this->assertDatabaseMissing('master_kartu_bensin_batams', [
            'id' => $card->id,
        ]);
    }
}
