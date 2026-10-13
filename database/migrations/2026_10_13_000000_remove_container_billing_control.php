<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            DB::transaction(function () {
                $permissionIds = DB::table('permissions')
                    ->where('name', 'like', 'container-billing-%')
                    ->pluck('id');

                foreach (['user_permissions', 'permission_role', 'role_has_permissions', 'model_has_permissions'] as $table) {
                    if (Schema::hasTable($table) && Schema::hasColumn($table, 'permission_id')) {
                        DB::table($table)->whereIn('permission_id', $permissionIds)->delete();
                    }
                }

                DB::table('permissions')->whereIn('id', $permissionIds)->delete();
            });
        }

        Schema::dropIfExists('container_billing_records');
        Schema::dropIfExists('container_billing_revisions');
    }

    public function down(): void
    {
        // Retired module: deleted billing data and access grants cannot be restored.
    }
};
