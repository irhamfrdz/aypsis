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
    public function test_sekar_permata_voyages_exclude_foreign_voyages_and_keep_docking(): void
    {
        foreach (['KM. SEKAR PERMATA', 'KM SEKAR PERMATA', 'Sekar Permata'] as $ship) {
            foreach (['ST01JP26', 'ST08JP25', 'SA18JT25', 'AP25JB25', 'MAJB430N25', 'MERATUSJB444N', 'SR03JP26', 'APDOCK310326', ''] as $voyage) {
                $this->assertFalse(RekapShipName::isValidVoyage($ship, $voyage));
            }
            foreach (['SP01JB26', 'SP16PJ26', 'SP16JP26', 'SP22JB25', 'SP20PJ25', ' sp22jp25 ', 'SPDOCK150726', 'DOCK'] as $voyage) {
                $this->assertTrue(RekapShipName::isValidVoyage($ship, $voyage));
            }
        }
        $this->assertTrue(RekapShipName::isValidVoyage('KM SENTOSA 18', 'ST01JP26'));
    }

    public function test_alken_princess_voyages_exclude_other_vessels_and_preserve_docking_references(): void
    {
        foreach (['KM. ALKEN PRINCESS', 'KM ALKEN PRINCESS', 'Alken Princess'] as $ship) {
            foreach (['SA01JP26', 'SA18JT25', 'SP22JB25', 'ST07JB25', 'SR03JP26', 'AS17JP26', 'SADOCK310326', ''] as $voyage) {
                $this->assertFalse(RekapShipName::isValidVoyage($ship, $voyage));
            }
            foreach (['AP14JB26', 'AP01BJ26', 'AP05JP26', 'AP05PJ26', 'AP23BJ25', ' ap25jb25 ', 'APDOCK310326', 'DOCK'] as $voyage) {
                $this->assertTrue(RekapShipName::isValidVoyage($ship, $voyage));
            }
        }
        $this->assertTrue(RekapShipName::isValidVoyage('KM. SUMBER ABADI 178', 'SA01JP26'));
    }

    public function test_sumber_abadi_voyages_exclude_other_vessels_and_keep_historical_routes(): void
    {
        foreach (['KM. SUMBER ABADI 178', 'KM SUMBER ABADI 178', 'Sumber Abadi 178'] as $ship) {
            foreach (['SR03JP26', 'SR18JB25', 'SP22JP25', 'SP22JB25', 'ST07JB25', 'AP25JB25', 'AS17JP26', ''] as $voyage) {
                $this->assertFalse(RekapShipName::isValidVoyage($ship, $voyage));
            }
            foreach (['SA17JB26', 'SA03JP26', 'SA03PJ26', 'SA04BJ26', 'SA18JT25', 'SA16BJ25', ' sa19jb25 ', 'DOCK'] as $voyage) {
                $this->assertTrue(RekapShipName::isValidVoyage($ship, $voyage));
            }
        }
        $this->assertTrue(RekapShipName::isValidVoyage('KM SRIWIJAYA', 'SR03JP26'));
    }

    public function test_alken_pesona_voyages_exclude_other_vessels_but_keep_its_own_routes_and_dock(): void
    {
        foreach (['KM ALKEN PESONA', 'KM. ALKEN PESONA', 'Alken Pesona'] as $ship) {
            foreach (['SR07JB26', 'ST04JB26', 'SA04JB26', 'SR18JB25', 'BATANGHARI13', 'AP25JB25', ''] as $voyage) {
                $this->assertFalse(RekapShipName::isValidVoyage($ship, $voyage));
            }
            foreach (['AS01BJ26', 'AS17JP26', 'AS17PJ26', ' as18jp26 ', 'AS01', 'DOCK'] as $voyage) {
                $this->assertTrue(RekapShipName::isValidVoyage($ship, $voyage));
            }
        }
        $this->assertTrue(RekapShipName::isValidVoyage('KM SRIWIJAYA', 'SR07JB26'));
        $this->assertTrue(RekapShipName::isValidVoyage('KM SENTOSA 18', 'ST04JB26'));
    }

    public function test_sriwijaya_voyage_choices_exclude_reference_numbers_and_other_ships(): void
    {
        foreach (['KM SRIWIJAYA', 'KM. SRIWIJAYA RAYA'] as $ship) {
            foreach (['AYP 0004632', 'MERATUSJB446', 'AP25JB25', 'SP22JB25', ''] as $voyage) {
                $this->assertFalse(RekapShipName::isValidVoyage($ship, $voyage));
            }
            foreach (['SR18JB26', 'SR17BJ26', 'SR04PJ26', 'SR04JP26', 'SR18JB25', ' sr01bj26 ', 'SR01', 'DOCK'] as $voyage) {
                $this->assertTrue(RekapShipName::isValidVoyage($ship, $voyage));
            }
        }

        $this->assertTrue(RekapShipName::isValidVoyage('KM MERATUS AMPANA', 'MERATUSJB446'));
        $this->assertTrue(RekapShipName::isValidVoyage('KM ALKEN PRINCESS', 'AP25JB25'));
    }

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
