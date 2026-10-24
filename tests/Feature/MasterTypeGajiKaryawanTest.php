<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterTypeGajiKaryawanController;
use App\Http\Controllers\UserController;
use App\Models\Karyawan;
use App\Models\MasterTypeGajiKaryawan;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MasterTypeGajiKaryawanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_10_24_100000_create_type_gaji_karyawan_table.php'))->up();
        (require database_path('migrations/2026_10_24_100002_add_type_gaji_id_to_type_gaji_karyawan_table.php'))->up();
        (require database_path('migrations/2025_09_30_141213_create_nomor_terakhir_table.php'))->up();
        (require database_path('migrations/2026_10_24_100003_initialize_automatic_type_gaji_ids.php'))->up();
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description');
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
        });
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->unique(['role_id', 'permission_id']);
        });
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->unique(['user_id', 'permission_id']);
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('auditable_id');
            foreach (['user_name', 'auditable_type', 'action', 'module', 'description', 'ip_address', 'user_agent', 'url'] as $column) {
                $table->string($column)->nullable();
            }
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->nullable();
        });
        DB::table('roles')->insert(['id' => 1, 'name' => 'admin']);
        DB::table('role_user')->insert(['role_id' => 1, 'user_id' => 1]);
        (require database_path('migrations/2026_10_24_100001_add_type_gaji_karyawan_permissions.php'))->up();
        $user = new User(['username' => 'type-gaji-test', 'karyawan_id' => 1, 'status' => 'approved']);
        $user->id = 1;
        $user->setRelation('karyawan', null);
        $user->setRelation('roles', collect());
        $user->setRelation('permissions', Permission::all());
        $this->actingAs($user);
        foreach (['view', 'create', 'update', 'delete'] as $action) {
            Gate::define('master-type-gaji-karyawan-'.$action, fn () => true);
        }
    }

    public function test_crud_saves_validated_data_and_audits_changes(): void
    {
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => 'Bulanan', 'keterangan' => 'Gaji setiap bulan', 'status' => 'active'])
            ->assertRedirect(route('master.type-gaji-karyawan.index'))->assertSessionHas('success');
        $type = MasterTypeGajiKaryawan::firstOrFail();
        $this->assertSame(1, $type->type_gaji_id);
        $this->putJson(route('master.type-gaji-karyawan.update', $type), ['nama' => 'Harian', 'keterangan' => 'Gaji per hari', 'status' => 'inactive'])
            ->assertRedirect(route('master.type-gaji-karyawan.index'));
        $this->assertDatabaseHas('type_gaji_karyawan', ['id' => $type->id, 'type_gaji_id' => 1, 'nama' => 'Harian', 'status' => 'inactive']);
        $this->deleteJson(route('master.type-gaji-karyawan.destroy', $type))->assertRedirect(route('master.type-gaji-karyawan.index'));
        $this->assertDatabaseMissing('type_gaji_karyawan', ['id' => $type->id]);
        $this->assertSame(['created', 'updated', 'deleted'], DB::table('audit_logs')->orderBy('id')->pluck('action')->all());
    }

    public function test_duplicate_names_and_invalid_status_are_rejected_but_own_name_can_be_retained(): void
    {
        $type = MasterTypeGajiKaryawan::create(['nama' => 'Bulanan', 'status' => 'active']);
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => 'Bulanan', 'status' => 'active'])->assertUnprocessable()->assertJsonValidationErrors('nama');
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => ' ', 'status' => 'invalid'])->assertUnprocessable()->assertJsonValidationErrors(['nama', 'status']);
        $this->putJson(route('master.type-gaji-karyawan.update', $type), ['nama' => 'Bulanan', 'status' => 'inactive'])->assertRedirect();
        $this->assertSame(1, MasterTypeGajiKaryawan::count());
    }

    public function test_search_and_status_filters_keep_pagination(): void
    {
        MasterTypeGajiKaryawan::create(['type_gaji_id' => 12, 'nama' => 'Bulanan', 'keterangan' => 'Karyawan tetap', 'status' => 'active']);
        MasterTypeGajiKaryawan::create(['nama' => 'Harian', 'status' => 'inactive']);
        $view = (new MasterTypeGajiKaryawanController)->index(Request::create('/', 'GET', ['search' => 'tetap', 'status' => 'active']));
        $types = $view->getData()['types'];
        $this->assertSame(1, $types->total());
        $this->assertSame('Bulanan', $types->first()->nama);
        $prefixed = (new MasterTypeGajiKaryawanController)->index(Request::create('/', 'GET', ['search' => '0012']))->getData()['types'];
        $this->assertSame(1, $prefixed->total());
        $this->assertSame('Bulanan', $prefixed->first()->nama);
    }

    public function test_each_mutation_requires_its_own_permission(): void
    {
        $type = MasterTypeGajiKaryawan::create(['nama' => 'Bulanan', 'status' => 'active']);
        foreach (['create', 'update', 'delete'] as $action) {
            Gate::define('master-type-gaji-karyawan-'.$action, fn () => false);
        }
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => 'Harian', 'status' => 'active'])->assertForbidden();
        $this->putJson(route('master.type-gaji-karyawan.update', $type), ['nama' => 'Harian', 'status' => 'inactive'])->assertForbidden();
        $this->deleteJson(route('master.type-gaji-karyawan.destroy', $type))->assertForbidden();
        Gate::define('master-type-gaji-karyawan-view', fn () => false);
        $this->getJson(route('master.type-gaji-karyawan.index'))->assertForbidden();
        $this->assertDatabaseHas('type_gaji_karyawan', ['id' => $type->id, 'nama' => 'Bulanan']);
    }

    public function test_permissions_round_trip_in_user_matrix_and_migration_is_idempotent(): void
    {
        (require database_path('migrations/2026_10_24_100001_add_type_gaji_karyawan_permissions.php'))->up();
        $this->assertSame(4, Permission::count());
        $this->assertSame(4, DB::table('permission_role')->count());
        $this->assertSame(4, DB::table('user_permissions')->count());
        $controller = new UserController;
        $matrix = $controller->testConvertPermissionsToMatrix(Permission::pluck('name')->all());
        $this->assertEquals(['view' => true, 'create' => true, 'update' => true, 'delete' => true], $matrix['master-type-gaji-karyawan']);
        $ids = $controller->testConvertMatrixPermissionsToIds(['master-type-gaji-karyawan' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1]]);
        sort($ids);
        $this->assertSame(Permission::orderBy('id')->pluck('id')->all(), $ids);
    }

    public function test_master_directory_contains_menu_for_authorized_user(): void
    {
        $html = view('master.data._directory', ['user' => auth()->user(), 'isAdmin' => false])->render();
        $this->assertStringContainsString(route('master.type-gaji-karyawan.index'), $html);
        $this->assertStringContainsString('Type Gaji Karyawan', $html);
    }

    public function test_ids_are_automatic_and_not_reused_after_deletion(): void
    {
        $this->postJson(route('master.type-gaji-karyawan.store'), ['type_gaji_id' => 999, 'nama' => 'Bulanan', 'status' => 'active'])->assertUnprocessable();
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => 'Bulanan', 'status' => 'active'])->assertRedirect();
        $type = MasterTypeGajiKaryawan::firstOrFail();
        $this->assertSame(1, $type->type_gaji_id);
        $this->deleteJson(route('master.type-gaji-karyawan.destroy', $type))->assertRedirect();
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => 'Harian', 'status' => 'active'])->assertRedirect();
        $this->assertSame(2, MasterTypeGajiKaryawan::firstOrFail()->type_gaji_id);
    }

    public function test_only_employee_nik_1593_can_change_an_existing_id(): void
    {
        $type = MasterTypeGajiKaryawan::create(['type_gaji_id' => 10, 'nama' => 'Bulanan', 'status' => 'active']);
        $url = route('master.type-gaji-karyawan.update', $type);
        $this->putJson($url, ['type_gaji_id' => 20, 'nama' => 'Bulanan', 'status' => 'active'])->assertForbidden();
        // A username or request payload cannot impersonate the employee NIK.
        auth()->user()->username = 'kiky';
        auth()->user()->setRelation('karyawan', new Karyawan(['nik' => '1004']));
        $this->putJson($url, ['type_gaji_id' => 20, 'nik' => '1593', 'nama' => 'Bulanan', 'status' => 'active'])->assertForbidden();
        $this->putJson($url, ['type_gaji_id' => 10, 'nama' => 'Bulanan', 'status' => 'inactive'])->assertRedirect();
        $this->assertSame(10, $type->fresh()->type_gaji_id);

        auth()->user()->setRelation('karyawan', new Karyawan(['nik' => '1593']));
        $this->putJson($url, ['type_gaji_id' => 200, 'nama' => 'Bulanan', 'status' => 'active'])->assertRedirect();
        $this->assertSame(200, $type->fresh()->type_gaji_id);
        foreach ([-1, 'abc', 1.5, null] as $invalidId) {
            $this->putJson($url, ['type_gaji_id' => $invalidId, 'nama' => 'Bulanan', 'status' => 'active'])->assertUnprocessable()->assertJsonValidationErrors('type_gaji_id');
        }
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => 'Harian', 'status' => 'active'])->assertRedirect();
        $this->assertSame(201, MasterTypeGajiKaryawan::where('nama', 'Harian')->firstOrFail()->type_gaji_id);
        $this->putJson($url, ['type_gaji_id' => 201, 'nama' => 'Bulanan', 'status' => 'active'])->assertUnprocessable();
    }

    public function test_initialization_fills_missing_ids_and_preserves_existing_ids(): void
    {
        DB::table('type_gaji_karyawan')->insert([
            ['nama' => 'Bulanan', 'type_gaji_id' => 50, 'status' => 'active'],
            ['nama' => 'Harian', 'type_gaji_id' => null, 'status' => 'active'],
        ]);
        $migration = require database_path('migrations/2026_10_24_100003_initialize_automatic_type_gaji_ids.php');
        $migration->up();
        $migration->up();
        $this->assertSame(50, MasterTypeGajiKaryawan::where('nama', 'Bulanan')->firstOrFail()->type_gaji_id);
        $this->assertSame(51, MasterTypeGajiKaryawan::where('nama', 'Harian')->firstOrFail()->type_gaji_id);
        $this->postJson(route('master.type-gaji-karyawan.store'), ['nama' => 'Borongan', 'status' => 'active'])->assertRedirect();
        $this->assertSame(52, MasterTypeGajiKaryawan::where('nama', 'Borongan')->firstOrFail()->type_gaji_id);
    }

    public function test_form_only_exposes_editable_id_to_employee_nik_1593(): void
    {
        $type = MasterTypeGajiKaryawan::create(['type_gaji_id' => 10, 'nama' => 'Bulanan', 'status' => 'active']);
        $data = ['typeGajiKaryawan' => $type, 'errors' => new \Illuminate\Support\ViewErrorBag];
        $locked = view('master-type-gaji-karyawan._form', $data)->render();
        $this->assertStringContainsString('readonly', $locked);
        $this->assertStringContainsString('value="0010"', $locked);
        $this->assertStringNotContainsString('name="type_gaji_id"', $locked);
        auth()->user()->setRelation('karyawan', new Karyawan(['nik' => '1593']));
        $editable = view('master-type-gaji-karyawan._form', $data)->render();
        $this->assertStringContainsString('name="type_gaji_id"', $editable);
        $this->assertStringContainsString('value="10"', $editable);
        $this->assertStringContainsString('>00</span>', $editable);
        $this->assertStringNotContainsString('readonly', $editable);
        $create = view('master-type-gaji-karyawan._form', array_merge($data, ['typeGajiKaryawan' => null]))->render();
        $this->assertStringContainsString('Dibuat otomatis saat disimpan', $create);
        $this->assertStringNotContainsString('name="type_gaji_id"', $create);
    }
}
