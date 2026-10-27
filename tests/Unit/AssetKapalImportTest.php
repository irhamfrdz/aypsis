<?php

namespace Tests\Unit;

use App\Imports\AssetKapalImport;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AssetKapalImportTest extends TestCase
{
    public function test_uppercase_choices_and_missing_optional_fields_are_normalized(): void
    {
        $import = new AssetKapalImport;
        $row = $import->prepareForValidation([
            'nama_asset' => 'BREATHING APPARATUS',
            'nama_kapal' => 'KM. ALEXINDO 8',
            'kategori' => ' KESELAMATAN ',
            'lokasi_asset' => null,
            'kondisi' => null,
        ], 2);

        $this->assertSame('Keselamatan', $row['kategori']);
        $this->assertNull($row['lokasi_asset']);
        $this->assertSame('Belum diketahui', $row['kondisi']);
        $this->assertTrue(Validator::make($row, $import->rules())->passes());

        $dashRow = $import->prepareForValidation(array_merge($row, [
            'kondisi' => '-',
            'lokasi_asset' => ' engine room ',
        ]), 3);
        $this->assertSame('Belum diketahui', $dashRow['kondisi']);
        $this->assertSame('ENGINE ROOM', $dashRow['lokasi_asset']);
    }

    public function test_unknown_category_remains_invalid(): void
    {
        $import = new AssetKapalImport;
        $row = $import->prepareForValidation([
            'nama_asset' => 'Contoh',
            'nama_kapal' => 'KM. ALEXINDO 8',
            'kategori' => 'LAINNYA',
            'kondisi' => 'BAGUS',
        ], 2);

        $this->assertSame('Bagus', $row['kondisi']);
        $this->assertTrue(Validator::make($row, $import->rules())->errors()->has('kategori'));
    }
}
