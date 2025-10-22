<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaDimensiItem;
use App\Models\Term;

class TandaTerimaTanpaSuratJalanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that TandaTerimaTanpaSuratJalan model can be created with required fields
     */
    public function test_tanda_terima_can_be_created()
    {
        $data = [
            'tanggal_tanda_terima' => '2025-10-21',
            'nomor_tanda_terima' => 'TEST-001',
            'nomor_surat_jalan_customer' => 'SJ-CUSTOMER-001',
            'pengirim' => 'Test Pengirim',
            'penerima' => 'Test Penerima',
            'jenis_barang' => 'Test Barang',
            'jumlah_barang' => 1,
            'satuan_barang' => 'unit',
            'supir' => 'Supir Customer',
            'kenek' => 'Kenek Customer',
            'tujuan_pengiriman' => 'Test Tujuan',
            'estimasi_naik_kapal' => 'Test Kapal',
            'no_tanda_terima' => TandaTerimaTanpaSuratJalan::generateNoTandaTerima(),
            'created_by' => 'Test User',
            'status' => 'draft'
        ];

        $tandaTerima = TandaTerimaTanpaSuratJalan::create($data);

        $this->assertInstanceOf(TandaTerimaTanpaSuratJalan::class, $tandaTerima);
        $this->assertEquals('Test Pengirim', $tandaTerima->pengirim);
        $this->assertEquals('Test Penerima', $tandaTerima->penerima);
        $this->assertEquals('draft', $tandaTerima->status);
    }

    /**
     * Test required field validation
     */
    public function test_required_fields_validation()
    {
        $requiredFields = [
            'tanggal_tanda_terima',
            'pengirim',
            'penerima',
            'jenis_barang',
            'jumlah_barang',
            'satuan_barang',
            'supir',
            'tujuan_pengiriman'
        ];

        // Each required field should be present
        foreach ($requiredFields as $field) {
            $this->assertContains($field, (new TandaTerimaTanpaSuratJalan())->getFillable());
        }
    }

    /**
     * Test form field compatibility
     */
    public function test_form_fields_are_fillable()
    {
        $formFields = [
            // Basic info
            'nomor_tanda_terima',
            'tanggal_tanda_terima',
            'nomor_surat_jalan_customer',
            'term_id',
            'estimasi_naik_kapal',

            // Sender/Receiver info
            'pengirim',
            'penerima',
            'alamat_pengirim',
            'alamat_penerima',
            'pic',
            'telepon',

            // Goods info
            'jenis_barang',
            'nama_barang',
            'aktifitas',
            'jumlah_barang',
            'satuan_barang',
            'berat',
            'satuan_berat',
            'keterangan_barang',

            // Transport info
            'supir',
            'kenek',
            'no_plat',
            'tujuan_pengiriman',
            'no_kontainer',
            'size_kontainer',
            'no_seal',

            // Dimensions (backward compatibility)
            'panjang',
            'lebar',
            'tinggi',
            'meter_kubik',
            'tonase',

            // Notes
            'catatan',
            'status',
            'created_by',
            'updated_by'
        ];

        $fillableFields = (new TandaTerimaTanpaSuratJalan())->getFillable();

        foreach ($formFields as $field) {
            $this->assertContains(
                $field,
                $fillableFields,
                "Field '{$field}' from form is not fillable in model"
            );
        }
    }

    /**
     * Test number generation
     */
    public function test_tanda_terima_number_generation()
    {
        $number = TandaTerimaTanpaSuratJalan::generateNoTandaTerima();

        $this->assertIsString($number);
        $this->assertStringStartsWith('TTR-', $number);

        // Should contain current year
        $currentYear = date('Y');
        $this->assertStringContainsString($currentYear, $number);
    }
}
