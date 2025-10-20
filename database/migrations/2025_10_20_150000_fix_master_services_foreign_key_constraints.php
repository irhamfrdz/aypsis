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
        // Step 1: Drop foreign key constraints first
        try {
            // Check if foreign key constraint exists and drop it
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'pricelist_gate_ins' 
                AND REFERENCED_TABLE_NAME = 'master_services'
            ");

            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE pricelist_gate_ins DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                echo "Dropped foreign key: {$fk->CONSTRAINT_NAME}\n";
            }

            // Also check for other tables that might reference master_services
            $allForeignKeys = DB::select("
                SELECT TABLE_NAME, CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND REFERENCED_TABLE_NAME = 'master_services'
            ");

            foreach ($allForeignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE {$fk->TABLE_NAME} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                    echo "Dropped foreign key: {$fk->CONSTRAINT_NAME} from table {$fk->TABLE_NAME}\n";
                } catch (Exception $e) {
                    echo "Warning: Could not drop foreign key {$fk->CONSTRAINT_NAME}: " . $e->getMessage() . "\n";
                }
            }

        } catch (Exception $e) {
            echo "Warning during foreign key cleanup: " . $e->getMessage() . "\n";
        }

        // Step 2: Drop service_id column from pricelist_gate_ins if it exists
        if (Schema::hasColumn('pricelist_gate_ins', 'service_id')) {
            Schema::table('pricelist_gate_ins', function (Blueprint $table) {
                $table->dropColumn('service_id');
            });
            echo "Dropped service_id column from pricelist_gate_ins\n";
        }

        // Step 3: Drop other service_id columns from other tables
        $tablesToClean = ['gate_ins', 'kontainers'];
        
        foreach ($tablesToClean as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'service_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('service_id');
                });
                echo "Dropped service_id column from {$tableName}\n";
            }
        }

        // Step 4: Now safely drop master_services table
        if (Schema::hasTable('master_services')) {
            Schema::dropIfExists('master_services');
            echo "Dropped master_services table\n";
        }
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
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
                $table->timestamps();
            });
        }

        // Add back service_id columns (but don't restore foreign keys as they're not needed)
        if (!Schema::hasColumn('pricelist_gate_ins', 'service_id')) {
            Schema::table('pricelist_gate_ins', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('gate_ins') && !Schema::hasColumn('gate_ins', 'service_id')) {
            Schema::table('gate_ins', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('kontainers') && !Schema::hasColumn('kontainers', 'service_id')) {
            Schema::table('kontainers', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('id');
            });
        }
    }
};