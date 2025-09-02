<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\MasterPricelistSewaKontainer;

class MasterPricelistSewaKontainerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Gate::define('master-pricelist-sewa-kontainer', function () {
            return true;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_store_pricelist_sewa_kontainer_without_nomor_tagihan()
    {
        $permission = \App\Models\Permission::create(['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Test permission']);
        $user = \App\Models\User::factory()->create();
        $user->permissions()->attach($permission->id);
        $this->actingAs($user);

        $data = [
            'vendor' => 'ZONA',
            'tarif' => 'Bulanan',
            'ukuran_kontainer' => '20',
            'harga' => 1000000,
            'tanggal_harga_awal' => '2025-08-20',
            'tanggal_harga_akhir' => '2025-09-20',
            'keterangan' => 'Test data',
        ];

        $response = $this->post(route('master.pricelist-sewa-kontainer.store'), $data);

        $response->assertRedirect(route('master.pricelist-sewa-kontainer.index'));
        $this->assertDatabaseHas('master_pricelist_sewa_kontainers', [
            'vendor' => 'ZONA',
            'tarif' => 'Bulanan',
            'ukuran_kontainer' => '20',
            'harga' => 1000000,
            'tanggal_harga_awal' => '2025-08-20',
            'tanggal_harga_akhir' => '2025-09-20',
            'keterangan' => 'Test data',
        ]);
    }
}
