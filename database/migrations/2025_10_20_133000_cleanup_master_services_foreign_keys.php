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
        // This migration specifically handles the foreign key constraint error
        // It must run BEFORE the drop_service_id_from_tables migration

        // Step 1: Drop the specific foreign key constraint that's causing the error
        try {
            DB::statement('ALTER TABLE pricelist_gate_ins DROP FOREIGN KEY pricelist_gate_ins_service_id_foreign');
            echo "Dropped foreign key: pricelist_gate_ins_service_id_foreign\n";
        } catch (Exception $e) {
            echo "Warning: Could not drop foreign key pricelist_gate_ins_service_id_foreign - " . $e->getMessage() . "\n";
        }

        // Step 2: Find and drop any other foreign keys referencing master_services
        $foreignKeys = DB::select("
            SELECT
                TABLE_NAME,
                CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME = 'master_services'
        ");

        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE {$fk->TABLE_NAME} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                echo "Dropped foreign key: {$fk->CONSTRAINT_NAME} from table {$fk->TABLE_NAME}\n";
            } catch (Exception $e) {
                echo "Warning: Could not drop foreign key {$fk->CONSTRAINT_NAME} - " . $e->getMessage() . "\n";
            }
        }

        // Step 3: Drop service_id column from pricelist_gate_ins if it exists
        if (Schema::hasColumn('pricelist_gate_ins', 'service_id')) {
            Schema::table('pricelist_gate_ins', function (Blueprint $table) {
                $table->dropColumn('service_id');
            });
            echo "Dropped service_id column from pricelist_gate_ins\n";
        }

        echo "Foreign key constraint cleanup completed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back service_id column to pricelist_gate_ins (but don't restore foreign key)
        if (!Schema::hasColumn('pricelist_gate_ins', 'service_id')) {
            Schema::table('pricelist_gate_ins', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('id');
            });
        }
    }
};
