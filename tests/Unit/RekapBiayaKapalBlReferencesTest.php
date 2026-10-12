<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalDokumen;
use App\Models\BiayaKapalTrucking;
use App\Models\Manifest;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class RekapBiayaKapalBlReferencesTest extends TestCase
{
    public function test_trucking_manifest_ids_resolve_to_bl_numbers_without_exposing_ids(): void
    {
        $record = (new BiayaKapalTrucking)->forceFill(['no_bl' => ['77', '78', '999']]);
        $manifests = collect([
            (new Manifest)->forceFill(['id' => 77, 'nomor_bl' => '01, 02']),
            (new Manifest)->forceFill(['id' => 78, 'nomor_bl' => '03']),
        ]);
        $references = (new ReflectionMethod(RekapBiayaKapalController::class, 'blReferences'))
            ->invoke(new RekapBiayaKapalController, $record, $manifests);
        $this->assertSame(['01', '02', '03'], $references->all());
    }

    public function test_array_and_string_bl_references_support_multiple_delimiters(): void
    {
        $method = new ReflectionMethod(RekapBiayaKapalController::class, 'blReferences');
        $controller = new RekapBiayaKapalController;
        $header = (new BiayaKapal)->forceFill(['no_bl' => ['01, 02', "03;04\n05", '01', null]]);
        $document = (new BiayaKapalDokumen)->forceFill(['nomor_bl' => "01, 02;03\r\n04"]);
        $this->assertSame(['01', '02', '03', '04', '05'], $method->invoke($controller, $header, collect())->all());
        $this->assertSame(['01', '02', '03', '04'], $method->invoke($controller, $document, collect())->all());
    }
}
