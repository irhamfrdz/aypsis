<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'permissions', 'user_permissions'] as $requiredTable) {
            if (! Schema::hasTable($requiredTable)) {
                throw new RuntimeException('Database dasar AYPSIS belum siap: tabel '.$requiredTable.' tidak ditemukan. Hubungkan atau pulihkan database AYPSIS sebelum menjalankan migrasi modul.');
            }
        }

        Schema::create('container_billing_records', function (Blueprint $table) {
            $table->id();
            $table->string('store', 32);
            $table->string('record_key', 64);
            $table->longText('payload');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['store', 'record_key']);
        });
        Schema::create('container_billing_revisions', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedBigInteger('revision')->default(0);
            $table->timestamps();
        });
        DB::table('container_billing_revisions')->insert(['id' => 1, 'revision' => 0, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('permissions')->insertOrIgnore([
            'name' => 'container-billing-manage',
            'description' => 'Mengelola Container Billing Control (Project 03), termasuk restore dan reset data',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $permissionId = DB::table('permissions')->where('name', 'container-billing-manage')->value('id');
        if (DB::table('users')->where('id', 1)->exists()) {
            DB::table('user_permissions')->insertOrIgnore(['user_id' => 1, 'permission_id' => $permissionId]);
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', 'container-billing-manage')->value('id');
        DB::table('user_permissions')->where('permission_id', $permissionId)->delete();
        DB::table('permissions')->where('id', $permissionId)->delete();
        Schema::dropIfExists('container_billing_records');
        Schema::dropIfExists('container_billing_revisions');
    }
};
