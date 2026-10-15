<?php

namespace Tests\Feature;

use App\Exports\PranotaPumlExport;
use App\Models\Karyawan;
use App\Models\PranotaPuml;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class PranotaPumlExportDepartemenTest extends TestCase
{
    public function test_departemen_is_exported_for_uang_makan_and_lembur_employees(): void
    {
        $uangMakanEmployee = new Karyawan([
            'nik' => '0023',
            'nama_lengkap' => 'ACENG JAELANI',
            'departemen' => 'Operasional',
            'akun_bank' => '001234',
        ]);
        $lemburEmployee = new Karyawan([
            'nik' => '0045',
            'nama_lengkap' => 'KARYAWAN LEMBUR',
            'departemen' => 'Keuangan',
            'akun_bank' => '005678',
            'nominal_uang_makan' => 10000,
        ]);
        $puml = new class(['nomor_pranota' => 'PUML-001']) extends PranotaPuml {
            public function load($relations)
            {
                return $this;
            }
        };
        $puml->setRelation('uangMakans', collect([
            (object) ['details' => collect([
                (object) [
                    'tipe_karyawan' => null,
                    'karyawan_id' => 1,
                    'karyawan' => $uangMakanEmployee,
                    'kehadiran' => 2,
                    'nominal_awal' => 20000,
                    'total_akhir' => 20000,
                ],
            ])],
        ]));
        $puml->setRelation('lemburs', collect([
            (object) ['karyawans' => collect([
                (object) [
                    'karyawan_id' => 2,
                    'karyawan' => $lemburEmployee,
                    'total_akhir' => 5000,
                ],
            ])],
        ]));
        $puml->setRelation('potongans', collect());
        $puml->setRelation('creator', null);

        $response = Excel::download(new PranotaPumlExport($puml), 'puml.xlsx');
        $path = $response->getFile()->getPathname();

        try {
            $sheet = IOFactory::load($path)->getActiveSheet();

            $this->assertSame('DEPARTEMEN', $sheet->getCell('D5')->getValue());
            $this->assertSame('No REK', $sheet->getCell('E5')->getValue());
            $this->assertSame('OPERASIONAL', $sheet->getCell('D6')->getValue());
            $this->assertSame('KEUANGAN', $sheet->getCell('D7')->getValue());
            $this->assertSame('001234', $sheet->getCell('E6')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('E6')->getDataType());
            $this->assertSame('0023', $sheet->getCell('B6')->getValue());
            $this->assertEquals(20000, $sheet->getCell('J6')->getValue());
            $this->assertEquals(5000, $sheet->getCell('I7')->getValue());
            $this->assertEquals(25000, $sheet->getCell('N8')->getValue());
            $this->assertSame('A5:N7', $sheet->getAutoFilter()->getRange());
        } finally {
            unlink($path);
        }
    }
}
