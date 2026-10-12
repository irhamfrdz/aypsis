<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalUmum;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class RekapBiayaKapalShipNameTest extends TestCase
{
    public function test_recap_matches_costs_saved_with_another_spelling_of_the_same_ship(): void
    {
        $record = new BiayaKapal;
        $record->nama_kapal = ['KM ALKEN PRINCESS'];
        $record->no_voyage = ['AP01BJ26'];

        $matches = new ReflectionMethod(RekapBiayaKapalController::class, 'recordHasShipAndVoyage');
        $controller = new RekapBiayaKapalController;

        $this->assertTrue($matches->invoke($controller, $record, 'KM. ALKEN PRINCESS', 'AP01BJ26'));
        $this->assertFalse($matches->invoke($controller, $record, 'KM. ALKEN PESONA', 'AP01BJ26'));
    }

    public function test_recap_includes_detail_costs_from_the_other_ship_spelling(): void
    {
        $controller = new RekapBiayaKapalController;
        $record = new BiayaKapal;
        $relations = new \ReflectionProperty(RekapBiayaKapalController::class, 'relations');
        foreach ($relations->getValue($controller) as $relation) {
            $record->setRelation($relation, collect());
        }
        $record->setRelation('umumDetails', collect([
            new BiayaKapalUmum(['kapal' => 'KM ALKEN PRINCESS', 'voyage' => 'AP01BJ26', 'nominal' => 150000, 'pph' => 0]),
        ]));

        $cost = $controller->getApportionedCostForRecord($record, 'KM. ALKEN PRINCESS', 'AP01BJ26');

        $this->assertEquals(150000, $cost['total_biaya']);
    }
}
