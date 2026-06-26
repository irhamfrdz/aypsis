<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterKapalController;
use App\Models\MasterKapal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MasterKapalSpkbmTest extends TestCase
{
    use RefreshDatabase;

    public function test_print_spkbm_saves_data_to_database()
    {
        // 1. Create a MasterKapal
        $kapal = MasterKapal::create([
            'kode' => 'KPL001',
            'nama_kapal' => 'MV Bintang',
            'nickname' => 'Bintang',
            'status' => 'aktif',
        ]);

        // 2. Prepare request data
        $requestData = [
            'nomor_surat' => '123/AYP-SPKBM/VI/2026',
            'hal' => 'Surat Penunjukan Kerja Bongkar Muat',
            'ditujukan_kepada' => 'PT Pelindo',
            'voyage' => 'Voy-01',
            'rencana_tiba' => 'Rencana Tiba',
            'rencana_sandar' => 'Rencana Sandar',
            'rencana_bongkar' => 'Rencana Bongkar',
            'rencana_muat' => 'Rencana Muat',
            'tujuan' => 'Jakarta',
        ];

        $request = Request::create('/master-kapal/'.$kapal->id.'/print-spkbm', 'POST', $requestData);

        // Bind the request to container so validation works
        $this->app->instance('request', $request);

        $controller = new MasterKapalController;
        $response = $controller->printSpkbm($request, $kapal);

        // 3. Assert response is PDF stream
        $this->assertEquals(200, $response->getStatusCode());

        // 4. Assert data is saved in database
        $this->assertDatabaseHas('kapal_spkbms', [
            'kapal_id' => $kapal->id,
            'nomor_surat' => '123/AYP-SPKBM/VI/2026',
            'hal' => 'Surat Penunjukan Kerja Bongkar Muat',
            'ditujukan_kepada' => 'PT Pelindo',
            'voyage' => 'Voy-01',
            'tujuan' => 'Jakarta',
        ]);
    }
}
