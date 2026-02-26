<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('sertifikat_kapals');

        $permissions = [
            'master-sertifikat-kapal-view',
            'master-sertifikat-kapal-create',
            'master-sertifikat-kapal-update',
            'master-sertifikat-kapal-delete',
        ];

        DB::table('permission_role')->whereIn('permission_id', function($query) use ($permissions) {
            $query->select('id')->from('permissions')->whereIn('name', $permissions);
        })->delete();

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }

    public function down(): void
    {
        Schema::create('sertifikat_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sertifikat');
            $table->text('keterangan')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }
};
