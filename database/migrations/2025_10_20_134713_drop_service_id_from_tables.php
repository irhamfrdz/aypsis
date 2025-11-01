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
        // Step 1: Drop foreign key dan kolom service_id dari tabel pricelist_gate_ins FIRST
        if (Schema::hasTable('pricelist_gate_ins') && Schema::hasColumn('pricelist_gate_ins', 'service_id')) {
            Schema::table('pricelist_gate_ins', function (Blueprint $table) {
                try {
                    $table->dropForeign(['service_id']);
                } catch (Exception $e) {
                    // Foreign key might not exist or have different name, continue
                }
                $table->dropColumn('service_id');
            });
        }

        // Step 2: Drop foreign key dan kolom service_id dari tabel gate_ins
        if (Schema::hasTable('gate_ins') && Schema::hasColumn('gate_ins', 'service_id')) {
            // Drop foreign key constraints that might exist
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            Schema::table('gate_ins', function (Blueprint $table) {
                $table->dropColumn('service_id');
            });
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Step 3: Drop foreign key dan kolom service_id dari tabel kontainers
        if (Schema::hasTable('kontainers') && Schema::hasColumn('kontainers', 'service_id')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            Schema::table('kontainers', function (Blueprint $table) {
                $table->dropColumn('service_id');
            });
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Step 4: Drop any other tables that might reference master_services
        $tablesToCheck = ['pranota_kontainer_sewas', 'tagihan_kontainer_sewas', 'daftar_tagihan_kontainer_sewas'];

        foreach ($tablesToCheck as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'service_id')) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('service_id');
                });
                
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        // Step 5: Now safely drop tabel master_services
        Schema::dropIfExists('master_services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate master_services table
        if (!Schema::hasTable('master_services')) {
            Schema::create('master_services', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique()->comment('Kode service unik');
                $table->string('nama_service')->comment('Nama service');
                $table->text('deskripsi')->nullable()->comment('Deskripsi service');
                $table->string('status')->default('aktif')->comment('Status aktif/non-aktif');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Add service_id back to pricelist_gate_ins (if table exists)
        if (Schema::hasTable('pricelist_gate_ins') && !Schema::hasColumn('pricelist_gate_ins', 'service_id')) {
            Schema::table('pricelist_gate_ins', function (Blueprint $table) {
                $table->foreignId('service_id')->nullable()->after('id')->constrained('master_services')->onDelete('set null');
            });
        }

        // Add service_id back to gate_ins
        if (Schema::hasTable('gate_ins') && !Schema::hasColumn('gate_ins', 'service_id')) {
            Schema::table('gate_ins', function (Blueprint $table) {
                $table->foreignId('service_id')->nullable()->after('id')->constrained('master_services')->onDelete('set null');
            });
        }

        // Add service_id back to kontainers
        if (Schema::hasTable('kontainers') && !Schema::hasColumn('kontainers', 'service_id')) {
            Schema::table('kontainers', function (Blueprint $table) {
                $table->foreignId('service_id')->nullable()->after('id')->constrained('master_services')->onDelete('set null');
            });
        }
    }
};
