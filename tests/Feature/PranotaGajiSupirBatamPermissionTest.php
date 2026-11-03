<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use App\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaGajiSupirBatamPermissionTest extends TestCase
{
    public function test_permission_migration_and_edit_matrix_round_trip(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
        });
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['user_id', 'permission_id']);
            $table->timestamps();
        });
        $adminId = DB::table('users')->insertGetId(['username' => 'admin']);
        $migration = require database_path('migrations/2026_11_03_100001_add_pranota_gaji_supir_batam_permissions.php');
        $migration->up();
        $migration->up();

        $names = [
            'pranota-gaji-supir-batam-view',
            'pranota-gaji-supir-batam-create',
            'pranota-gaji-supir-batam-delete',
        ];
        $this->assertSame(3, Permission::whereIn('name', $names)->count());
        $this->assertSame(3, DB::table('user_permissions')->where('user_id', $adminId)->count());

        $controller = new UserController;
        $save = new \ReflectionMethod($controller, 'convertMatrixPermissionsToIds');
        $load = new \ReflectionMethod($controller, 'convertPermissionsToMatrix');
        $ids = $save->invoke($controller, ['pranota-gaji-supir-batam' => [
            'view' => '1', 'create' => '1', 'delete' => '1',
        ]]);
        $this->assertEqualsCanonicalizing($names, Permission::whereIn('id', $ids)->pluck('name')->all());
        $this->assertSame(['pranota-gaji-supir-batam' => [
            'view' => true, 'create' => true, 'delete' => true,
        ]], $load->invoke($controller, $names));
    }
}
