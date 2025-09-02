<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Permohonan;
use App\Models\TagihanKontainerSewa;
use App\Models\Karyawan;
use App\Models\User;

class TagihanKontainerSewaTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_zona_or_dpe_permohonan_creates_tagihan_kontainer_sewa()
    {
        // Buat user dan karyawan supir
        $user = User::factory()->create();
        $supir = Karyawan::factory()->create();
        $this->actingAs($user);

        // Buat permohonan vendor ZONA
        $permohonan = Permohonan::factory()->create([
            'vendor_perusahaan' => 'ZONA',
            'supir_id' => $supir->id,
            'status' => 'draft',
            'jumlah_uang_jalan' => 100000,
            'ukuran' => '20',
            'tanggal_memo' => '2025-08-20',
        ]);
        $kontainer = \App\Models\Kontainer::factory()->create(['ukuran' => '20']);
        $permohonan->kontainers()->attach($kontainer->id);

        // Simulasikan approval
        $response = $this->post(route('approval.store', $permohonan), [
            'status_permohonan' => 'selesai',
            'tanggal_masuk_sewa' => '2025-08-20',
        ]);
        $response->assertRedirect(route('approval.dashboard'));

        // Pastikan tagihan kontainer sewa tercipta
        $this->assertDatabaseHas('tagihan_kontainer_sewa', [
            'vendor' => 'ZONA',
            'tanggal_harga_awal' => '2025-08-20',
        ]);

        // Buat permohonan vendor DPE
        $permohonanDpe = Permohonan::factory()->create([
            'vendor_perusahaan' => 'DPE',
            'supir_id' => $supir->id,
            'status' => 'draft',
            'jumlah_uang_jalan' => 200000,
            'ukuran' => '40',
            'tanggal_memo' => '2025-08-20',
        ]);
        $kontainerDpe = \App\Models\Kontainer::factory()->create(['ukuran' => '40']);
        $permohonanDpe->kontainers()->attach($kontainerDpe->id);

        $responseDpe = $this->post(route('approval.store', $permohonanDpe), [
            'status_permohonan' => 'selesai',
            'tanggal_masuk_sewa' => '2025-08-20',
        ]);
        $responseDpe->assertRedirect(route('approval.dashboard'));

        $this->assertDatabaseHas('tagihan_kontainer_sewa', [
            'vendor' => 'DPE',
            'tanggal_harga_awal' => '2025-08-20',
        ]);
    }
}
