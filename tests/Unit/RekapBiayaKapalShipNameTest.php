<?php

namespace Tests\Unit;

use App\Helpers\RekapShipName;
use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalUmum;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class RekapBiayaKapalShipNameTest extends TestCase
{
    public function test_sriwijaya_aliases_share_one_selector_key_and_search_pattern(): void
    {
        $ships = [];
        foreach (['KM SRIWIJAYA', 'KM. SRIWIJAYA RAYA', 'Sriwijaya Raya'] as $name) {
            $ships[RekapShipName::key($name)] = $name;
            $this->assertSame('%sriwijaya%', RekapShipName::likePattern($name));
        }

        $this->assertCount(1, $ships);
        $this->assertNotSame(RekapShipName::key('Sriwijaya'), RekapShipName::key('Sriwijaya Indah'));
    }

    public function test_recap_matches_sriwijaya_costs_under_both_names(): void
    {
        $controller = new RekapBiayaKapalController;
        $matches = new ReflectionMethod(RekapBiayaKapalController::class, 'recordHasShipAndVoyage');
        $relations = new \ReflectionProperty(RekapBiayaKapalController::class, 'relations');

        foreach (['KM SRIWIJAYA' => 'KM. SRIWIJAYA RAYA', 'KM. SRIWIJAYA RAYA' => 'KM SRIWIJAYA'] as $stored => $selected) {
            $record = new BiayaKapal;
            $record->nama_kapal = [$stored];
            $record->no_voyage = ['SR01'];
            foreach ($relations->getValue($controller) as $relation) {
                $record->setRelation($relation, collect());
            }
            $this->assertTrue($matches->invoke($controller, $record, $selected, 'SR01'));
            $this->assertFalse($matches->invoke($controller, $record, $selected, 'SR02'));

            $record->setRelation('umumDetails', collect([
                new BiayaKapalUmum(['kapal' => $stored, 'voyage' => 'SR01', 'nominal' => 150000, 'pph' => 0]),
            ]));
            $this->assertEquals(150000, $controller->getApportionedCostForRecord($record, $selected, 'SR01')['total_biaya']);
        }
    }

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
