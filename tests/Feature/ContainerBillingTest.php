<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ContainerBillingStore;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ContainerBillingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', fn (Blueprint $table) => $table->id());
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->timestamps();
        });
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
        });
        (require database_path('migrations/2026_09_25_100000_create_container_billing_tables.php'))->up();
        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureKaryawanPresent::class,
            \App\Http\Middleware\EnsureUserApproved::class,
            \App\Http\Middleware\EnsureCrewChecklistComplete::class,
        ]);
        Gate::define('container-billing-manage', fn ($user) => $user->username === 'billing-test');
    }

    private function loginBilling(): void
    {
        $user = new User;
        $user->forceFill(['id' => 99, 'username' => 'billing-test']);
        $this->actingAs($user);
    }

    public function test_page_assets_and_api_require_login_and_permission(): void
    {
        $this->getJson('/container-billing/state')->assertUnauthorized();
        $user = new User;
        $user->forceFill(['id' => 98, 'username' => 'no-access']);
        $user->setRelation('roles', collect());
        $this->actingAs($user);
        $this->getJson('/container-billing')->assertForbidden();
        $this->getJson('/container-billing/assets/baseline_data.js')->assertForbidden();
        $this->postJson('/container-billing/state', [])->assertForbidden();
    }

    public function test_original_layout_is_served_with_laravel_endpoints(): void
    {
        $this->loginBilling();
        $page = $this->get('/container-billing')->assertOk()->assertSee('window.CBC_SERVER', false);
        preg_match('/<style>(.*?)<\/style>/s', file_get_contents(base_path('03/index.html')), $original);
        preg_match('/<style>(.*?)<\/style>/s', $page->getContent(), $rendered);
        $this->assertSame($original[1], $rendered[1]);
        $this->get('/container-billing/assets/app.js')->assertOk();
        $this->get('/container-billing/assets/other.js')->assertNotFound();
    }

    public function test_records_preserve_financial_evidence_and_reject_stale_updates(): void
    {
        $this->loginBilling();
        $row = ['id' => 'INV-1', 'amount' => 675676, 'paid' => true, 'issueEvidence' => ['period' => 3, 'raw' => '  ORIGINAL  ', 'empty' => ''], 'adjustment' => -30000];
        $this->postJson('/container-billing/state', ['revision' => 0, 'operation' => 'put', 'store' => 'invoices', 'rows' => [$row]])
            ->assertOk()->assertJsonPath('revision', 1);
        $this->postJson('/container-billing/state', ['revision' => 0, 'operation' => 'clear', 'store' => 'invoices'])->assertConflict();
        $this->getJson('/container-billing/state')->assertOk()->assertJsonPath('data.invoices.0', $row)->assertJsonPath('revision', 1);
        $this->assertSame(99, DB::table('container_billing_records')->value('updated_by'));
    }

    public function test_restore_validates_every_collection_before_replacing_data(): void
    {
        $this->loginBilling();
        $snapshot = array_fill_keys(ContainerBillingStore::STORES, []);
        $snapshot['masters'] = [['id' => 'ABCD1234567', 'vendor' => 'ZONA']];
        $this->postJson('/container-billing/state', ['revision' => 0, 'operation' => 'replace', 'data' => $snapshot])->assertOk();
        $invalid = $snapshot;
        $invalid['invoices'] = [['amount' => 10]];
        $this->postJson('/container-billing/state', ['revision' => 1, 'operation' => 'replace', 'data' => $invalid])->assertUnprocessable();
        $this->getJson('/container-billing/state')->assertJsonPath('revision', 1)->assertJsonPath('data.masters.0.vendor', 'ZONA');
        $this->postJson('/container-billing/state', ['revision' => 1, 'operation' => 'put', 'store' => 'users', 'rows' => [['id' => 1]]])->assertUnprocessable();
        $this->postJson('/container-billing/state', ['revision' => 1, 'operation' => 'delete', 'store' => 'masters', 'id' => 'ABCD1234567'])->assertOk();
        $this->getJson('/container-billing/state')->assertJsonPath('data.masters', []);
    }

    public function test_numeric_and_string_ids_remain_distinct_and_batch_upserts_update(): void
    {
        $store = app(ContainerBillingStore::class);
        $store->mutate(['revision' => 0, 'operation' => 'put', 'store' => 'settings', 'rows' => [['id' => 1, 'value' => 'a'], ['id' => '1', 'value' => 'b']]], 99);
        $store->mutate(['revision' => 1, 'operation' => 'put', 'store' => 'settings', 'rows' => [['id' => 1, 'value' => 'c']]], 99);
        $rows = $store->snapshot()['data']['settings'];
        $this->assertCount(2, $rows);
        $this->assertSame('c', $rows[0]['value']);
        $this->assertSame('b', $rows[1]['value']);
    }

    public function test_migration_refuses_an_empty_aypsis_database_before_creating_module_tables(): void
    {
        $migration = require database_path('migrations/2026_09_25_100000_create_container_billing_tables.php');
        $migration->down();
        Schema::drop('users');
        try {
            $migration->up();
            $this->fail('An empty AYPSIS database must be rejected.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Database dasar AYPSIS belum siap', $exception->getMessage());
        }
        $this->assertFalse(Schema::hasTable('container_billing_records'));
        $this->assertFalse(Schema::hasTable('container_billing_revisions'));
    }

    public function test_billing_permission_round_trips_through_user_edit_matrix(): void
    {
        $controller = app(\App\Http\Controllers\UserController::class);
        $permissionId = DB::table('permissions')->where('name', 'container-billing-manage')->value('id');
        $matrix = $controller->testConvertPermissionsToMatrix(['container-billing-manage']);
        $this->assertSame(['container-billing' => ['view' => true]], $matrix);
        $this->assertEquals([$permissionId], $controller->testConvertMatrixPermissionsToIds($matrix));
        $this->assertEquals([$permissionId], $controller->testConvertMatrixPermissionsToIds(['container-billing' => ['view' => '1']]));
        $this->assertSame([], $controller->testConvertMatrixPermissionsToIds(['container-billing' => ['view' => '0']]));
        $this->assertSame([], $controller->testConvertMatrixPermissionsToIds([]));
    }

    public function test_permission_repair_migration_is_repeatable_and_preserves_grants(): void
    {
        $migration = require database_path('migrations/2026_09_25_110000_ensure_container_billing_manage_permission.php');
        $id = DB::table('permissions')->where('name', 'container-billing-manage')->value('id');
        DB::table('user_permissions')->insert(['user_id' => 99, 'permission_id' => $id]);
        $migration->up();
        $migration->up();
        $migration->down();
        $this->assertSame(1, DB::table('permissions')->where('name', 'container-billing-manage')->count());
        $this->assertTrue(DB::table('user_permissions')->where('user_id', 99)->where('permission_id', $id)->exists());
        DB::table('user_permissions')->delete();
        DB::table('permissions')->where('id', $id)->delete();
        $migration->up();
        $this->assertSame(1, DB::table('permissions')->where('name', 'container-billing-manage')->count());
    }
}
