<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TagihanLoloBatamMasterUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('password')->default('secret');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id');
            $table->foreignId('user_id');
            $table->timestamps();
        });

        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'secret',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $perms = [
            'tagihan-lolo-batam-view' => 'Melihat Daftar Tagihan LOLO Batam',
            'tagihan-lolo-batam-create' => 'Membuat Tagihan LOLO Batam',
            'tagihan-lolo-batam-update' => 'Mengubah Tagihan LOLO Batam',
            'tagihan-lolo-batam-delete' => 'Menghapus Tagihan LOLO Batam',
            'tagihan-lolo-batam-approve' => 'Menyetujui Tagihan LOLO Batam',
            'tagihan-lolo-batam-print' => 'Mencetak Tagihan LOLO Batam',
            'tagihan-lolo-batam-export' => 'Mengekspor Tagihan LOLO Batam',
        ];

        foreach ($perms as $name => $desc) {
            DB::table('permissions')->insert([
                'name' => $name,
                'description' => $desc,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_tagihan_lolo_batam_permissions_exist()
    {
        $permissions = [
            'tagihan-lolo-batam-view',
            'tagihan-lolo-batam-create',
            'tagihan-lolo-batam-update',
            'tagihan-lolo-batam-delete',
            'tagihan-lolo-batam-approve',
            'tagihan-lolo-batam-print',
            'tagihan-lolo-batam-export',
        ];

        foreach ($permissions as $perm) {
            $this->assertDatabaseHas('permissions', ['name' => $perm]);
        }
    }

    public function test_user_controller_matrix_conversion()
    {
        $controller = new UserController;

        $matrixInput = [
            'tagihan-lolo-batam' => [
                'view' => '1',
                'create' => '1',
                'update' => '1',
                'delete' => '1',
                'approve' => '1',
                'print' => '1',
                'export' => '1',
            ],
        ];

        $ids = $controller->testConvertMatrixPermissionsToIds($matrixInput);
        $this->assertNotEmpty($ids);
        $this->assertCount(7, $ids);

        $permissionNames = [
            'tagihan-lolo-batam-view',
            'tagihan-lolo-batam-create',
        ];

        $matrix = $controller->testConvertPermissionsToMatrix($permissionNames);
        $this->assertArrayHasKey('tagihan-lolo-batam', $matrix);
        $this->assertTrue($matrix['tagihan-lolo-batam']['view']);
        $this->assertTrue($matrix['tagihan-lolo-batam']['create']);
    }

    public function test_master_user_views_contain_tagihan_lolo_batam()
    {
        $user = User::first();

        $viewCreate = view('master-user.create', [
            'karyawans' => collect([]),
            'karyawanTidakTetaps' => collect([]),
            'permissions' => Permission::all(),
            'users' => collect([]),
            'userPermissions' => [],
            'userMatrixPermissions' => [],
        ])->render();

        $this->assertStringContainsString('permissions[tagihan-lolo-batam][view]', $viewCreate);
        $this->assertStringContainsString('Pranota LOLO Batam', $viewCreate);

        $viewEdit = view('master-user.edit', [
            'user' => $user,
            'karyawans' => collect([]),
            'karyawanTidakTetaps' => collect([]),
            'permissions' => Permission::all(),
            'users' => collect([]),
            'userPermissions' => [],
            'userSimplePermissions' => [],
            'userMatrixPermissions' => [],
            'templates' => config('permission_templates', []),
        ])->render();

        $this->assertStringContainsString('permissions[tagihan-lolo-batam][view]', $viewEdit);
        $this->assertStringContainsString('Pranota LOLO Batam', $viewEdit);
    }
}
