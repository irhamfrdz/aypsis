<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterPenerimaBuktiPotongPphController;
use App\Http\Controllers\UserController;
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

    public function test_permission_migration_grants_existing_admins_without_duplicates(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('role_id');
        });
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id');
            $table->integer('role_id');
            $table->timestamps();
            $table->unique(['permission_id', 'role_id']);
        });
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->timestamps();
            $table->unique(['user_id', 'permission_id']);
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'staff'],
        ]);
        DB::table('role_user')->insert([
            ['user_id' => 7, 'role_id' => 1],
            ['user_id' => 8, 'role_id' => 2],
        ]);

        $migration = require database_path('migrations/2026_10_17_000007_ensure_master_penerima_bukti_potong_pph_permissions.php');
        $migration->up();
        $migration->up();

        $this->assertSame(4, DB::table('permissions')->where('name', 'like', 'master-penerima-bukti-potong-pph-%')->count());
        $this->assertSame(4, DB::table('user_permissions')->where('user_id', 7)->count());
        $this->assertSame(0, DB::table('user_permissions')->where('user_id', 8)->count());
        $this->assertSame(4, DB::table('permission_role')->where('role_id', 1)->count());
    }

    public function test_master_user_matrix_converts_all_four_permissions(): void
    {
        $controller = new UserController;
        $names = DB::table('permissions')
            ->where('name', 'like', 'master-penerima-bukti-potong-pph-%')
            ->pluck('name')
            ->all();

        $matrix = $controller->testConvertPermissionsToMatrix($names);
        $this->assertCount(4, $matrix['master-penerima-bukti-potong-pph']);
        foreach (['view', 'create', 'update', 'delete'] as $action) {
            $this->assertTrue($matrix['master-penerima-bukti-potong-pph'][$action]);
        }

        $input = ['master-penerima-bukti-potong-pph' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
        ]];
        $ids = $controller->testConvertMatrixPermissionsToIds($input);

        $this->assertEqualsCanonicalizing(
            DB::table('permissions')->whereIn('name', $names)->pluck('id')->all(),
            array_unique($ids)
        );
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
