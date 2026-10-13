<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RemoveContainerBillingTest extends TestCase
{
    public function test_removal_deletes_module_data_and_grants_and_preserves_other_modules(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        DB::table('permissions')->insert([
            ['id' => 1, 'name' => 'container-billing-manage'],
            ['id' => 2, 'name' => 'master-divisi-view'],
            ['id' => 3, 'name' => 'container-billing-view'],
        ]);
        foreach (['user_permissions', 'permission_role', 'role_has_permissions', 'model_has_permissions'] as $table) {
            Schema::create($table, function (Blueprint $blueprint) {
                $blueprint->unsignedBigInteger('permission_id');
                $blueprint->foreign('permission_id')->references('id')->on('permissions');
            });
            DB::table($table)->insert([['permission_id' => 1], ['permission_id' => 2], ['permission_id' => 3]]);
        }
        foreach (['container_billing_records', 'container_billing_revisions', 'kontainers'] as $table) {
            Schema::create($table, fn (Blueprint $blueprint) => $blueprint->id());
            DB::table($table)->insert(['id' => 1]);
        }

        $migration = require database_path('migrations/2026_10_13_000000_remove_container_billing_control.php');
        $migration->up();
        $migration->up();

        $this->assertFalse(Schema::hasTable('container_billing_records'));
        $this->assertFalse(Schema::hasTable('container_billing_revisions'));
        $this->assertSame(['master-divisi-view'], DB::table('permissions')->pluck('name')->all());
        foreach (['user_permissions', 'permission_role', 'role_has_permissions', 'model_has_permissions'] as $table) {
            $this->assertSame([2], DB::table($table)->pluck('permission_id')->all());
        }
        $this->assertSame(1, DB::table('kontainers')->count());
    }

    public function test_removal_handles_databases_without_module_or_permission_tables(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        $migration = require database_path('migrations/2026_10_13_000000_remove_container_billing_control.php');
        $migration->up();

        $this->assertFalse(Schema::hasTable('container_billing_records'));
        $this->assertFalse(Schema::hasTable('container_billing_revisions'));
        $billingRoutes = array_filter(
            app('router')->getRoutes()->getRoutes(),
            fn ($route) => str_starts_with($route->uri(), 'container-billing')
        );
        $this->assertSame([], array_values($billingRoutes));
    }
}
