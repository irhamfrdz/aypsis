<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterPenerimaBuktiPotongPphController;
use App\Http\Middleware\EnsureCrewChecklistComplete;
use App\Http\Middleware\EnsureKaryawanPresent;
use App\Http\Middleware\EnsureUserApproved;
use App\Models\PenerimaBuktiPotongPph;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PenerimaBuktiPotongPphTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
        ]);
        DB::purge('sqlite');

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        $migration = require database_path('migrations/2026_10_17_000006_create_master_penerima_bukti_potong_pph_table.php');
        $migration->up();
    }

    public function test_migration_registers_table_and_permissions(): void
    {
        $this->assertTrue(Schema::hasTable('master_penerima_bukti_potong_pph'));
        $this->assertSame(4, DB::table('permissions')->where('name', 'like', 'master-penerima-bukti-potong-pph-%')->count());
    }

    public function test_create_update_and_delete_recipient(): void
    {
        $controller = new MasterPenerimaBuktiPotongPphController;

        $controller->store(Request::create('/', 'POST', [
            'nama' => 'PT Contoh',
            'npwp' => '1234567890123456',
            'alamat' => 'Jakarta',
            'is_active' => '1',
        ]));

        $penerima = PenerimaBuktiPotongPph::firstOrFail();
        $this->assertSame('PT Contoh', $penerima->nama);
        $this->assertTrue($penerima->is_active);

        $controller->update(Request::create('/', 'PUT', [
            'nama' => 'PT Contoh Baru',
            'nik' => '1234567890123456',
            'is_active' => '0',
        ]), $penerima);

        $this->assertSame('PT Contoh Baru', $penerima->fresh()->nama);
        $this->assertFalse($penerima->fresh()->is_active);

        $controller->destroy($penerima);
        $this->assertSoftDeleted('master_penerima_bukti_potong_pph', ['id' => $penerima->id]);
    }

    public function test_update_route_binds_the_correct_recipient(): void
    {
        $this->withoutMiddleware([
            Authenticate::class,
            EnsureKaryawanPresent::class,
            EnsureUserApproved::class,
            EnsureCrewChecklistComplete::class,
            Authorize::class,
        ]);

        $penerima = PenerimaBuktiPotongPph::create(['nama' => 'Penerima Awal', 'is_active' => true]);

        $this->put(route('master.penerima-bukti-potong-pph.update', $penerima), [
            'nama' => 'Penerima Baru',
            'is_active' => '0',
        ])->assertRedirect(route('master.penerima-bukti-potong-pph.index'));

        $this->assertSame('Penerima Baru', $penerima->fresh()->nama);
        $this->assertFalse($penerima->fresh()->is_active);
    }
}
