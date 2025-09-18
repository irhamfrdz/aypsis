<?php

use Tests\TestCase;
use App\Http\Controllers\MasterCoaController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MasterCoaControllerTest extends TestCase
{
    public function test_download_template()
    {
        $controller = new MasterCoaController();
        $response = $controller->downloadTemplate();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('attachment; filename=', $response->headers->get('Content-Disposition'));
    }
}
