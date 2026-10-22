<?php

namespace Tests\Feature;

use App\Http\Controllers\AssetController;
use App\Imports\AssetImport;
use App\Models\Asset;
use App\Models\TandaTerimaAsset;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AssetTandaTerimaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('users', fn (Blueprint $table) => $table->id());
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik');
            $table->string('departemen')->nullable();
            $table->string('divisi')->nullable();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('auditable_id');
            foreach (['user_name', 'auditable_type', 'action', 'module', 'description', 'old_values', 'new_values', 'ip_address', 'user_agent', 'url'] as $column) {
                $table->text($column)->nullable();
            }
            $table->timestamp('created_at')->nullable();
        });

        (require database_path('migrations/2026_10_05_100000_create_assets_table.php'))->up();
        (require database_path('migrations/2026_10_05_153000_add_tanggal_tanda_terima_to_assets_table.php'))->up();
        (require database_path('migrations/2026_10_22_110000_add_nama_merk_to_assets_table.php'))->up();
        Schema::table('assets', fn (Blueprint $table) => $table->unsignedBigInteger('kapal_id')->nullable());
        (require database_path('migrations/2026_10_22_120000_create_tanda_terima_asset_table.php'))->up();
        (require database_path('migrations/2026_10_22_130000_add_foto_to_tanda_terima_asset_table.php'))->up();

        DB::table('karyawans')->insert([
            'id' => 1, 'nama_lengkap' => 'Budi Santoso', 'nik' => '00123', 'departemen' => 'IT',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'kode_asset' => 'AST-0001',
            'nama_asset' => 'Laptop Kantor',
            'nama_merk' => 'ASUS',
            'kategori' => 'Elektronik & IT',
            'nomor_seri' => 'SN-0001',
            'kondisi' => 'Baik',
            'status' => 'Digunakan',
            'lokasi' => 'Di bawa oleh karyawan',
            'karyawan_id' => 1,
            'tanggal_tanda_terima' => '2026-10-22',
            'keterangan' => 'Lengkap dengan charger',
        ], $overrides);
    }

    public function test_form_creation_saves_initial_receipt_without_duplicating_it_on_edit(): void
    {
        (new AssetController)->store(Request::create('/master-asset', 'POST', $this->payload()));

        $asset = Asset::firstOrFail();
        $receipt = $asset->tandaTerima;
        $this->assertNotNull($receipt);
        $this->assertSame('ASUS', $receipt->nama_merk);
        $this->assertSame('SN-0001', $receipt->nomor_seri);
        $this->assertSame('Budi Santoso', $receipt->nama_pemegang);
        $this->assertSame('00123', $receipt->nik_pemegang);
        $this->assertSame('IT', $receipt->departemen_pemegang);
        $this->assertSame('2026-10-22', $receipt->tanggal_tanda_terima->toDateString());

        $asset->update(['nama_asset' => 'Laptop Diperbarui']);

        $this->assertSame(1, TandaTerimaAsset::count());
        $this->assertSame('Laptop Kantor', $receipt->fresh()->nama_asset);
    }

    public function test_excel_import_also_creates_receipt(): void
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['kode_asset', 'nama_asset', 'nama_merk', 'kategori', 'nomor_seri', 'nik_pemegang', 'nama_pemegang'],
            ['AST-IMPORT', 'Laptop Import', 'Dell', 'Elektronik & IT', 'SN-IMPORT', '00123', 'Budi Santoso'],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'asset-receipt-');

        try {
            (new Xlsx($spreadsheet))->save($path);
            Excel::import(new AssetImport, $path, null, \Maatwebsite\Excel\Excel::XLSX);

            $asset = Asset::where('kode_asset', 'AST-IMPORT')->firstOrFail();
            $this->assertSame('Laptop Import', $asset->tandaTerima->nama_asset);
            $this->assertSame('00123', $asset->tandaTerima->nik_pemegang);
            $this->assertSame(1, TandaTerimaAsset::count());
        } finally {
            unlink($path);
            $spreadsheet->disconnectWorksheets();
        }
    }

    public function test_asset_without_holder_still_gets_receipt(): void
    {
        (new AssetController)->store(Request::create('/master-asset', 'POST', $this->payload([
            'lokasi' => 'Jakarta', 'karyawan_id' => null, 'tanggal_tanda_terima' => null,
        ])));

        $receipt = Asset::firstOrFail()->tandaTerima;
        $this->assertNotNull($receipt);
        $this->assertNull($receipt->karyawan_id);
        $this->assertNull($receipt->nama_pemegang);
        $this->assertNull($receipt->tanggal_tanda_terima);
    }

    public function test_form_creation_rolls_back_asset_when_receipt_cannot_be_saved(): void
    {
        Event::listen('eloquent.creating: '.TandaTerimaAsset::class, function () {
            throw new RuntimeException('Receipt save failed');
        });

        try {
            (new AssetController)->store(Request::create('/master-asset', 'POST', $this->payload()));
            $this->fail('Expected receipt creation failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Receipt save failed', $exception->getMessage());
        }

        $this->assertSame(0, Asset::count());
        $this->assertSame(0, TandaTerimaAsset::count());
    }

    private function handoverRequest(array $files = []): Request
    {
        return Request::create('/master-asset/1/tanda-terima', 'POST', [
            'karyawan_id' => 1, 'tanggal_tanda_terima' => '2026-10-23',
            'status' => 'Digunakan',
        ], [], $files);
    }

    public function test_separate_receipt_photo_upload_and_replacement_preserve_asset_photo(): void
    {
        Storage::fake('tanda_terima_asset');
        $asset = Asset::create($this->payload(['foto' => 'uploads/assets/photos/original.jpg']));
        $controller = new AssetController;
        $response = $controller->updateTandaTerima($this->handoverRequest([
            'foto_tanda_terima' => UploadedFile::fake()->image('bukti.jpg'),
        ]), $asset);

        $firstPhoto = $asset->tandaTerima()->firstOrFail()->foto;
        Storage::disk('tanda_terima_asset')->assertExists($firstPhoto);
        $this->assertSame('uploads/assets/photos/original.jpg', $asset->fresh()->foto);
        $this->assertSame('2026-10-23', $asset->tandaTerima()->first()->tanggal_tanda_terima->toDateString());
        $this->assertFalse($response->getSession()->has('print_asset_id'));

        $controller->updateTandaTerima($this->handoverRequest(), $asset);
        $this->assertSame($firstPhoto, $asset->tandaTerima()->first()->foto);
        Storage::disk('tanda_terima_asset')->assertExists($firstPhoto);

        $controller->updateTandaTerima($this->handoverRequest([
            'foto_tanda_terima' => UploadedFile::fake()->image('bukti-baru.png'),
        ]), $asset);

        $newPhoto = $asset->tandaTerima()->first()->foto;
        $this->assertNotSame($firstPhoto, $newPhoto);
        Storage::disk('tanda_terima_asset')->assertExists($newPhoto);
        Storage::disk('tanda_terima_asset')->assertMissing($firstPhoto);
        $this->assertSame(1, TandaTerimaAsset::count());
    }

    public function test_failed_receipt_update_removes_new_upload_and_preserves_old_photo(): void
    {
        Storage::fake('tanda_terima_asset');
        $asset = Asset::create($this->payload());
        Storage::disk('tanda_terima_asset')->put('old.jpg', 'old proof');
        $asset->tandaTerima()->first()->update(['foto' => 'old.jpg']);
        Event::listen('eloquent.updating: '.TandaTerimaAsset::class, function () {
            throw new RuntimeException('Receipt update failed');
        });

        try {
            (new AssetController)->updateTandaTerima($this->handoverRequest([
                'foto_tanda_terima' => UploadedFile::fake()->image('new.jpg'),
            ]), $asset);
            $this->fail('Expected receipt update failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Receipt update failed', $exception->getMessage());
        }

        $this->assertSame(['old.jpg'], Storage::disk('tanda_terima_asset')->files());
        $this->assertSame('old.jpg', $asset->tandaTerima()->first()->foto);
        $this->assertSame('2026-10-22', $asset->fresh()->tanggal_tanda_terima->toDateString());
    }

    public function test_receipt_photo_rejects_non_images_and_oversized_files(): void
    {
        Storage::fake('tanda_terima_asset');
        $asset = Asset::create($this->payload());
        foreach ([UploadedFile::fake()->create('proof.pdf', 10, 'application/pdf'), UploadedFile::fake()->image('large.jpg')->size(5121)] as $file) {
            try {
                (new AssetController)->updateTandaTerima($this->handoverRequest(['foto_tanda_terima' => $file]), $asset);
                $this->fail('Expected invalid photo to be rejected.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('foto_tanda_terima', $exception->errors());
            }
        }
        $this->assertSame([], Storage::disk('tanda_terima_asset')->files());
        $this->assertNull($asset->tandaTerima()->first()->foto);
    }

    public function test_upload_creates_receipt_for_legacy_asset_without_one(): void
    {
        Storage::fake('tanda_terima_asset');
        $asset = Asset::create($this->payload());
        $asset->tandaTerima()->delete();

        (new AssetController)->updateTandaTerima($this->handoverRequest([
            'foto_tanda_terima' => UploadedFile::fake()->image('bukti.jpg'),
        ]), $asset);

        $receipt = $asset->tandaTerima()->firstOrFail();
        $this->assertSame('Budi Santoso', $receipt->nama_pemegang);
        Storage::disk('tanda_terima_asset')->assertExists($receipt->foto);
        $this->assertSame(1, TandaTerimaAsset::count());
    }

    public function test_receipt_photo_link_uses_application_host_and_serves_uploaded_image(): void
    {
        Storage::fake('tanda_terima_asset');
        config(['filesystems.disks.tanda_terima_asset.url' => 'http://wrong-host.test/uploads']);
        app('url')->forceRootUrl('http://127.0.0.1:8000');
        $asset = Asset::create($this->payload());
        $file = UploadedFile::fake()->image('proof.png');
        $photo = $file->store('', 'tanda_terima_asset');
        $receipt = $asset->tandaTerima()->firstOrFail();
        $receipt->update(['foto' => $photo]);

        $this->assertSame('http://127.0.0.1:8000/master-asset/'.$asset->id.'/foto-tanda-terima', $receipt->foto_url);
        $response = (new AssetController)->fotoTandaTerima($asset);
        $this->assertSame('image/png', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('inline;', $response->headers->get('Content-Disposition'));
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $this->assertSame(Storage::disk('tanda_terima_asset')->get($photo), $content);
    }

    public function test_missing_receipt_photo_returns_clear_not_found_message(): void
    {
        Storage::fake('tanda_terima_asset');
        $asset = Asset::create($this->payload());
        foreach ([null, 'missing.jpg'] as $photo) {
            $asset->tandaTerima()->first()->update(['foto' => $photo]);
            $asset->unsetRelation('tandaTerima');
            try {
                (new AssetController)->fotoTandaTerima($asset);
                $this->fail('Expected missing photo to return 404.');
            } catch (HttpException $exception) {
                $this->assertSame(404, $exception->getStatusCode());
                $this->assertStringContainsString('unggah kembali', $exception->getMessage());
            }
        }
    }
}
